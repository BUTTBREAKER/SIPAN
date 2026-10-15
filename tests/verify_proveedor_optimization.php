<?php

namespace App\Core;

class Database
{
    private static $instance = null;
    public array $queries = [];

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function beginTransaction()
    {
        $this->queries[] = ['sql' => 'BEGIN', 'params' => []];
    }

    public function commit()
    {
        $this->queries[] = ['sql' => 'COMMIT', 'params' => []];
    }

    public function rollback()
    {
        $this->queries[] = ['sql' => 'ROLLBACK', 'params' => []];
    }

    public function execute($sql, $params = [])
    {
        $this->queries[] = ['sql' => $sql, 'params' => $params];
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
}

namespace Tests;

require_once __DIR__ . '/../vendor/autoload.php';

use App\Models\Proveedor;
use App\Core\Database;

function testProveedorOptimizations()
{
    echo "=== Testing Proveedor Optimizations ===\n\n";

    $db = Database::getInstance();
    $proveedorModel = new Proveedor();

    // Test 1: addInsumos Batch Insert
    echo "1. Testing addInsumos Multi-Row Batch Insert...\n";
    $db->queries = [];

    $proveedorId = 10;
    $insumos = [
        ['id_insumo' => 1, 'precio' => 15.5, 'tiempo_entrega' => 3],
        ['id_insumo' => 2, 'precio' => 25.0, 'tiempo_entrega' => 5],
        ['id_insumo' => 3, 'precio' => 8.75, 'tiempo_entrega' => null]
    ];

    $proveedorModel->addInsumos($proveedorId, $insumos);

    $deleteFound = false;
    $batchInsertFound = false;

    foreach ($db->queries as $q) {
        if (strpos($q['sql'], 'DELETE FROM proveedor_insumos') !== false) {
            $deleteFound = true;
        }
        if (strpos($q['sql'], 'INSERT INTO proveedor_insumos') !== false) {
            $batchInsertFound = true;
            $expectedSql = "INSERT INTO proveedor_insumos (id_proveedor, id_insumo, precio, tiempo_entrega) VALUES (?, ?, ?, ?), (?, ?, ?, ?), (?, ?, ?, ?)";
            assert($q['sql'] === $expectedSql, "SQL should match multi-row batch insert. Got: " . $q['sql']);
            assert(count($q['params']) === 12, "Parameters count should be 12 (4 * 3). Got: " . count($q['params']));
            assert($q['params'][0] === 10 && $q['params'][1] === 1, "First row params invalid");
            assert($q['params'][4] === 10 && $q['params'][5] === 2, "Second row params invalid");
        }
    }

    assert($deleteFound, "DELETE query should be executed");
    assert($batchInsertFound, "Batch INSERT query should be executed in a single query execution");
    echo "   ✅ addInsumos multi-row batch insert test PASSED!\n\n";

    // Test 2: getInsumosSinProveedor NOT EXISTS
    echo "2. Testing getInsumosSinProveedor SARGable NOT EXISTS Query...\n";
    $db->queries = [];

    $sucursalId = 1;
    $proveedorModel->getInsumosSinProveedor($sucursalId);

    $lastQuery = end($db->queries);
    assert(strpos($lastQuery['sql'], 'NOT EXISTS') !== false, "Query must contain NOT EXISTS clause");
    assert(strpos($lastQuery['sql'], 'HAVING') === false, "Query must NOT contain HAVING clause");
    assert(strpos($lastQuery['sql'], 'GROUP BY') === false, "Query must NOT contain GROUP BY clause");
    assert($lastQuery['params'] === [1], "Params should be sucursal_id");

    echo "   ✅ getInsumosSinProveedor NOT EXISTS test PASSED!\n\n";

    echo "All Proveedor optimization tests PASSED successfully!\n";
}

testProveedorOptimizations();
