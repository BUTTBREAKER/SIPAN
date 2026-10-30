<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Database;
use App\Models\Producto;
use App\Models\Receta;
use App\Models\Pedido;
use App\Controllers\PedidosController;
use Leaf\Http\Session;

final class MockPDOStatementForUltimoCliente extends PDOStatement
{
    public function execute($params = null): bool
    {
        return parent::execute($params);
    }
}

class MockPDOForUltimoCliente extends PDO
{
    public function __construct()
    {
        parent::__construct('sqlite::memory:');
        $this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $this->setAttribute(PDO::ATTR_STATEMENT_CLASS, [MockPDOStatementForUltimoCliente::class]);
    }
}

// 1. Setup Mock Database
$pdo = new MockPDOForUltimoCliente();

$pdo->exec("
    CREATE TABLE productos (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        nombre TEXT,
        precio_costo REAL,
        precio_actual REAL,
        stock_actual REAL,
        id_sucursal INTEGER
    );

    CREATE TABLE recetas (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        id_producto INTEGER,
        rendimiento REAL,
        id_sucursal INTEGER
    );

    CREATE TABLE insumos (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        nombre TEXT,
        unidad_medida TEXT,
        precio_unitario REAL,
        stock_actual REAL
    );

    CREATE TABLE receta_insumos (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        id_receta INTEGER,
        id_insumo INTEGER,
        cantidad REAL,
        unidad_medida TEXT
    );

    CREATE TABLE pedidos (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        id_cliente INTEGER,
        id_sucursal INTEGER,
        fecha_pedido TEXT
    );

    CREATE TABLE pedido_productos (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        id_pedido INTEGER,
        id_producto INTEGER,
        cantidad REAL,
        precio_unitario REAL,
        subtotal REAL
    );

    CREATE TABLE clientes (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        nombre TEXT,
        id_sucursal INTEGER
    );

    CREATE TABLE cajas (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        id_sucursal INTEGER,
        estado TEXT
    );
");

// Insert seed data
$pdo->exec("INSERT INTO productos (id, nombre, precio_costo, precio_actual, stock_actual, id_sucursal) VALUES (1, 'Pan Frances', 0.50, 1.00, 100, 1)");
$pdo->exec("INSERT INTO productos (id, nombre, precio_costo, precio_actual, stock_actual, id_sucursal) VALUES (2, 'Torta Chocolate', 5.00, 10.00, 10, 1)");

$pdo->exec("INSERT INTO insumos (id, nombre, unidad_medida, precio_unitario, stock_actual) VALUES (10, 'Harina', 'kg', 0.80, 50)");
$pdo->exec("INSERT INTO insumos (id, nombre, unidad_medida, precio_unitario, stock_actual) VALUES (11, 'Levadura', 'kg', 2.00, 10)");

$pdo->exec("INSERT INTO recetas (id, id_producto, rendimiento, id_sucursal) VALUES (100, 1, 10, 1)"); // Yields 10 units of Pan Frances
$pdo->exec("INSERT INTO receta_insumos (id_receta, id_insumo, cantidad, unidad_medida) VALUES (100, 10, 2.0, 'kg')"); // 2kg Harina
$pdo->exec("INSERT INTO receta_insumos (id_receta, id_insumo, cantidad, unidad_medida) VALUES (100, 11, 0.1, 'kg')"); // 0.1kg Levadura

$pdo->exec("INSERT INTO clientes (id, nombre, id_sucursal) VALUES (1, 'Cliente Test', 1)");
$pdo->exec("INSERT INTO pedidos (id, id_cliente, id_sucursal, fecha_pedido) VALUES (50, 1, 1, '2025-01-24 10:00:00')");
$pdo->exec("INSERT INTO pedido_productos (id_pedido, id_producto, cantidad, precio_unitario, subtotal) VALUES (50, 1, 20, 1.00, 20.00)"); // 20 units Pan Frances
$pdo->exec("INSERT INTO pedido_productos (id_pedido, id_producto, cantidad, precio_unitario, subtotal) VALUES (50, 2, 2, 10.00, 20.00)"); // 2 units Torta (no recipe, fallback to cost)

$pdo->exec("INSERT INTO cajas (id, id_sucursal, estado) VALUES (1, 1, 'abierta')");

// Inject DB connection into Database singleton
$reflection = new ReflectionClass(Database::class);
$instanceProperty = $reflection->getProperty('instance');
$instanceProperty->setAccessible(true);
$dbSingleton = $reflection->newInstanceWithoutConstructor();

$connProperty = $reflection->getProperty('connection');
$connProperty->setAccessible(true);
$connProperty->setValue($dbSingleton, $pdo);

$instanceProperty->setValue(null, $dbSingleton);

$productoModel = new Producto();
$recetaModel = new Receta();

$productsMap = $productoModel->getByIds([1, 2]);
assert(count($productsMap) === 2, "Expected 2 products in batch map");
assert($productsMap[1]['nombre'] === 'Pan Frances', "Expected product 1 to be Pan Frances");

$recetasMap = $recetaModel->getByProductoIds([1, 2]);
assert(count($recetasMap) === 1, "Expected 1 recipe (product 1 only)");
assert($recetasMap[1]['id'] == 100, "Expected recipe ID 100 for product 1");

$insumosMap = $recetaModel->getInsumosPorRecetaIds([100]);
assert(count($insumosMap[100]) === 2, "Expected 2 ingredients for recipe 100");

// Register shutdown function to verify controller response when exit() is triggered
register_shutdown_function(function() {
    $output = ob_get_clean();
    $data = json_decode($output, true);
    assert($data !== null, "Failed to parse JSON output: " . $output);
    assert($data['success'] === true, "Expected success: true, got output: " . $output);

    assert($data['gasto_insumos'] == 13.60, "Expected gasto_insumos to be 13.60, got: " . $data['gasto_insumos']);
    assert(str_contains($data['insumos_resumen_str'], 'Harina: 4 kg'), "Expected Harina: 4 kg in summary string, got: " . $data['insumos_resumen_str']);
    assert(str_contains($data['insumos_resumen_str'], 'Levadura: 0.2 kg'), "Expected Levadura: 0.2 kg in summary string, got: " . $data['insumos_resumen_str']);

    echo "PedidosController::ultimoCliente batch optimization verified SUCCESSFULLY!\n";
});

// Test PedidosController::ultimoCliente
Session::set('user_id', 1);
Session::set('user_nombre', 'Admin Test');
Session::set('user_rol', 'administrador');
Session::set('sucursal_id', 1);

ob_start();
$controller = new PedidosController();
$controller->ultimoCliente(1);
