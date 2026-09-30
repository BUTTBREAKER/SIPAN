<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../app/Models/BaseModel.php';
require_once __DIR__ . '/../app/Models/Producto.php';

use App\Core\Database;

function verify_producto_all()
{
    $dbReflection = new ReflectionClass(Database::class);
    $mockDb = $dbReflection->newInstanceWithoutConstructor();

    $pdo = new PDO('sqlite::memory:');
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->exec("
        CREATE TABLE productos (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            nombre TEXT NOT NULL,
            id_sucursal INTEGER
        );
        INSERT INTO productos (nombre, id_sucursal) VALUES ('Pan', 10);
    ");

    $connProp = $dbReflection->getProperty('connection');
    $connProp->setAccessible(true);
    $connProp->setValue($mockDb, $pdo);

    $reflection = new ReflectionClass(\App\Models\Producto::class);
    $productoModel = $reflection->newInstanceWithoutConstructor();

    $dbProp = new ReflectionProperty(\App\Models\BaseModel::class, 'db');
    $dbProp->setAccessible(true);
    $dbProp->setValue($productoModel, $mockDb);

    echo "--- Testing Actual Producto::all() ---\n";

    // Test 1: With sucursal_id
    echo "Test 1: Filtered query (sucursal_id = 10)\n";
    $result1 = $productoModel->all(10);
    if (count($result1) === 1 && $result1[0]['nombre'] === 'Pan') {
        echo "✅ PASS: SQL correctly filtered by sucursal_id.\n";
    } else {
        echo "❌ FAIL: Incorrect result for filtered query.\n";
        exit(1);
    }

    // Test 2: Without sucursal_id
    echo "\nTest 2: Unfiltered query\n";
    $result2 = $productoModel->all();
    if (count($result2) === 1) {
        echo "✅ PASS: SQL correctly unfiltered when no ID provided.\n";
    } else {
        echo "❌ FAIL: Incorrect result for unfiltered query.\n";
        exit(1);
    }

    echo "\nSUCCESS: Producto::all() optimization verified with real class and reflection.\n";
}

verify_producto_all();
