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
}

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

require_once __DIR__ . '/../app/Models/Proveedor.php';

use App\Models\Proveedor;
use App\Core\Database as DB;

$proveedor = new Proveedor();
$db = DB::getInstance();

echo "Running Proveedor Optimization Tests...\n\n";

// Test 1: addInsumos with multiple insumos (batch insert)
$db->queries = [];
$insumosMock = [
    ['id_insumo' => 10, 'precio' => 15.5, 'tiempo_entrega' => 3],
    ['id_insumo' => 11, 'precio' => 22.0, 'tiempo_entrega' => 5],
    ['id_insumo' => 12, 'precio' => 5.0, 'tiempo_entrega' => 1]
];

$proveedor->addInsumos(5, $insumosMock);

$insertQuery = null;
foreach ($db->queries as $q) {
    if (strpos($q['sql'], 'INSERT INTO proveedor_insumos') !== false) {
        $insertQuery = $q;
        break;
    }
}

if ($insertQuery && count($db->queries) === 2) {
    echo "Test 1 (Batch INSERT query count O(1)): PASS\n";
} else {
    echo "Test 1 (Batch INSERT query count O(1)): FAIL\n";
}

if ($insertQuery && substr_count($insertQuery['sql'], '(?, ?, ?, ?)') === 3) {
    echo "Test 2 (Batch INSERT multi-row placeholders): PASS\n";
} else {
    echo "Test 2 (Batch INSERT multi-row placeholders): FAIL\n";
}

if ($insertQuery && count($insertQuery['params']) === 12) {
    echo "Test 3 (Batch INSERT parameter count): PASS\n";
} else {
    echo "Test 3 (Batch INSERT parameter count): FAIL\n";
}

// Test 4: addInsumos with empty insumos
$db->queries = [];
$proveedor->addInsumos(5, []);

if (count($db->queries) === 1 && strpos($db->queries[0]['sql'], 'DELETE FROM proveedor_insumos') !== false) {
    echo "Test 4 (Empty insumos handling): PASS\n";
} else {
    echo "Test 4 (Empty insumos handling): FAIL\n";
}

// Test 5: getInsumosSinProveedor uses NOT EXISTS and no GROUP BY
$db->queries = [];
$proveedor->getInsumosSinProveedor(1);
$lastQuery = end($db->queries);

if (
    strpos($lastQuery['sql'], 'NOT EXISTS') !== false &&
    strpos($lastQuery['sql'], 'GROUP BY') === false &&
    strpos($lastQuery['sql'], 'HAVING') === false
) {
    echo "Test 5 (getInsumosSinProveedor NOT EXISTS SARGable structure): PASS\n";
} else {
    echo "Test 5 (getInsumosSinProveedor NOT EXISTS SARGable structure): FAIL\n";
}

echo "\nVerification Complete!\n";
