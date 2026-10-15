<?php

namespace Tests;

require_once __DIR__ . '/../vendor/autoload.php';

use App\Models\Cliente;
use App\Core\Database;
use PDO;

class VerifyClienteResumenOptimization
{
    public function run()
    {
        echo "Testing Cliente::getWithResumen optimization...\n";

        $filePath = __DIR__ . '/../app/Models/Cliente.php';
        $content = file_get_contents($filePath);

        // 1. Verify that v_resumen_pedidos_cliente view is no longer referenced
        if (strpos($content, 'v_resumen_pedidos_cliente') === false) {
            echo "✅ SUCCESS: Non-existent view v_resumen_pedidos_cliente is no longer referenced.\n";
        } else {
            echo "❌ FAILURE: v_resumen_pedidos_cliente is still present in Cliente.php\n";
            exit(1);
        }

        // 2. Verify COALESCE usage in SQL
        if (
            strpos($content, 'COALESCE(SUM(p.total), 0) as total_comprado') !== false &&
            strpos($content, 'COALESCE(SUM(p.monto_pagado), 0) as total_pagado') !== false &&
            strpos($content, 'COALESCE(SUM(p.monto_deuda), 0) as total_deuda') !== false
        ) {
            echo "✅ SUCCESS: Query uses COALESCE to ensure non-null numeric returns for sums.\n";
        } else {
            echo "❌ FAILURE: Query does not properly use COALESCE for aggregated fields.\n";
            exit(1);
        }

        // 3. Set up in-memory SQLite database to test query execution and output values
        $pdo = new PDO('sqlite::memory:', null, null, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);

        // Create mock tables
        $pdo->exec("CREATE TABLE clientes (
            id INTEGER PRIMARY KEY,
            id_sucursal INTEGER,
            nombre TEXT,
            apellido TEXT
        )");

        $pdo->exec("CREATE TABLE pedidos (
            id INTEGER PRIMARY KEY,
            id_cliente INTEGER,
            total REAL,
            monto_pagado REAL,
            monto_deuda REAL
        )");

        // Insert sample data
        // Client 1 has 2 orders
        $pdo->exec("INSERT INTO clientes VALUES (1, 1, 'Juan', 'Perez')");
        $pdo->exec("INSERT INTO pedidos VALUES (101, 1, 100.0, 60.0, 40.0)");
        $pdo->exec("INSERT INTO pedidos VALUES (102, 1, 150.0, 150.0, 0.0)");

        // Client 2 has 0 orders
        $pdo->exec("INSERT INTO clientes VALUES (2, 1, 'Maria', 'Gomez')");

        // Client 3 belongs to sucursal 2
        $pdo->exec("INSERT INTO clientes VALUES (3, 2, 'Carlos', 'Lopez')");

        // Instanciate Database object without constructor using Reflection
        $dbRef = new \ReflectionClass(Database::class);
        /** @var Database $dbInstance */
        $dbInstance = $dbRef->newInstanceWithoutConstructor();
        $connProp = $dbRef->getProperty('connection');
        $connProp->setAccessible(true);
        $connProp->setValue($dbInstance, $pdo);

        // Instantiate Cliente model without constructor using Reflection
        $clienteRef = new \ReflectionClass(Cliente::class);
        /** @var Cliente $clienteModel */
        $clienteModel = $clienteRef->newInstanceWithoutConstructor();

        $tableProp = $clienteRef->getProperty('table');
        $tableProp->setAccessible(true);
        $tableProp->setValue($clienteModel, 'clientes');

        $dbProp = $clienteRef->getProperty('db');
        $dbProp->setAccessible(true);
        $dbProp->setValue($clienteModel, $dbInstance);

        // Test global getWithResumen()
        $resGlobal = $clienteModel->getWithResumen();
        if (count($resGlobal) !== 3) {
            echo "❌ FAILURE: Expected 3 clients in global result, got " . count($resGlobal) . "\n";
            exit(1);
        }

        // Test branch-filtered getWithResumen(1)
        $resSucursal1 = $clienteModel->getWithResumen(1);
        if (count($resSucursal1) !== 2) {
            echo "❌ FAILURE: Expected 2 clients for sucursal 1, got " . count($resSucursal1) . "\n";
            exit(1);
        }

        // Verify aggregated values for client with orders (Juan Perez)
        $juan = array_values(array_filter($resSucursal1, fn($c) => $c['id'] == 1))[0];
        if ($juan['total_pedidos'] != 2 || $juan['total_comprado'] != 250.0 || $juan['total_pagado'] != 210.0 || $juan['total_deuda'] != 40.0) {
            echo "❌ FAILURE: Aggregated values for Juan Perez incorrect: " . print_r($juan, true) . "\n";
            exit(1);
        }

        // Verify non-null COALESCE zero values for client without orders (Maria Gomez)
        $maria = array_values(array_filter($resSucursal1, fn($c) => $c['id'] == 2))[0];
        if ($maria['total_pedidos'] != 0 || $maria['total_comprado'] != 0 || $maria['total_pagado'] != 0 || $maria['total_deuda'] != 0) {
            echo "❌ FAILURE: Non-null COALESCE zero values for Maria Gomez incorrect: " . print_r($maria, true) . "\n";
            exit(1);
        }

        echo "✅ SUCCESS: In-memory database test confirmed accurate aggregations and non-null defaults for clients without orders!\n";
        echo "All verification checks passed successfully!\n";
    }
}

$test = new VerifyClienteResumenOptimization();
$test->run();
