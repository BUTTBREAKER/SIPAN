<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Models\Cliente;

class MockDatabase
{
    public static $lastQuery = '';
    public static $lastParams = [];

    public function fetchAll($sql, $params = [])
    {
        self::$lastQuery = $sql;
        self::$lastParams = $params;
        return [
            [
                'id' => 1,
                'nombre' => 'Juan',
                'apellido' => 'Pérez',
                'total_pedidos' => 2,
                'total_comprado' => 150.00,
                'total_pagado' => 100.00,
                'total_deuda' => 50.00
            ]
        ];
    }
}

class TestCliente extends Cliente
{
    public function __construct($db)
    {
        $this->db = $db;
        $this->table = 'clientes';
    }
}

function verifyClienteResumen()
{
    echo "--- Testing Cliente::getWithResumen Optimization ---\n";

    $mockDb = new MockDatabase();
    $clienteModel = new TestCliente($mockDb);

    // Test 1: Query with sucursal_id filter
    echo "Test 1: Branch-filtered query...\n";
    $clienteModel->getWithResumen(1);

    if (strpos(MockDatabase::$lastQuery, 'v_resumen_pedidos_cliente') !== false) {
        echo "❌ Test 1 Failed: Query still references missing view v_resumen_pedidos_cliente!\n";
        exit(1);
    }

    if (strpos(MockDatabase::$lastQuery, 'COALESCE(SUM(p.total), 0)') === false) {
        echo "❌ Test 1 Failed: COALESCE missing for aggregate SUM columns!\n";
        exit(1);
    }

    if (MockDatabase::$lastParams !== [1]) {
        echo "❌ Test 1 Failed: Parameter binding mismatch. Expected [1], got: " . print_r(MockDatabase::$lastParams, true) . "\n";
        exit(1);
    }

    echo "✅ Test 1 Passed: Branch-filtered query uses COALESCE and binds parameters correctly.\n";

    // Test 2: Global query without sucursal_id filter
    echo "\nTest 2: Global query without sucursal_id...\n";
    $clienteModel->getWithResumen(null);

    if (strpos(MockDatabase::$lastQuery, 'v_resumen_pedidos_cliente') !== false) {
        echo "❌ Test 2 Failed: Global query attempts to select from missing view v_resumen_pedidos_cliente!\n";
        exit(1);
    }

    if (strpos(MockDatabase::$lastQuery, 'WHERE c.id_sucursal') !== false) {
        echo "❌ Test 2 Failed: Global query includes unexpected WHERE clause!\n";
        exit(1);
    }

    if (!empty(MockDatabase::$lastParams)) {
        echo "❌ Test 2 Failed: Expected empty parameters for global query, got: " . print_r(MockDatabase::$lastParams, true) . "\n";
        exit(1);
    }

    echo "✅ Test 2 Passed: Global query successfully generated without view dependency.\n";
    echo "\n✨ All Cliente::getWithResumen optimizations verified successfully!\n";
}

verifyClienteResumen();
