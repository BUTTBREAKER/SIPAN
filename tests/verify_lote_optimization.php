<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Database;
use App\Models\Lote;

// Create Database instance with SQLite in-memory connection
$dbRef = new ReflectionClass(Database::class);
/** @var Database $db */
$db = $dbRef->newInstanceWithoutConstructor();

$pdo = new PDO('sqlite::memory:');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

$pdoProperty = new ReflectionProperty(Database::class, 'connection');
$pdoProperty->setAccessible(true);
$pdoProperty->setValue($db, $pdo);

// Create lotes table in SQLite
$pdo->exec("
    CREATE TABLE lotes (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        id_sucursal INTEGER,
        tipo TEXT,
        id_item INTEGER,
        codigo_lote TEXT,
        fecha_entrada TEXT,
        fecha_vencimiento TEXT,
        cantidad_inicial REAL,
        cantidad_actual REAL,
        costo_unitario REAL,
        estado TEXT DEFAULT 'activo',
        created_at TEXT DEFAULT CURRENT_TIMESTAMP
    )
");

// Inject Database instance into Lote
function createLoteModel($db): Lote
{
    $loteRef = new ReflectionClass(Lote::class);
    /** @var Lote $lote */
    $lote = $loteRef->newInstanceWithoutConstructor();
    $dbProperty = new ReflectionProperty(Lote::class, 'db');
    $dbProperty->setAccessible(true);
    $dbProperty->setValue($lote, $db);
    return $lote;
}

echo "=== TESTING LOTE::DESCONTARSTOCK OPTIMIZATION ===\n\n";

$loteModel = createLoteModel($db);

// 1. Test Single Lot Deduction
$pdo->exec("DELETE FROM lotes");
$pdo->exec("INSERT INTO lotes (id, id_sucursal, tipo, id_item, cantidad_actual, fecha_vencimiento, estado) VALUES (10, 1, 'insumo', 5, 50.0, '2025-12-31', 'activo')");

$pendiente1 = $loteModel->descontarStock('insumo', 5, 20.0, 1);

assert($pendiente1 === 0.0, 'Single lot deduction should leave 0 pending stock');

$row1 = $pdo->query("SELECT cantidad_actual, estado FROM lotes WHERE id = 10")->fetch();
assert((float)$row1['cantidad_actual'] === 30.0, 'Stock of lot 10 should be reduced to 30');
assert($row1['estado'] === 'activo', 'Lot 10 should remain active');

echo "✓ Test 1 Passed: Single-lot stock deduction correctly updated SQLite database.\n";

// 2. Test Multi-Lot Deduction
$pdo->exec("DELETE FROM lotes");
$pdo->exec("INSERT INTO lotes (id, id_sucursal, tipo, id_item, cantidad_actual, fecha_vencimiento, estado) VALUES
    (10, 1, 'insumo', 5, 50.0, '2025-06-01', 'activo'),
    (11, 1, 'insumo', 5, 30.0, '2025-07-01', 'activo'),
    (12, 1, 'insumo', 5, 20.0, '2025-08-01', 'activo')
");

// Deduct 70 units (spans Lot 10 [50] and Lot 11 [30])
$pendiente2 = $loteModel->descontarStock('insumo', 5, 70.0, 1);

assert($pendiente2 === 0.0, 'Multi lot deduction should leave 0 pending stock');

$row10 = $pdo->query("SELECT cantidad_actual, estado FROM lotes WHERE id = 10")->fetch();
assert((float)$row10['cantidad_actual'] === 0.0, 'Lot 10 should be exhausted (0.0)');
assert($row10['estado'] === 'agotado', 'Lot 10 status should be agotado');

$row11 = $pdo->query("SELECT cantidad_actual, estado FROM lotes WHERE id = 11")->fetch();
assert((float)$row11['cantidad_actual'] === 10.0, 'Lot 11 stock should be reduced to 10.0');
assert($row11['estado'] === 'activo', 'Lot 11 status should be activo');

$row12 = $pdo->query("SELECT cantidad_actual, estado FROM lotes WHERE id = 12")->fetch();
assert((float)$row12['cantidad_actual'] === 20.0, 'Lot 12 stock should be untouched (20.0)');
assert($row12['estado'] === 'activo', 'Lot 12 status should be activo');

echo "✓ Test 2 Passed: Multi-lot FIFO deduction updated all lots correctly in SQLite database.\n";

// 3. Test Insufficient Stock Deduction
$pdo->exec("DELETE FROM lotes");
$pdo->exec("INSERT INTO lotes (id, id_sucursal, tipo, id_item, cantidad_actual, fecha_vencimiento, estado) VALUES
    (10, 1, 'insumo', 5, 10.0, '2025-06-01', 'activo')
");

$pendiente3 = $loteModel->descontarStock('insumo', 5, 30.0, 1);
assert($pendiente3 === 20.0, 'Pending stock should be 20.0 when 30 is requested but only 10 exists');

$row10 = $pdo->query("SELECT cantidad_actual, estado FROM lotes WHERE id = 10")->fetch();
assert((float)$row10['cantidad_actual'] === 0.0, 'Lot 10 should be reduced to 0.0');
assert($row10['estado'] === 'agotado', 'Lot 10 status should be agotado');

echo "✓ Test 3 Passed: Insufficient stock correctly returned remaining pending amount.\n";

echo "\nALL LOTE OPTIMIZATION VERIFICATION TESTS PASSED SUCCESSFULLY!\n";
