<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Database;
use App\Models\Lote;

function verifyLoteOptimization()
{
    echo "=== Verifying Lote::descontarStock Optimization ===\n\n";

    // Setup SQLite in-memory Database via PDO
    $pdo = new PDO('sqlite::memory:');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // Create lotes table
    $pdo->exec("CREATE TABLE lotes (
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
        created_at TEXT
    )");

    // Inject PDO into App\Core\Database singleton using Reflection
    $dbReflection = new ReflectionClass(Database::class);
    $mockDbInstance = $dbReflection->newInstanceWithoutConstructor();

    $connProp = $dbReflection->getProperty('connection');
    $connProp->setAccessible(true);
    $connProp->setValue($mockDbInstance, $pdo);

    $instanceProp = $dbReflection->getProperty('instance');
    $instanceProp->setAccessible(true);
    $instanceProp->setValue(null, $mockDbInstance);

    $loteModel = new Lote();

    // Test 1: Single Lot Stock Deduction
    echo "1. Testing Single Lot Stock Deduction...\n";
    $pdo->exec("DELETE FROM lotes");
    $pdo->exec("INSERT INTO lotes (id_sucursal, tipo, id_item, codigo_lote, fecha_entrada, fecha_vencimiento, cantidad_inicial, cantidad_actual, costo_unitario, estado)
                VALUES (1, 'insumo', 1, 'LOTE-1', '2025-01-01', '2025-12-31', 20.0, 20.0, 10.0, 'activo')");

    $remaining1 = $loteModel->descontarStock('insumo', 1, 8.0, 1);

    assert($remaining1 === 0.0, "Expected remaining to be 0");

    $stmt = $pdo->query("SELECT cantidad_actual, estado FROM lotes WHERE id = 1");
    $row1 = $stmt->fetch();
    assert((float)$row1['cantidad_actual'] === 12.0, "Updated stock should be 12.0");
    assert($row1['estado'] === 'activo', "Status should remain 'activo'");
    echo "   [✓] Single lot stock updated to 12.0 and state remains 'activo'\n";

    // Test 2: Multi Lot Stock Deduction (Batch UPDATE)
    echo "\n2. Testing Multi-Lot Stock Deduction (Batch UPDATE)...\n";
    $pdo->exec("DELETE FROM lotes");
    $pdo->exec("INSERT INTO lotes (id, id_sucursal, tipo, id_item, codigo_lote, fecha_entrada, fecha_vencimiento, cantidad_inicial, cantidad_actual, costo_unitario, estado, created_at)
                VALUES (10, 1, 'insumo', 100, 'LOTE-10', '2025-01-01', '2025-06-01', 5.0, 5.0, 10.0, 'activo', '2025-01-01 00:00:00')");
    $pdo->exec("INSERT INTO lotes (id, id_sucursal, tipo, id_item, codigo_lote, fecha_entrada, fecha_vencimiento, cantidad_inicial, cantidad_actual, costo_unitario, estado, created_at)
                VALUES (11, 1, 'insumo', 100, 'LOTE-11', '2025-01-01', '2025-07-01', 10.0, 10.0, 10.0, 'activo', '2025-01-01 00:00:01')");

    // Deduct 12 units across Lot 10 (5.0 stock) and Lot 11 (10.0 stock)
    $remaining2 = $loteModel->descontarStock('insumo', 100, 12.0, 1);

    assert($remaining2 === 0.0, "Expected remaining to be 0");

    $stmt = $pdo->query("SELECT id, cantidad_actual, estado FROM lotes ORDER BY id ASC");
    $rows2 = $stmt->fetchAll();

    assert((float)$rows2[0]['cantidad_actual'] === 0.0, "Lot 10 stock should be 0.0");
    assert($rows2[0]['estado'] === 'agotado', "Lot 10 status should be 'agotado'");

    assert((float)$rows2[1]['cantidad_actual'] === 3.0, "Lot 11 stock should be 3.0");
    assert($rows2[1]['estado'] === 'activo', "Lot 11 status should be 'activo'");

    echo "   [✓] Lot 10 exhausted (stock 0.0, estado 'agotado')\n";
    echo "   [✓] Lot 11 partially deducted (stock 3.0, estado 'activo')\n";

    echo "\n✅ ALL LOTE OPTIMIZATION TESTS PASSED SUCCESSFULLY!\n";
}

verifyLoteOptimization();