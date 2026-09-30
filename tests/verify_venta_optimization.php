<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Database;
use App\Models\Venta;

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
");

$connProp = $dbReflection->getProperty('connection');
$connProp->setAccessible(true);
$connProp->setValue($database, $pdo);

$reflection = new ReflectionClass(Venta::class);
$ventaModel = $reflection->newInstanceWithoutConstructor();

$dbProp = new ReflectionProperty(\App\Models\BaseModel::class, 'db');
$dbProp->setAccessible(true);
$dbProp->setValue($ventaModel, $database);

$tableProp = new ReflectionProperty(\App\Models\BaseModel::class, 'table');
$tableProp->setAccessible(true);
$tableProp->setValue($ventaModel, 'ventas');

echo "--- Testing Venta::getWithDetails Optimization (Mocked) ---\n";

$ventaModel->getWithDetails(1);

$methodRef = new ReflectionMethod(Venta::class, 'getWithDetails');
$filename = $methodRef->getFileName();
$startLine = $methodRef->getStartLine();
$endLine = $methodRef->getEndLine();
$source = implode('', array_slice(file($filename), $startLine - 1, $endLine - $startLine + 1));
$codeNoComments = preg_replace('!/\*.*?\*/!s', '', $source);
$codeNoComments = preg_replace('!//.*!', '', $codeNoComments);

if (strpos($codeNoComments, 'LEFT JOIN venta_productos') !== false) {
    echo "❌ Error: Redundant LEFT JOIN venta_productos still present.\n";
    exit(1);
}

if (strpos($codeNoComments, 'COUNT(vp.id)') !== false) {
    echo "❌ Error: Redundant COUNT(vp.id) still present.\n";
    exit(1);
}

if (strpos($codeNoComments, 'GROUP BY') !== false) {
    echo "❌ Error: Redundant GROUP BY clause still present.\n";
    exit(1);
}

echo "✅ Venta optimization verified (Mocked)!\n";
