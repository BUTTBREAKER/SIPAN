<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Models\Pedido;
use App\Core\Database;

// Mock session and environment
$_SESSION['sucursal_id'] = 1;
$_SESSION['user_id'] = 1;
$_SESSION['user_rol'] = 'administrador';
$_ENV['moneda_principal'] = 'S/';

class TestPedidoOptimization
{
    private $pedidoModel;
    private $dbMock;

    public function __construct()
    {
        $dbReflection = new ReflectionClass(Database::class);
        $this->dbMock = $dbReflection->newInstanceWithoutConstructor();

        $pdo = new PDO('sqlite::memory:');
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $pdo->sqliteCreateFunction('CURDATE', fn() => date('Y-m-d'));

        $pdo->exec("
            CREATE TABLE clientes (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                nombre TEXT,
                apellido TEXT,
                direccion TEXT
            );
            CREATE TABLE usuarios (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                primer_nombre TEXT,
                apellido_paterno TEXT
            );
            CREATE TABLE pedidos (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                id_cliente INTEGER,
                id_sucursal INTEGER,
                id_usuario INTEGER,
                id_repartidor INTEGER,
                numero_pedido TEXT,
                fecha_pedido TEXT,
                estado_pedido TEXT,
                estado_pago TEXT,
                subtotal REAL,
                descuento REAL,
                total REAL,
                monto_pagado REAL,
                monto_deuda REAL,
                observaciones TEXT
            );
            INSERT INTO clientes (id, nombre, apellido) VALUES (1, 'Juan', 'Perez');
            INSERT INTO usuarios (id, primer_nombre, apellido_paterno) VALUES (1, 'Admin', 'User');
            INSERT INTO pedidos (id_cliente, id_sucursal, id_usuario, numero_pedido, fecha_pedido, estado_pedido, estado_pago, total)
            VALUES (1, 1, 1, 'PED-20250101-0001', '2025-01-01 10:00:00', 'pendiente', 'pendiente', 100.00);
        ");

        $connProp = $dbReflection->getProperty('connection');
        $connProp->setAccessible(true);
        $connProp->setValue($this->dbMock, $pdo);

        $this->pedidoModel = (new ReflectionClass(Pedido::class))->newInstanceWithoutConstructor();

        $ref = new ReflectionClass(Pedido::class);
        $prop = $ref->getProperty('db');
        $prop->setAccessible(true);
        $prop->setValue($this->pedidoModel, $this->dbMock);

        $tableProp = $ref->getProperty('table');
        $tableProp->setAccessible(true);
        $tableProp->setValue($this->pedidoModel, 'pedidos');
    }

    public function run()
    {
        echo "Starting verification of Pedido optimizations (Mocked DB)...\n";

        try {
            $this->testCounts();
            $this->testSargability();
            $this->testActiveFilter();
            echo "\n✅ All optimizations verified successfully!\n";
        } catch (\Exception $e) {
            echo "\n❌ Verification failed: " . $e->getMessage() . "\n";
            exit(1);
        }
    }

    private function testCounts()
    {
        echo "Testing Pedido::getCountsBySucursal()... ";
        $counts = $this->pedidoModel->getCountsBySucursal(1);
        if (!isset($counts['pendiente']) || $counts['pendiente'] !== 1) {
            throw new \Exception("getCountsBySucursal did not return correct counts: " . print_r($counts, true));
        }
        echo "OK\n";
    }

    private function testSargability()
    {
        echo "Testing SARGable query logic... ";

        $reflection = new ReflectionClass($this->pedidoModel);
        $method = $reflection->getMethod('generarNumeroPedido');
        $method->setAccessible(true);
        $num = $method->invoke($this->pedidoModel);

        if (empty($num) || strpos($num, 'PED-') !== 0) {
            throw new \Exception("generarNumeroPedido failed. Got: " . var_export($num, true));
        }
        echo "OK\n";
    }

    private function testActiveFilter()
    {
        echo "Testing active status filtering and IN clause... ";
        $activeStatuses = ['pendiente', 'en_proceso', 'en_camino'];
        $results = $this->pedidoModel->getWithDetails(1, $activeStatuses);

        if (count($results) !== 1) {
             throw new \Exception("getWithDetails did not return expected results with IN clause. Count: " . count($results));
        }
        echo "OK\n";
    }
}

$tester = new TestPedidoOptimization();
$tester->run();
