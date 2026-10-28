<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Models\Insumo;
use App\Core\Database;

class MockPDOStatement extends PDOStatement
{
    public function execute(?array $params = null): bool
    {
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

class QueryCapturePDO extends PDO
{
    public array $queries = [];

    public function prepare(string $query, array $options = []): PDOStatement|false
    {
        $this->queries[] = ['sql' => $query, 'params' => []];
        return new MockPDOStatement();
    }
}

function verifyInsumoOptimization(): void
{
    echo "=== Verifying Insumo Model Query Optimizations ===\n";

    $refDb = new ReflectionClass(Database::class);
    $dbInstance = $refDb->newInstanceWithoutConstructor();

    $pdo = new QueryCapturePDO('sqlite::memory:');

    $refConn = $refDb->getProperty('connection');
    $refConn->setAccessible(true);
    $refConn->setValue($dbInstance, $pdo);

    $refInst = $refDb->getProperty('instance');
    $refInst->setAccessible(true);
    $refInst->setValue(null, $dbInstance);

    $insumoModel = new Insumo();

    // 1. Test getAllBySucursal
    $insumoModel->getAllBySucursal(1);
    assert(count($pdo->queries) === 1, "Expected 1 query for getAllBySucursal");
    $sql1 = $pdo->queries[0]['sql'];

    // Ensure no correlated scalar subqueries in SELECT clause
    assert(
        !preg_match('/SELECT\s+.*?\(\s*SELECT\s+cd\.costo_unitario/i', $sql1),
        "getAllBySucursal query should not contain correlated scalar subquery in SELECT"
    );

    // Ensure branch-filtered MAX derived table JOIN is used
    assert(
        str_contains($sql1, 'MAX(c2.id)') && str_contains($sql1, 'c2.id_sucursal = ?'),
        "getAllBySucursal query should use branch-filtered MAX derived table JOIN"
    );

    echo "✅ Insumo::getAllBySucursal uses optimized branch-scoped MAX derived table JOIN without correlated scalar subqueries.\n";

    // 2. Test all() without sucursal_id
    $pdo->queries = [];
    $insumoModel->all();
    assert(count($pdo->queries) === 1, "Expected 1 query for all()");
    $sql2 = $pdo->queries[0]['sql'];

    assert(
        !preg_match('/SELECT\s+.*?\(\s*SELECT\s+cd\.costo_unitario/i', $sql2),
        "all() query should not contain correlated scalar subquery in SELECT"
    );

    assert(
        str_contains($sql2, 'MAX(c2.id)'),
        "all() query should use MAX derived table JOIN"
    );

    echo "✅ Insumo::all uses optimized MAX derived table JOIN without correlated scalar subqueries.\n";
}

try {
    verifyInsumoOptimization();
    echo "🎉 All Insumo optimization assertions PASSED!\n";
} catch (Throwable $e) {
    echo "❌ Verification failed: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}
