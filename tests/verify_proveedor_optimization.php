<?php

// Mocking the Database class
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

    public function beginTransaction() {}
    public function commit() {}
    public function rollback() {}

    public function fetchAll($sql, $params = [])
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

// Mocking BaseModel
namespace App\Models;

use App\Core\Database;

class BaseModel
{
    protected $db;
    protected string $table;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }
}

// Include Proveedor model
require_once __DIR__ . '/../app/Models/Proveedor.php';

use App\Models\Proveedor;
use App\Core\Database as DB;

$proveedor = new Proveedor();
$db = DB::getInstance();

echo "Running Proveedor Model Optimizations Verification Tests...\n\n";

// Test 1: addInsumos batched multi-row INSERT
$insumos = [
    ['id_insumo' => 10, 'precio' => 15.5, 'tiempo_entrega' => 3],
    ['id_insumo' => 11, 'precio' => 20.0, 'tiempo_entrega' => 5],
    ['id_insumo' => 12, 'precio' => 5.25, 'tiempo_entrega' => 2]
];

$db->queries = []; // Clear query log
$proveedor->addInsumos(1, $insumos);

$deleteQuery = $db->queries[0];
$insertQuery = $db->queries[1];

$insertPass = (count($db->queries) === 2)
    && (strpos($insertQuery['sql'], 'INSERT INTO proveedor_insumos (id_proveedor, id_insumo, precio, tiempo_entrega) VALUES (?, ?, ?, ?), (?, ?, ?, ?), (?, ?, ?, ?)') !== false)
    && ($insertQuery['params'] === [1, 10, 15.5, 3, 1, 11, 20.0, 5, 1, 12, 5.25, 2]);

echo "Test 1 (addInsumos Batched Multi-row INSERT): " . ($insertPass ? "PASS" : "FAIL") . "\n";
echo "Delete Query: " . $deleteQuery['sql'] . "\n";
echo "Insert Query: " . $insertQuery['sql'] . "\n";
echo "Insert Params: " . json_encode($insertQuery['params']) . "\n\n";

// Test 2: getInsumosSinProveedor NOT EXISTS optimization
$db->queries = [];
$proveedor->getInsumosSinProveedor(2);

$query = $db->queries[0];

$notExistsPass = (strpos($query['sql'], 'NOT EXISTS') !== false)
    && (strpos($query['sql'], 'GROUP BY') === false)
    && (strpos($query['sql'], 'HAVING') === false)
    && ($query['params'] === [2]);

echo "Test 2 (getInsumosSinProveedor NOT EXISTS Anti-Semi-Join): " . ($notExistsPass ? "PASS" : "FAIL") . "\n";
echo "SQL: " . $query['sql'] . "\n";
echo "Params: " . json_encode($query['params']) . "\n\n";

if ($insertPass && $notExistsPass) {
    echo "ALL PROVEEDOR OPTIMIZATION TESTS PASSED SUCCESSFULLY!\n";
    exit(0);
} else {
    echo "PROVEEDOR OPTIMIZATION TESTS FAILED!\n";
    exit(1);
}
