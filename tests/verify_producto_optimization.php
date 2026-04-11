<?php

/**
 * Test: Producto::all() Optimization Verification
 * This script uses Reflection to instantiate the Producto model without its constructor,
 * and then injects a mock database object. This allows us to verify the SQL generated
 * by the actual model class without needing a live database connection.
 */

require_once __DIR__ . '/../app/Models/BaseModel.php';
require_once __DIR__ . '/../app/Models/Producto.php';

class MockDB {
    public $lastSql = '';
    public $lastParams = [];
    public function fetchAll($sql, $params = []) {
        $this->lastSql = $sql;
        $this->lastParams = $params;
        return [];
    }
}

function verify_producto_all() {
    $mockDb = new MockDB();

    // Create Producto instance without triggering its constructor (which tries to connect to DB)
    $reflection = new ReflectionClass(\App\Models\Producto::class);
    $productoModel = $reflection->newInstanceWithoutConstructor();

    // Inject mock database into the private/protected 'db' property
    $dbProp = new ReflectionProperty(\App\Models\BaseModel::class, 'db');
    $dbProp->setAccessible(true);
    $dbProp->setValue($productoModel, $mockDb);

    echo "--- Testing Actual Producto::all() ---\n";

    // Test 1: With sucursal_id
    echo "Test 1: Filtered query (sucursal_id = 10)\n";
    $productoModel->all(10);

    $expectedSqlFiltered = "SELECT * FROM productos WHERE id_sucursal = ? ORDER BY nombre";
    if ($mockDb->lastSql === $expectedSqlFiltered && $mockDb->lastParams === [10]) {
        echo "✅ PASS: SQL correctly filtered by sucursal_id.\n";
    } else {
        echo "❌ FAIL: Incorrect SQL or params for filtered query.\n";
        echo "Got SQL: {$mockDb->lastSql}\n";
        echo "Got Params: " . json_encode($mockDb->lastParams) . "\n";
        exit(1);
    }

    // Test 2: Without sucursal_id
    echo "\nTest 2: Unfiltered query\n";
    $productoModel->all();

    $expectedSqlUnfiltered = "SELECT * FROM productos ORDER BY nombre";
    if ($mockDb->lastSql === $expectedSqlUnfiltered && empty($mockDb->lastParams)) {
        echo "✅ PASS: SQL correctly unfiltered when no ID provided.\n";
    } else {
        echo "❌ FAIL: Incorrect SQL or params for unfiltered query.\n";
        echo "Got SQL: {$mockDb->lastSql}\n";
        exit(1);
    }

    echo "\nSUCCESS: Producto::all() optimization verified with real class and reflection.\n";
}

verify_producto_all();
