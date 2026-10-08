<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Database;
use App\Models\Insumo;

class CustomStatement extends PDOStatement
{
    public $querySql;
    public static $lastExecutedQuery = null;

    protected function __construct($sql)
    {
        $this->querySql = $sql;
    }

    public function execute(?array $params = null): bool
    {
        self::$lastExecutedQuery = [
            'sql' => $this->querySql,
            'params' => $params
        ];
        return true;
    }

    public function fetchAll(int $mode = PDO::FETCH_DEFAULT, mixed ...$args): array
    {
        return [];
    }

    public function fetch(int $mode = PDO::FETCH_DEFAULT, int $cursorOrientation = PDO::FETCH_ORI_NEXT, int $cursorOffset = 0): mixed
    {
        return false;
    }
}

class QueryLoggerPDO extends PDO
{
    public function __construct()
    {
        parent::__construct('sqlite::memory:');
    }

    public function prepare(string $query, array $options = []): PDOStatement|false
    {
        $stmt = new class($query) extends PDOStatement {
            private $sql;
            public function __construct($sql)
            {
                $this->sql = $sql;
            }
            public function execute(?array $params = null): bool
            {
                CustomStatement::$lastExecutedQuery = [
                    'sql' => $this->sql,
                    'params' => $params
                ];
                return true;
            }
            public function fetchAll(int $mode = PDO::FETCH_DEFAULT, mixed ...$args): array
            {
                return [];
            }
        };
        return $stmt;
    }
}

function verifyOptimization()
{
    $dbRef = new ReflectionClass(Database::class);
    $dbMock = $dbRef->newInstanceWithoutConstructor();

    $pdoLogger = new QueryLoggerPDO();

    $connProp = new ReflectionProperty(Database::class, 'connection');
    $connProp->setAccessible(true);
    $connProp->setValue($dbMock, $pdoLogger);

    $insumoRef = new ReflectionClass(Insumo::class);
    $insumoModel = $insumoRef->newInstanceWithoutConstructor();

    $dbProp = new ReflectionProperty(Insumo::class, 'db');
    $dbProp->setAccessible(true);
    $dbProp->setValue($insumoModel, $dbMock);

    echo "Running Insumo::getAllBySucursal(1)...\n";
    $insumoModel->getAllBySucursal(1);

    $lastQuery = CustomStatement::$lastExecutedQuery;
    $sqlSucursal = $lastQuery['sql'];
    $paramsSucursal = $lastQuery['params'];

    echo "SQL (Sucursal):\n" . $sqlSucursal . "\n";
    echo "Params: " . json_encode($paramsSucursal) . "\n\n";

    // 1. Verify correlated scalar subquery is gone from SELECT clause
    $selectClause = substr($sqlSucursal, 0, stripos($sqlSucursal, 'FROM'));
    if (strpos($selectClause, '(SELECT') !== false) {
        throw new Exception("Optimization failed: Correlated scalar subquery still found in SELECT clause.");
    }

    // 2. Verify LEFT JOIN with derived table is used
    if (strpos($sqlSucursal, 'ultimo_costo') === false) {
        throw new Exception("Optimization failed: Derived table join 'ultimo_costo' not found in query.");
    }

    // 3. Verify parameters
    if ($paramsSucursal !== [1, 1]) {
        throw new Exception("Parameter mismatch in getAllBySucursal: Expected [1, 1], got " . json_encode($paramsSucursal));
    }

    echo "Running Insumo::all()...\n";
    $insumoModel->all();

    $lastQueryAll = CustomStatement::$lastExecutedQuery;
    $sqlAll = $lastQueryAll['sql'];
    $paramsAll = $lastQueryAll['params'];

    echo "SQL (All):\n" . $sqlAll . "\n";
    echo "Params: " . json_encode($paramsAll) . "\n\n";

    $selectAllClause = substr($sqlAll, 0, stripos($sqlAll, 'FROM'));
    if (strpos($selectAllClause, '(SELECT') !== false) {
        throw new Exception("Optimization failed in all(): Correlated scalar subquery still found in SELECT clause.");
    }

    if (strpos($sqlAll, 'ultimo_costo') === false) {
        throw new Exception("Optimization failed in all(): Derived table 'ultimo_costo' not found in query.");
    }

    echo "✅ Insumo model optimization verified successfully!\n";
}

try {
    verifyOptimization();
} catch (Exception $e) {
    echo "❌ Verification failed: " . $e->getMessage() . "\n";
    exit(1);
}
