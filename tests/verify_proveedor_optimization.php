<?php

/**
 * Test: Proveedor Optimization Verification
 * Verifies batch multi-row INSERT in addInsumos and NOT EXISTS SARGable query in getInsumosSinProveedor.
 */

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Database;
use App\Models\Proveedor;

function createMockDatabase(): Database
{
    $dbReflection = new ReflectionClass(Database::class);
    $database = $dbReflection->newInstanceWithoutConstructor();

    $pdo = new PDO('sqlite::memory:');
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // Create schema
    $pdo->exec("
        CREATE TABLE insumos (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            nombre TEXT NOT NULL,
            id_sucursal INTEGER NOT NULL,
            unidad_medida TEXT DEFAULT 'kg',
            stock_actual REAL DEFAULT 0,
            stock_minimo REAL DEFAULT 0
        );

        CREATE TABLE proveedor_insumos (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            id_proveedor INTEGER NOT NULL,
            id_insumo INTEGER NOT NULL,
            precio REAL DEFAULT 0,
            tiempo_entrega INTEGER DEFAULT NULL
        );
    ");

    $connProp = $dbReflection->getProperty('connection');
    $connProp->setAccessible(true);
    $connProp->setValue($database, $pdo);

    $instanceProp = $dbReflection->getProperty('instance');
    $instanceProp->setAccessible(true);
    $instanceProp->setValue(null, $database);

    return $database;
}

function verify_proveedor_optimizations()
{
    $db = createMockDatabase();
    $proveedorModel = new Proveedor();

    echo "--- Test 1: Proveedor::addInsumos (Batch Multi-row INSERT) ---\n";

    // Insert mock insumos into SQLite
    $pdo = $db->getConnection();
    $pdo->exec("INSERT INTO insumos (id, nombre, id_sucursal) VALUES (101, 'Harina', 1), (102, 'Azúcar', 1), (103, 'Sal', 1)");

    $insumosToAdd = [
        ['id_insumo' => 101, 'precio' => 12.50, 'tiempo_entrega' => 3],
        ['id_insumo' => 102, 'precio' => 20.00, 'tiempo_entrega' => 5],
        ['id_insumo' => 103, 'precio' => 8.00, 'tiempo_entrega' => null]
    ];

    $proveedorModel->addInsumos(5, $insumosToAdd);

    $stmt = $pdo->query("SELECT * FROM proveedor_insumos WHERE id_proveedor = 5 ORDER BY id_insumo ASC");
    $rows = $stmt->fetchAll();

    if (count($rows) !== 3) {
        echo "❌ FAIL: Expected 3 inserted rows in proveedor_insumos, got " . count($rows) . "\n";
        exit(1);
    }

    if ($rows[0]['id_insumo'] != 101 || $rows[0]['precio'] != 12.50 || $rows[0]['tiempo_entrega'] != 3) {
        echo "❌ FAIL: Row 0 values incorrect.\n";
        var_dump($rows[0]);
        exit(1);
    }

    echo "✅ PASS: Proveedor::addInsumos correctly persisted all insumos in batch.\n";

    echo "\n--- Test 2: Proveedor::getInsumosSinProveedor (NOT EXISTS Query) ---\n";

    // Insumo 101, 102 are linked to supplier 5. Insumo 103 is not linked to any supplier.
    // Let's add insumo 104 without supplier
    $pdo->exec("INSERT INTO insumos (id, nombre, id_sucursal) VALUES (104, 'Mantequilla', 1)");

    // Clear proveedor_insumos and re-add only 101 and 102
    $pdo->exec("DELETE FROM proveedor_insumos");
    $proveedorModel->addInsumos(5, [
        ['id_insumo' => 101, 'precio' => 12.50],
        ['id_insumo' => 102, 'precio' => 20.00]
    ]);

    // Now getInsumosSinProveedor for sucursal 1 should return 103 (Sal) and 104 (Mantequilla)
    $unassignedInsumos = $proveedorModel->getInsumosSinProveedor(1);

    if (count($unassignedInsumos) !== 2) {
        echo "❌ FAIL: Expected 2 unassigned insumos, got " . count($unassignedInsumos) . "\n";
        var_dump($unassignedInsumos);
        exit(1);
    }

    $names = array_column($unassignedInsumos, 'nombre');
    if (!in_array('Sal', $names) || !in_array('Mantequilla', $names)) {
        echo "❌ FAIL: Incorrect unassigned insumos returned.\n";
        var_dump($names);
        exit(1);
    }

    // Verify method SQL uses NOT EXISTS
    $reflection = new ReflectionMethod(Proveedor::class, 'getInsumosSinProveedor');
    $filename = $reflection->getFileName();
    $startLine = $reflection->getStartLine();
    $endLine = $reflection->getEndLine();
    $source = implode('', array_slice(file($filename), $startLine - 1, $endLine - $startLine + 1));

    if (strpos($source, 'NOT EXISTS') === false) {
        echo "❌ FAIL: getInsumosSinProveedor method code does not contain NOT EXISTS.\n";
        exit(1);
    }

    if (strpos($source, 'GROUP BY') !== false || strpos($source, 'HAVING') !== false) {
        echo "❌ FAIL: getInsumosSinProveedor method code still contains GROUP BY or HAVING.\n";
        exit(1);
    }

    echo "✅ PASS: Proveedor::getInsumosSinProveedor correctly filters using SARGable NOT EXISTS query.\n";

    echo "\nSUCCESS: All Proveedor optimizations verified successfully!\n";
}

verify_proveedor_optimizations();
