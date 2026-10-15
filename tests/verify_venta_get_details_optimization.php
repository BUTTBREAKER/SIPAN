<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Database;
use App\Models\Venta;

function verifyOptimization()
{
    $dbReflection = new ReflectionClass(Database::class);
    $database = $dbReflection->newInstanceWithoutConstructor();

    $pdo = new PDO('sqlite::memory:');
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    $pdo->exec("
        CREATE TABLE usuarios (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            primer_nombre TEXT,
            apellido_paterno TEXT
        );
        CREATE TABLE clientes (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            nombre TEXT,
            apellido TEXT
        );
        CREATE TABLE ventas (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            id_sucursal INTEGER,
            id_usuario INTEGER,
            id_cliente INTEGER,
            total REAL,
            fecha_venta TEXT
        );
        INSERT INTO usuarios (id, primer_nombre, apellido_paterno) VALUES (1, 'Admin', 'User');
        INSERT INTO clientes (id, nombre, apellido) VALUES (1, 'Juan', 'Perez');
        INSERT INTO ventas (id, id_sucursal, id_usuario, id_cliente, total, fecha_venta) VALUES (1, 1, 1, 1, 100.0, '2023-01-01 10:00:00');
    ");

    $connProp = $dbReflection->getProperty('connection');
    $connProp->setAccessible(true);
    $connProp->setValue($database, $pdo);

    $reflection = new ReflectionClass(Venta::class);
    $model = $reflection->newInstanceWithoutConstructor();

    $dbProp = new ReflectionProperty(\App\Models\BaseModel::class, 'db');
    $dbProp->setAccessible(true);
    $dbProp->setValue($model, $database);

    $tableProp = new ReflectionProperty(\App\Models\BaseModel::class, 'table');
    $tableProp->setAccessible(true);
    $tableProp->setValue($model, 'ventas');

    echo "Running Venta::getWithDetails()...\n";
    $results = $model->getWithDetails(1);

    $methodRef = new ReflectionMethod(Venta::class, 'getWithDetails');
    $filename = $methodRef->getFileName();
    $startLine = $methodRef->getStartLine();
    $endLine = $methodRef->getEndLine();
    $source = implode('', array_slice(file($filename), $startLine - 1, $endLine - $startLine + 1));
    // Strip comments
    $codeNoComments = preg_replace('!/\*.*?\*/!s', '', $source);
    $codeNoComments = preg_replace('!//.*!', '', $codeNoComments);

    if (strpos($codeNoComments, 'LEFT JOIN venta_productos') !== false) {
        throw new Exception("Optimization failed: LEFT JOIN venta_productos still present in query.");
    }

    if (strpos($codeNoComments, 'COUNT(vp.id)') !== false) {
        throw new Exception("Optimization failed: COUNT(vp.id) still present in query.");
    }

    if (strpos($codeNoComments, 'GROUP BY') !== false) {
        throw new Exception("Optimization failed: GROUP BY still present in query.");
    }

    if (!isset($results[0]['cliente_nombre'])) {
        throw new Exception("Functional regression: cliente_nombre missing from results.");
    }

    echo "✅ Venta::getWithDetails optimization verified successfully!\n";
}

try {
    verifyOptimization();
} catch (Exception $e) {
    echo "❌ Verification failed: " . $e->getMessage() . "\n";
    exit(1);
}
