<?php

namespace App\Core;

class Database
{
    private static $instance = null;
    public $executedQueries = [];
    public $inTransaction = false;

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function beginTransaction()
    {
        $this->inTransaction = true;
        $this->executedQueries[] = ['type' => 'BEGIN'];
        return true;
    }

    public function commit()
    {
        $this->inTransaction = false;
        $this->executedQueries[] = ['type' => 'COMMIT'];
        return true;
    }

    public function rollback()
    {
        $this->inTransaction = false;
        $this->executedQueries[] = ['type' => 'ROLLBACK'];
        return true;
    }

    public function execute($sql, $params = [])
    {
        $this->executedQueries[] = ['type' => 'EXECUTE', 'sql' => $sql, 'params' => $params];
        return true;
    }

    public function fetchAll($sql, $params = [])
    {
        $this->executedQueries[] = ['type' => 'FETCH_ALL', 'sql' => $sql, 'params' => $params];
        return [];
    }

    public function fetchOne($sql, $params = [])
    {
        $this->executedQueries[] = ['type' => 'FETCH_ONE', 'sql' => $sql, 'params' => $params];
        return [];
    }

    public function reset()
    {
        $this->executedQueries = [];
        $this->inTransaction = false;
    }
}

namespace App\Models;

use App\Core\Database;

class BaseModel
{
    protected Database $db;
    protected string $table;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }
}

require_once __DIR__ . '/../app/Models/Proveedor.php';

use App\Models\Proveedor;
use App\Core\Database as DB;

$proveedorModel = new Proveedor();
$db = DB::getInstance();

echo "Running Proveedor::addInsumos Optimization Verification...\n\n";

// Test Case 1: Attaching 3 insumos to a provider
$db->reset();
$insumosTest = [
    ['id_insumo' => 101, 'precio' => 12.50, 'tiempo_entrega' => 3],
    ['id_insumo' => 102, 'precio' => 8.00, 'tiempo_entrega' => null],
    ['id_insumo' => 103, 'precio' => 15.25, 'tiempo_entrega' => 5],
];

$proveedorModel->addInsumos(1, $insumosTest);

$executeQueries = array_filter($db->executedQueries, fn($q) => $q['type'] === 'EXECUTE');
$insertQueries = array_filter($executeQueries, fn($q) => str_contains($q['sql'], 'INSERT INTO proveedor_insumos'));

echo "Executing 3 insumos insert:\n";
echo "Total EXECUTE queries run: " . count($executeQueries) . "\n";
echo "Total INSERT queries run: " . count($insertQueries) . "\n";

assert(count($executeQueries) === 2, "Expected exactly 2 EXECUTE queries (1 DELETE + 1 Batched INSERT), got " . count($executeQueries));
assert(count($insertQueries) === 1, "Expected exactly 1 Batched INSERT query, got " . count($insertQueries));

$batchInsertQuery = reset($insertQueries);
$expectedSql = "INSERT INTO proveedor_insumos (id_proveedor, id_insumo, precio, tiempo_entrega) VALUES (?, ?, ?, ?), (?, ?, ?, ?), (?, ?, ?, ?)";

assert($batchInsertQuery['sql'] === $expectedSql, "Generated SQL does not match expected batched SQL.\nActual: {$batchInsertQuery['sql']}\nExpected: {$expectedSql}");

$expectedParams = [
    1, 101, 12.50, 3,
    1, 102, 8.00, null,
    1, 103, 15.25, 5
];

assert($batchInsertQuery['params'] === $expectedParams, "Query parameters do not match expected bound values.\nActual: " . json_encode($batchInsertQuery['params']) . "\nExpected: " . json_encode($expectedParams));

echo "✅ Test 1 Passed: 3 insumos inserted in 1 batched SQL round-trip.\n\n";

// Test Case 2: Empty insumos list
$db->reset();
$proveedorModel->addInsumos(1, []);

$executeQueries = array_filter($db->executedQueries, fn($q) => $q['type'] === 'EXECUTE');
$insertQueries = array_filter($executeQueries, fn($q) => str_contains($q['sql'], 'INSERT INTO proveedor_insumos'));

assert(count($executeQueries) === 1, "Expected exactly 1 EXECUTE query (DELETE only for empty input)");
assert(count($insertQueries) === 0, "Expected 0 INSERT queries for empty insumos array");

echo "✅ Test 2 Passed: Empty insumos array handled cleanly without executing INSERT.\n\n";

echo "ALL TESTS PASSED SUCCESSFULLY! 🚀\n";
