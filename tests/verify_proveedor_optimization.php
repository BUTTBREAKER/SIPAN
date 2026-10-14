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

// Mocking BaseModel
namespace App\Models;

use App\Core\Database;

class BaseModel
{
    protected $db;
    protected string $table = '';
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

echo "Running Proveedor Optimization Verification Tests...\n\n";

// Test 1: addInsumos with multiple insumos (batch insert verification)
$db->queries = [];
$insumos = [
    ['id_insumo' => 10, 'precio' => 5.50, 'tiempo_entrega' => 2],
    ['id_insumo' => 11, 'precio' => 12.00, 'tiempo_entrega' => 3],
    ['id_insumo' => 12, 'precio' => 8.75, 'tiempo_entrega' => 1]
];

$proveedor->addInsumos(1, $insumos);

assert(count($db->queries) === 2, "Expected 2 queries (DELETE + 1 Batch INSERT)");

$deleteQuery = $db->queries[0];
assert(str_contains($deleteQuery['sql'], "DELETE FROM proveedor_insumos"), "First query should be DELETE");

$insertQuery = $db->queries[1];
assert(str_contains($insertQuery['sql'], "INSERT INTO proveedor_insumos"), "Second query should be INSERT");
assert(substr_count($insertQuery['sql'], "(?, ?, ?, ?)") === 3, "Insert SQL should contain 3 value placeholders");
assert(count($insertQuery['params']) === 12, "Insert params array should contain 12 elements for 3 rows x 4 cols");

echo "Test 1 (addInsumos batch insert): PASS\n";
echo "SQL: " . $insertQuery['sql'] . "\n";
echo "Params count: " . count($insertQuery['params']) . "\n\n";

// Test 2: addInsumos with empty insumos list
$db->queries = [];
$proveedor->addInsumos(1, []);

assert(count($db->queries) === 1, "Expected 1 query (DELETE only)");
assert(str_contains($db->queries[0]['sql'], "DELETE FROM proveedor_insumos"), "Only DELETE query executed when insumos list is empty");

echo "Test 2 (addInsumos empty list): PASS\n\n";

// Test 3: getInsumosSinProveedor NOT EXISTS verification
$db->queries = [];
$proveedor->getInsumosSinProveedor(5);

assert(count($db->queries) === 1, "Expected 1 query execution");
$sinProveedorQuery = $db->queries[0];

assert(str_contains($sinProveedorQuery['sql'], "NOT EXISTS"), "Query should use NOT EXISTS anti-join");
assert(!str_contains($sinProveedorQuery['sql'], "GROUP BY"), "Query should NOT use GROUP BY");
assert(!str_contains($sinProveedorQuery['sql'], "HAVING"), "Query should NOT use HAVING");
assert($sinProveedorQuery['params'] === [5], "Params should match sucursal_id = 5");

echo "Test 3 (getInsumosSinProveedor NOT EXISTS): PASS\n";
echo "SQL: " . $sinProveedorQuery['sql'] . "\n";
echo "Params: " . json_encode($sinProveedorQuery['params']) . "\n\n";

echo "All Proveedor optimization tests PASSED successfully!\n";
