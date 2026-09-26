<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Models\Compra;
use App\Core\Database;

// Setup an in-memory SQLite database connection
$pdo = new PDO('sqlite::memory:');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

// Create required mock tables in SQLite
$pdo->exec("CREATE TABLE compras (id INTEGER PRIMARY KEY AUTOINCREMENT, id_sucursal INT, id_proveedor INT, total REAL, fecha_compra TEXT)");
$pdo->exec("CREATE TABLE compra_detalles (id INTEGER PRIMARY KEY AUTOINCREMENT, id_compra INT, tipo_item TEXT, id_item INT, cantidad REAL, costo_unitario REAL, subtotal REAL, lote_codigo TEXT, fecha_vencimiento TEXT)");
$pdo->exec("CREATE TABLE insumos (id INTEGER PRIMARY KEY, stock_actual REAL, precio_unitario REAL)");
$pdo->exec("CREATE TABLE productos (id INTEGER PRIMARY KEY, stock_actual REAL)");
$pdo->exec("CREATE TABLE lotes (id INTEGER PRIMARY KEY AUTOINCREMENT, id_sucursal INT, tipo TEXT, id_item INT, codigo_lote TEXT, fecha_entrada TEXT, fecha_vencimiento TEXT, cantidad_inicial REAL, cantidad_actual REAL, costo_unitario REAL, estado TEXT)");

// Insert test data
$pdo->exec("INSERT INTO insumos (id, stock_actual, precio_unitario) VALUES (1, 10, 2.0), (2, 20, 5.0)");
$pdo->exec("INSERT INTO productos (id, stock_actual) VALUES (10, 5), (11, 15)");

// Inject SQLite PDO into App\Core\Database singleton using Reflection
$dbReflection = new ReflectionClass(Database::class);
$databaseObj = $dbReflection->newInstanceWithoutConstructor();

$pdoProp = $dbReflection->getProperty('connection');
$pdoProp->setAccessible(true);
$pdoProp->setValue($databaseObj, $pdo);

$instanceProp = $dbReflection->getProperty('instance');
$instanceProp->setAccessible(true);
$instanceProp->setValue(null, $databaseObj);

echo "Testing Compra::createWithDetails batch update optimization with SQLite in-memory DB...\n";

$compra = new Compra();

$compraData = [
    'id_sucursal' => 1,
    'id_proveedor' => 2,
    'total' => 150,
    'fecha_compra' => date('Y-m-d H:i:s')
];

$detalles = [
    [
        'tipo_item' => 'insumo',
        'id_item' => 1,
        'cantidad' => 5,
        'costo_unitario' => 3.0,
        'subtotal' => 15.0,
        'lote_codigo' => 'L001',
        'fecha_vencimiento' => '2026-12-31'
    ],
    [
        'tipo_item' => 'insumo',
        'id_item' => 2,
        'cantidad' => 10,
        'costo_unitario' => 6.0,
        'subtotal' => 60.0,
        'lote_codigo' => 'L002',
        'fecha_vencimiento' => '2026-12-31'
    ],
    [
        'tipo_item' => 'producto',
        'id_item' => 10,
        'cantidad' => 2,
        'costo_unitario' => 10.0,
        'subtotal' => 20.0,
        'lote_codigo' => null,
        'fecha_vencimiento' => null
    ],
    [
        'tipo_item' => 'producto',
        'id_item' => 11,
        'cantidad' => 4,
        'costo_unitario' => 10.0,
        'subtotal' => 40.0,
        'lote_codigo' => null,
        'fecha_vencimiento' => null
    ]
];

$compraId = $compra->createWithDetails($compraData, $detalles);

assert($compraId > 0, "Compra ID should be created.");

// Verify updated values in SQLite
$insumo1 = $pdo->query("SELECT * FROM insumos WHERE id = 1")->fetch();
$insumo2 = $pdo->query("SELECT * FROM insumos WHERE id = 2")->fetch();

assert((float)$insumo1['stock_actual'] === 15.0, "Insumo 1 stock should be 15");
assert(abs((float)$insumo1['precio_unitario'] - 2.3333) < 0.001, "Insumo 1 weighted average price calculation should be correct");

assert((float)$insumo2['stock_actual'] === 30.0, "Insumo 2 stock should be 30");
assert(abs((float)$insumo2['precio_unitario'] - 5.3333) < 0.001, "Insumo 2 weighted average price calculation should be correct");

$prod10 = $pdo->query("SELECT * FROM productos WHERE id = 10")->fetch();
$prod11 = $pdo->query("SELECT * FROM productos WHERE id = 11")->fetch();

assert((float)$prod10['stock_actual'] === 7.0, "Producto 10 stock should be 7");
assert((float)$prod11['stock_actual'] === 19.0, "Producto 11 stock should be 19");

echo "✅ SUCCESS: Compra::createWithDetails batched updates executed correctly and calculated accurate stock/price values!\n";