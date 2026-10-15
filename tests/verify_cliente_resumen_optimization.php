<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Database;
use App\Models\Cliente;

function verifyClienteResumenOptimization(): void
{
    echo "=== Verifying Cliente::getWithResumen Optimization ===\n";

    // 1. Create SQLite in-memory PDO
    $pdo = new PDO('sqlite::memory:', null, null, [
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);

    // Create tables
    $pdo->exec("CREATE TABLE clientes (
        id INTEGER PRIMARY KEY,
        id_sucursal INTEGER,
        nombre TEXT
    )");

    $pdo->exec("CREATE TABLE pedidos (
        id INTEGER PRIMARY KEY,
        id_cliente INTEGER,
        total REAL,
        monto_pagado REAL,
        monto_deuda REAL
    )");

    // Insert test data
    $pdo->exec("INSERT INTO clientes (id, id_sucursal, nombre) VALUES (1, 10, 'Alice')");
    $pdo->exec("INSERT INTO clientes (id, id_sucursal, nombre) VALUES (2, 10, 'Bob')");
    $pdo->exec("INSERT INTO clientes (id, id_sucursal, nombre) VALUES (3, 20, 'Charlie')");

    // Alice has orders, Bob has 0 orders, Charlie belongs to sucursal 20
    $pdo->exec("INSERT INTO pedidos (id, id_cliente, total, monto_pagado, monto_deuda) VALUES (100, 1, 150.0, 100.0, 50.0)");
    $pdo->exec("INSERT INTO pedidos (id, id_cliente, total, monto_pagado, monto_deuda) VALUES (101, 1, 50.0, 50.0, 0.0)");

    // 2. Instantiate Database singleton using Reflection without constructor
    $dbRef = new ReflectionClass(Database::class);
    $dbInstance = $dbRef->newInstanceWithoutConstructor();

    $connProp = $dbRef->getProperty('connection');
    $connProp->setAccessible(true);
    $connProp->setValue($dbInstance, $pdo);

    $instProp = $dbRef->getProperty('instance');
    $instProp->setAccessible(true);
    $instProp->setValue(null, $dbInstance);

    // 3. Test Cliente model
    $clienteModel = new Cliente();

    // Test 1: getWithResumen(10)
    echo "Test 1: getWithResumen(10)...\n";
    $resultSucursal10 = $clienteModel->getWithResumen(10);

    if (count($resultSucursal10) !== 2) {
        throw new Exception("Expected 2 clients for sucursal 10, got " . count($resultSucursal10));
    }

    $alice = $resultSucursal10[0]; // Alice
    if ($alice['nombre'] !== 'Alice' || (int)$alice['total_pedidos'] !== 2 || (float)$alice['total_comprado'] !== 200.0) {
        throw new Exception("Alice totals incorrect: " . json_encode($alice));
    }

    $bob = $resultSucursal10[1]; // Bob
    if ($bob['nombre'] !== 'Bob' || (int)$bob['total_pedidos'] !== 0 || (float)$bob['total_comprado'] !== 0.0) {
        throw new Exception("Bob totals incorrect (COALESCE check failed): " . json_encode($bob));
    }

    echo "✅ Test 1 Passed! (Alice total = 200.0, Bob total = 0.0)\n\n";

    // Test 2: getWithResumen(null)
    echo "Test 2: getWithResumen(null)...\n";
    $resultAll = $clienteModel->getWithResumen(null);

    if (count($resultAll) !== 3) {
        throw new Exception("Expected 3 clients total when sucursal_id is null, got " . count($resultAll));
    }

    echo "✅ Test 2 Passed! (All 3 clients returned without error)\n\n";
    echo "ALL TESTS PASSED SUCCESSFULLY! ⚡\n";
}

try {
    verifyClienteResumenOptimization();
} catch (Throwable $t) {
    echo "❌ Verification Failed: " . $t->getMessage() . "\n";
    echo $t->getTraceAsString() . "\n";
    exit(1);
}
