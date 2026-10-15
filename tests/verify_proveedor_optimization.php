<?php

namespace App\Core;

class Database
{
    private static $instance = null;
    public $queries = [];

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function beginTransaction()
    {
        return true;
    }

    public function commit()
    {
        return true;
    }

    public function rollback()
    {
        return true;
    }

    public function fetchAll($sql, $params = [])
    {
        $this->queries[] = ['sql' => $sql, 'params' => $params];
        return [];
    }

    public function fetchOne($sql, $params = [])
    {
        $this->queries[] = ['sql' => $sql, 'params' => $params];
        return [];
    }

    public function execute($sql, $params = [])
    {
        $this->queries[] = ['sql' => $sql, 'params' => $params];
        return true;
    }
}

namespace App\Models;

use App\Core\Database;

class BaseModel
{
    protected string $table;
    protected $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }
}

require_once __DIR__ . '/../app/Models/Proveedor.php';

use App\Models\Proveedor;
use App\Core\Database as DB;

$proveedor = new Proveedor();
$db = DB::getInstance();

echo "Running Proveedor Optimization Verification...\n\n";

// Test 1: addInsumos with multiple insumos (Batched Multi-row INSERT)
$db->queries = [];
$insumos = [
    ['id_insumo' => 10, 'precio' => 12.5, 'tiempo_entrega' => 3],
    ['id_insumo' => 20, 'precio' => 45.0, 'tiempo_entrega' => 5],
    ['id_insumo' => 30, 'precio' => 8.00, 'tiempo_entrega' => 1],
];

$proveedor->addInsumos(5, $insumos);

// Should execute DELETE + 1 Batched INSERT (total 2 queries)
assert(count($db->queries) === 2, "Expected exactly 2 queries for addInsumos with 3 items");
assert(strpos($db->queries[0]['sql'], "DELETE FROM proveedor_insumos") !== false, "First query should be DELETE");
assert(strpos($db->queries[1]['sql'], "INSERT INTO proveedor_insumos") !== false, "Second query should be INSERT");
assert(strpos($db->queries[1]['sql'], "(?, ?, ?, ?), (?, ?, ?, ?), (?, ?, ?, ?)") !== false, "INSERT should be batched multi-row with 3 value placeholders");
assert(count($db->queries[1]['params']) === 12, "Parameters should contain 12 elements (4 per row)");

echo "Test 1 Passed: addInsumos uses O(1) batched multi-row INSERT\n";
echo "SQL: " . $db->queries[1]['sql'] . "\n";
echo "Params: " . json_encode($db->queries[1]['params']) . "\n\n";

// Test 2: getInsumosSinProveedor (NOT EXISTS optimization)
$db->queries = [];
$proveedor->getInsumosSinProveedor(2);

assert(count($db->queries) === 1, "Expected 1 query for getInsumosSinProveedor");
$querySql = $db->queries[0]['sql'];
assert(strpos($querySql, "NOT EXISTS") !== false, "Query should use NOT EXISTS");
assert(strpos($querySql, "GROUP BY") === false, "Query should not use GROUP BY");
assert(strpos($querySql, "HAVING") === false, "Query should not use HAVING");

echo "Test 2 Passed: getInsumosSinProveedor uses SARGable NOT EXISTS query\n";
echo "SQL: " . $querySql . "\n";
echo "Params: " . json_encode($db->queries[0]['params']) . "\n\n";

echo "All Proveedor optimization tests passed successfully!\n";
