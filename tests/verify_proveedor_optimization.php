<?php

// Mocking Database singleton in App\Core namespace
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
        $this->queries[] = ['sql' => 'BEGIN TRANSACTION', 'params' => []];
        return true;
    }

    public function commit()
    {
        $this->queries[] = ['sql' => 'COMMIT', 'params' => []];
        return true;
    }

    public function rollback()
    {
        $this->queries[] = ['sql' => 'ROLLBACK', 'params' => []];
        return true;
    }
}

namespace App\Models;

use App\Core\Database;

class BaseModel
{
    protected string $table;
    protected $db;

    final public function __construct()
    {
        $this->db = Database::getInstance();
    }
}

require_once __DIR__ . '/../app/Models/Proveedor.php';

use App\Models\Proveedor;
use App\Core\Database as DB;

$proveedor = new Proveedor();
$db = DB::getInstance();

echo "Running Proveedor Optimization Verification Tests...\n\n";

// Test 1: Verify addInsumos creates a multi-row INSERT query instead of N queries
$db->queries = [];
$insumos = [
    ['id_insumo' => 10, 'precio' => 12.50, 'tiempo_entrega' => '2025-02-01'],
    ['id_insumo' => 20, 'precio' => 5.00, 'tiempo_entrega' => null],
    ['id_insumo' => 30, 'precio' => 8.75, 'tiempo_entrega' => '2025-02-15'],
];

$proveedor->addInsumos(1, $insumos);

$insertQuery = null;
foreach ($db->queries as $q) {
    if (strpos($q['sql'], 'INSERT INTO proveedor_insumos') !== false) {
        $insertQuery = $q;
        break;
    }
}

if ($insertQuery !== null && strpos($insertQuery['sql'], '(?, ?, ?, ?), (?, ?, ?, ?), (?, ?, ?, ?)') !== false) {
    echo "✅ PASS: addInsumos successfully created a batched multi-row INSERT query.\n";
    echo "SQL: " . $insertQuery['sql'] . "\n";
    echo "Params count: " . count($insertQuery['params']) . " (Expected: 12)\n\n";
} else {
    echo "❌ FAIL: addInsumos did not create the expected multi-row INSERT query.\n";
    exit(1);
}

// Test 2: Verify getInsumosSinProveedor uses NOT EXISTS and no GROUP BY / HAVING
$db->queries = [];
$proveedor->getInsumosSinProveedor(1);

$lastQuery = end($db->queries);
$sql = $lastQuery['sql'];

if (
    strpos($sql, 'NOT EXISTS') !== false &&
    strpos($sql, 'GROUP BY') === false &&
    strpos($sql, 'HAVING') === false
) {
    echo "✅ PASS: getInsumosSinProveedor uses SARGable NOT EXISTS query without GROUP BY / HAVING.\n";
    echo "SQL: " . $sql . "\n\n";
} else {
    echo "❌ FAIL: getInsumosSinProveedor query structure invalid.\n";
    echo "SQL: " . $sql . "\n";
    exit(1);
}

echo "All Proveedor optimization tests PASSED successfully!\n";
