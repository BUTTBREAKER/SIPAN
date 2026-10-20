<?php

/**
 * Test: Insumo::getAllBySucursal() and Insumo::all() Optimization Verification
 * Verifies that correlated scalar subqueries in SELECT are eliminated and replaced
 * with derived table LEFT JOINs.
 */

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Database;
use App\Models\BaseModel;
use App\Models\Insumo;

class MockPDOStatement extends PDOStatement
{
    private MockPDO $mock;

    public function __construct(MockPDO $mock)
    {
        $this->mock = $mock;
    }

    public function execute($params = null): bool
    {
        if ($params !== null) {
            $this->mock->lastParams = $params;
        }
        return true;
    }

    public function fetchAll(int $mode = PDO::FETCH_DEFAULT, mixed ...$args): array
    {
        return [];
    }
}

class MockPDO extends PDO
{
    public string $lastSql = '';
    public array $lastParams = [];

    public function __construct()
    {
        // Do not call parent::__construct to avoid actual DB connection
    }

    public function prepare(string $query, array $options = []): PDOStatement|false
    {
        $this->lastSql = $query;
        return new MockPDOStatement($this);
    }
}

function verify_insumo_optimization(): void
{
    $mockPdo = new MockPDO();

    // Create Database instance without constructor
    $dbRef = new ReflectionClass(Database::class);
    $mockDb = $dbRef->newInstanceWithoutConstructor();

    $connProp = $dbRef->getProperty('connection');
    $connProp->setAccessible(true);
    $connProp->setValue($mockDb, $mockPdo);

    // Create Insumo model without constructor
    $insumoRef = new ReflectionClass(Insumo::class);
    $insumoModel = $insumoRef->newInstanceWithoutConstructor();

    $dbProp = new ReflectionProperty(BaseModel::class, 'db');
    $dbProp->setAccessible(true);
    $dbProp->setValue($insumoModel, $mockDb);

    echo "--- Testing Insumo::getAllBySucursal() ---\n";

    // Test 1: getAllBySucursal(5)
    $insumoModel->getAllBySucursal(5);

    $sql = $mockPdo->lastSql;
    $params = $mockPdo->lastParams;

    echo "Generated SQL: {$sql}\n";
    echo "Bound Params: " . json_encode($params) . "\n\n";

    // Verify NO correlated scalar subquery in SELECT
    if (preg_match('/SELECT\s+.*?\(\s*SELECT\s+cd\.costo_unitario/is', $sql)) {
        echo "❌ FAIL: Correlated scalar subquery found in SELECT clause!\n";
        exit(1);
    }

    // Verify derived table JOIN is present
    if (str_pos_ci($sql, 'LEFT JOIN (') !== false && str_pos_ci($sql, 'SUBSTRING_INDEX') !== false) {
        echo "✅ PASS: Derived table JOIN with SUBSTRING_INDEX used for costo_ultimo.\n";
    } else {
        echo "❌ FAIL: Expected derived table JOIN with SUBSTRING_INDEX not found.\nGot SQL: {$sql}\n";
        exit(1);
    }

    // Verify parameter bindings
    if ($params === [5, 5]) {
        echo "✅ PASS: Correct parameter bindings [5, 5].\n";
    } else {
        echo "❌ FAIL: Expected parameter bindings [5, 5], got: " . json_encode($params) . "\n";
        exit(1);
    }

    echo "\n--- Testing Insumo::all() ---\n";

    // Test 2: all() without sucursal_id
    $insumoModel->all();

    $sqlAll = $mockPdo->lastSql;
    $paramsAll = $mockPdo->lastParams;

    echo "Generated SQL: {$sqlAll}\n";
    echo "Bound Params: " . json_encode($paramsAll) . "\n\n";

    if (preg_match('/SELECT\s+.*?\(\s*SELECT\s+cd\.costo_unitario/is', $sqlAll)) {
        echo "❌ FAIL: Correlated scalar subquery found in SELECT clause for all()!\n";
        exit(1);
    }

    if (str_pos_ci($sqlAll, 'LEFT JOIN (') !== false && str_pos_ci($sqlAll, 'SUBSTRING_INDEX') !== false) {
        echo "✅ PASS: Derived table JOIN used in all().\n";
    } else {
        echo "❌ FAIL: Expected derived table JOIN in all().\nGot SQL: {$sqlAll}\n";
        exit(1);
    }

    if (empty($paramsAll)) {
        echo "✅ PASS: No parameter bindings for global all().\n";
    } else {
        echo "❌ FAIL: Expected empty params for all(), got: " . json_encode($paramsAll) . "\n";
        exit(1);
    }

    // Test 3: all(5) with sucursal_id
    $insumoModel->all(5);

    if ($mockPdo->lastParams === [5, 5]) {
        echo "✅ PASS: all(5) correctly delegated to getAllBySucursal(5).\n";
    } else {
        echo "❌ FAIL: all(5) did not delegate properly.\n";
        exit(1);
    }

    echo "\nSUCCESS: Insumo optimization verified successfully!\n";
}

function str_pos_ci(string $haystack, string $needle): bool
{
    return stripos($haystack, $needle) !== false;
}

verify_insumo_optimization();
