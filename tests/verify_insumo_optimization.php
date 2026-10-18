<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Database;
use App\Models\BaseModel;
use App\Models\Insumo;

class MockPDOStatement extends PDOStatement
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    #[\ReturnTypeWillChange]
    public function execute($params = null): bool
    {
        $this->pdo->lastParams = $params ?? [];
        return true;
    }

    #[\ReturnTypeWillChange]
    public function fetchAll(...$args): array
    {
        return [];
    }

    #[\ReturnTypeWillChange]
    public function fetch(...$args)
    {
        return false;
    }
}

class TrackingPDO extends PDO
{
    public string $lastSql = '';
    public array $lastParams = [];

    public function __construct()
    {
        parent::__construct('sqlite::memory:');
    }

    #[\ReturnTypeWillChange]
    public function prepare($query, $options = [])
    {
        $this->lastSql = $query;
        return new MockPDOStatement($this);
    }
}

function verify_insumo_optimization()
{
    $trackingPdo = new TrackingPDO();

    $dbRef = new ReflectionClass(Database::class);
    $mockDb = $dbRef->newInstanceWithoutConstructor();

    $connProp = $dbRef->getProperty('connection');
    $connProp->setAccessible(true);
    $connProp->setValue($mockDb, $trackingPdo);

    $insumoRef = new ReflectionClass(Insumo::class);
    $insumoModel = $insumoRef->newInstanceWithoutConstructor();

    $baseDbProp = new ReflectionProperty(BaseModel::class, 'db');
    $baseDbProp->setAccessible(true);
    $baseDbProp->setValue($insumoModel, $mockDb);

    echo "--- Testing Insumo::getAllBySucursal(5) ---\n";
    $insumoModel->getAllBySucursal(5);

    $sqlFiltered = $trackingPdo->lastSql;
    $paramsFiltered = $trackingPdo->lastParams;

    echo "Generated SQL:\n$sqlFiltered\n";
    echo "Generated Params: " . json_encode($paramsFiltered) . "\n\n";

    // Isolate SELECT clause before FROM
    $parts = explode('FROM', $sqlFiltered, 2);
    $selectClause = $parts[0];

    if (stripos($selectClause, '(SELECT') !== false) {
        throw new Exception("FAIL: Correlated scalar subquery still found in SELECT clause of getAllBySucursal!");
    }

    if (stripos($sqlFiltered, 'LEFT JOIN (') === false) {
        throw new Exception("FAIL: Derived table JOIN for costo_ultimo missing in getAllBySucursal!");
    }

    if ($paramsFiltered !== [5, 5]) {
        throw new Exception("FAIL: Unexpected parameters for getAllBySucursal. Expected [5, 5], got " . json_encode($paramsFiltered));
    }

    echo "✅ PASS: Insumo::getAllBySucursal(5) uses derived table JOIN without scalar subqueries.\n\n";

    echo "--- Testing Insumo::all() ---\n";
    $insumoModel->all();

    $sqlAll = $trackingPdo->lastSql;
    $partsAll = explode('FROM', $sqlAll, 2);
    $selectAllClause = $partsAll[0];

    if (stripos($selectAllClause, '(SELECT') !== false) {
        throw new Exception("FAIL: Correlated scalar subquery still found in SELECT clause of all()!");
    }

    if (stripos($sqlAll, 'LEFT JOIN (') === false) {
        throw new Exception("FAIL: Derived table JOIN for costo_ultimo missing in all()!");
    }

    echo "✅ PASS: Insumo::all() uses derived table JOIN without scalar subqueries.\n\n";

    echo "--- Testing Insumo::all(5) ---\n";
    $insumoModel->all(5);
    if ($trackingPdo->lastSql !== $sqlFiltered) {
        throw new Exception("FAIL: Insumo::all(5) did not route to getAllBySucursal(5)!");
    }

    echo "✅ PASS: Insumo::all(5) correctly delegates to getAllBySucursal(5).\n\n";

    echo "🎉 ALL INSUMO OPTIMIZATION TESTS PASSED SUCCESSFULLY!\n";
}

try {
    verify_insumo_optimization();
} catch (Exception $e) {
    echo "❌ Verification failed: " . $e->getMessage() . "\n";
    exit(1);
}
