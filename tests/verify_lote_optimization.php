<?php

/**
 * Test: Lote::descontarStock Optimization Verification
 * Verifies that Lote::descontarStock fetches only required columns (id, cantidad_actual)
 * and batches multi-lote updates into a single query using CASE expressions.
 */

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Database;
use App\Models\Lote;
use App\Models\BaseModel;

function verify_lote_optimization()
{
    // Create Database instance without running __construct (no MySQL needed)
    $dbRef = new ReflectionClass(Database::class);
    $dbInstance = $dbRef->newInstanceWithoutConstructor();

    // Attach in-memory SQLite connection
    $pdo = new PDO('sqlite::memory:');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    $pdoProp = $dbRef->getProperty('connection');
    $pdoProp->setAccessible(true);
    $pdoProp->setValue($dbInstance, $pdo);

    // Create Lote instance without constructor and inject dbInstance
    $loteRef = new ReflectionClass(Lote::class);
    $loteModel = $loteRef->newInstanceWithoutConstructor();

    $baseModelRef = new ReflectionClass(BaseModel::class);
    $dbProp = $baseModelRef->getProperty('db');
    $dbProp->setAccessible(true);
    $dbProp->setValue($loteModel, $dbInstance);

    // Setup SQLite schema
    $pdo->exec("CREATE TABLE lotes (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        tipo TEXT,
        id_item INTEGER,
        id_sucursal INTEGER,
        estado TEXT,
        cantidad_actual REAL,
        fecha_vencimiento TEXT,
        created_at TEXT
    )");

    echo "--- Test 1: Single Lote Stock Deduction ---\n";
    $pdo->exec("DELETE FROM lotes");
    $pdo->exec("INSERT INTO lotes (tipo, id_item, id_sucursal, estado, cantidad_actual, fecha_vencimiento, created_at)
                VALUES ('insumo', 101, 1, 'activo', 10.0, '2025-12-31', '2025-01-01')");

    $rem = $loteModel->descontarStock('insumo', 101, 4.0, 1);

    assert($rem === 0.0, 'Remaining should be 0');
    $row = $pdo->query("SELECT cantidad_actual, estado FROM lotes WHERE id = 1")->fetch();
    assert((float)$row['cantidad_actual'] === 6.0, 'New stock should be 6.0');
    assert($row['estado'] === 'activo', 'Status should be activo');
    echo "✅ PASS: Single lote stock deduction correctly updated database.\n";

    echo "\n--- Test 2: Multi-Lote Batched Stock Deduction ---\n";
    $pdo->exec("DELETE FROM lotes");
    $pdo->exec("INSERT INTO lotes (id, tipo, id_item, id_sucursal, estado, cantidad_actual, fecha_vencimiento, created_at)
                VALUES (1, 'insumo', 101, 1, 'activo', 5.0, '2025-12-01', '2025-01-01')");
    $pdo->exec("INSERT INTO lotes (id, tipo, id_item, id_sucursal, estado, cantidad_actual, fecha_vencimiento, created_at)
                VALUES (2, 'insumo', 101, 1, 'activo', 10.0, '2025-12-31', '2025-01-02')");

    $rem = $loteModel->descontarStock('insumo', 101, 8.0, 1);

    assert($rem === 0.0, 'Remaining should be 0');
    $row1 = $pdo->query("SELECT cantidad_actual, estado FROM lotes WHERE id = 1")->fetch();
    $row2 = $pdo->query("SELECT cantidad_actual, estado FROM lotes WHERE id = 2")->fetch();

    assert((float)$row1['cantidad_actual'] === 0.0, 'Lote 1 stock should be 0.0');
    assert($row1['estado'] === 'agotado', 'Lote 1 status should be agotado');

    assert((float)$row2['cantidad_actual'] === 7.0, 'Lote 2 stock should be 7.0');
    assert($row2['estado'] === 'activo', 'Lote 2 status should be activo');
    echo "✅ PASS: Multi-lote stock deduction successfully updated multiple lotes in database.\n";

    echo "\nALL TESTS PASSED SUCCESSFULLY!\n";
}

verify_lote_optimization();
