<?php

namespace Tests\Performance;

require_once __DIR__ . '/../../vendor/autoload.php';

use App\Models\Venta;
use App\Core\Database;
use ReflectionClass;

class MockDatabase {
    public $calls = [];
    public $queries = [];

    public function beginTransaction() { $this->calls[] = 'beginTransaction'; }
    public function commit() { $this->calls[] = 'commit'; }
    public function rollback() { $this->calls[] = 'rollback'; }

    public function fetchOne($sql, $params = []) {
        $this->calls[] = 'fetchOne';
        $this->queries[] = ['sql' => $sql, 'params' => $params];

        // Simular respuesta para validación de stock
        if (strpos($sql, 'SELECT stock_actual FROM productos WHERE id = ?') !== false) {
            return ['stock_actual' => 100];
        }
        // Simular respuesta para validación de stock por lotes (WHERE IN)
        if (strpos($sql, 'WHERE id IN') !== false) {
            $ids = $params;
            $results = [];
            foreach ($ids as $id) {
                $results[] = ['id' => $id, 'stock_actual' => 100];
            }
            return $results; // fetchAll would be used here usually
        }
        return ['id' => 1];
    }

    public function fetchAll($sql, $params = []) {
        $this->calls[] = 'fetchAll';
        $this->queries[] = ['sql' => $sql, 'params' => $params];

        if (strpos($sql, 'WHERE id IN') !== false) {
            $ids = $params;
            $results = [];
            foreach ($ids as $id) {
                $results[] = ['id' => $id, 'stock_actual' => 100];
            }
            return $results;
        }
        return [];
    }

    public function execute($sql, $params = []) {
        $this->calls[] = 'execute';
        $this->queries[] = ['sql' => $sql, 'params' => $params];
        return 1;
    }

    public function lastInsertId() {
        return 999;
    }
}

class VentaOptimizationTest {
    public function run() {
        echo "Starting Performance Test for Venta::createWithProducts\n";
        echo "======================================================\n";

        $mockDb = new MockDatabase();

        // Create Venta instance without triggering constructor that connects to DB
        $reflection = new ReflectionClass(Venta::class);
        $venta = $reflection->newInstanceWithoutConstructor();

        // Inject mock database
        $dbProperty = $reflection->getProperty('db');
        $dbProperty->setAccessible(true);
        $dbProperty->setValue($venta, $mockDb);

        // Also set table property as it might be needed
        $tableProperty = $reflection->getProperty('table');
        $tableProperty->setAccessible(true);
        $tableProperty->setValue($venta, 'ventas');

        $productos = [
            ['id_producto' => 1, 'cantidad' => 2, 'precio_unitario' => 10, 'subtotal' => 20],
            ['id_producto' => 2, 'cantidad' => 1, 'precio_unitario' => 15, 'subtotal' => 15],
            ['id_producto' => 3, 'cantidad' => 5, 'precio_unitario' => 5, 'subtotal' => 25],
            ['id_producto' => 4, 'cantidad' => 3, 'precio_unitario' => 20, 'subtotal' => 60],
            ['id_producto' => 5, 'cantidad' => 1, 'precio_unitario' => 50, 'subtotal' => 50],
        ];

        $pagos = [
            ['metodo' => 'efectivo', 'monto' => 100, 'referencia' => ''],
            ['metodo' => 'transferencia', 'monto' => 70, 'referencia' => '123456'],
        ];

        $venta_data = [
            'id_negocio' => 1,
            'id_sucursal' => 1,
            'id_usuario' => 1,
            'id_cliente' => 1,
            'total' => 170,
            'metodo_pago' => 'mixto',
            'estado' => 'completada',
            'fecha_venta' => date('Y-m-d H:i:s')
        ];

        echo "Simulating sale with 5 products and 2 payments...\n";

        try {
            $venta->createWithProducts($venta_data, $productos, $pagos);

            $totalCalls = count($mockDb->calls);
            echo "Total Database Calls: $totalCalls\n";

            $fetchOneCount = count(array_filter($mockDb->calls, function($c) { return $c === 'fetchOne'; }));
            $fetchAllCount = count(array_filter($mockDb->calls, function($c) { return $c === 'fetchAll'; }));
            $executeCount = count(array_filter($mockDb->calls, function($c) { return $c === 'execute'; }));

            echo "- fetchOne: $fetchOneCount\n";
            echo "- fetchAll: $fetchAllCount\n";
            echo "- execute: $executeCount\n";

            if ($totalCalls <= 6) {
                echo "\nRESULT: Optimization verified! ($totalCalls calls)\n";
            } else {
                echo "\nRESULT: Optimization failed or more calls than expected ($totalCalls).\n";
            }

        } catch (\Exception $e) {
            echo "Error during test: " . $e->getMessage() . "\n";
        }

        echo "\nTesting edge case: Empty products list\n";
        try {
            $venta->createWithProducts($venta_data, [], $pagos);
            echo "FAIL: Should have thrown exception for empty products\n";
        } catch (\Exception $e) {
            echo "PASS: Caught expected exception: " . $e->getMessage() . "\n";
        }
    }
}

$test = new VentaOptimizationTest();
$test->run();
