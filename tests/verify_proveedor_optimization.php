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

namespace App\Test;

require_once __DIR__ . '/../app/Models/BaseModel.php';
require_once __DIR__ . '/../app/Models/Proveedor.php';

use App\Models\Proveedor;
use App\Core\Database as DB;

$proveedor = new Proveedor();
$db = DB::getInstance();

echo "Running Proveedor Model Optimization Tests...\n\n";

// Test 1: addInsumos multi-row batch insert
$db->queries = [];
$proveedor->addInsumos(5, [
    ['id_insumo' => 10, 'precio' => 12.50, 'tiempo_entrega' => '3 días'],
    ['id_insumo' => 20, 'precio' => 8.00, 'tiempo_entrega' => '1 día'],
    ['id_insumo' => 30, 'precio' => 15.00, 'tiempo_entrega' => null],
]);

$insertQuery = null;
foreach ($db->queries as $q) {
    if (str_contains($q['sql'], 'INSERT INTO proveedor_insumos')) {
        $insertQuery = $q;
        break;
    }
}

if ($insertQuery !== null && str_contains($insertQuery['sql'], '(?, ?, ?, ?), (?, ?, ?, ?), (?, ?, ?, ?)')) {
    echo "Test 1 (addInsumos Batched Multi-row INSERT): PASS\n";
    echo "  SQL: " . $insertQuery['sql'] . "\n";
    echo "  Params count: " . count($insertQuery['params']) . " (Expected: 12)\n\n";
} else {
    echo "Test 1 (addInsumos Batched Multi-row INSERT): FAIL\n";
    echo "  Queries logged: " . json_encode($db->queries) . "\n\n";
    exit(1);
}

// Test 2: getInsumosSinProveedor NOT EXISTS query
$db->queries = [];
$proveedor->getInsumosSinProveedor(1);
$lastQuery = end($db->queries);

if (
    str_contains($lastQuery['sql'], 'NOT EXISTS') &&
    !str_contains($lastQuery['sql'], 'GROUP BY') &&
    !str_contains($lastQuery['sql'], 'HAVING')
) {
    echo "Test 2 (getInsumosSinProveedor SARGable NOT EXISTS): PASS\n";
    echo "  SQL: " . $lastQuery['sql'] . "\n";
    echo "  Params: " . json_encode($lastQuery['params']) . "\n\n";
} else {
    echo "Test 2 (getInsumosSinProveedor SARGable NOT EXISTS): FAIL\n";
    echo "  Query logged: " . json_encode($lastQuery) . "\n\n";
    exit(1);
}

echo "All Proveedor optimization tests passed successfully!\n";
