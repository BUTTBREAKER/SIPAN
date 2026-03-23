<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Models\Pedido;
use App\Core\Database;

// Mock session and environment
$_SESSION['sucursal_id'] = 1;
$_SESSION['user_id'] = 1;
$_SESSION['user_rol'] = 'administrador';
$_ENV['moneda_principal'] = 'S/';

class TestPedidoOptimization {
    private $pedidoModel;
    private $queries = [];

    public function __construct() {
        // Create model without constructor to avoid DB connection
        $this->pedidoModel = (new ReflectionClass(Pedido::class))->newInstanceWithoutConstructor();

        // Mock DB object
        $dbMock = $this->createMockDB();

        // Inject mock DB into model
        $ref = new ReflectionClass(Pedido::class);
        $prop = $ref->getProperty('db');
        $prop->setAccessible(true);
        $prop->setValue($this->pedidoModel, $dbMock);

        // Set table name manually since constructor was skipped
        $tableProp = $ref->getProperty('table');
        $tableProp->setAccessible(true);
        $tableProp->setValue($this->pedidoModel, 'pedidos');
    }

    private function createMockDB() {
        $tester = $this;
        return new class($tester) {
            private $tester;
            public function __construct($tester) { $this->tester = $tester; }
            public function fetchAll($sql, $params = []) {
                $this->tester->recordQuery($sql, $params);
                return []; // Return empty result
            }
            public function fetchOne($sql, $params = []) {
                $this->tester->recordQuery($sql, $params);
                return ['total' => 5]; // Return dummy result
            }
            public function execute($sql, $params = []) {
                $this->tester->recordQuery($sql, $params);
                return true;
            }
        };
    }

    public function recordQuery($sql, $params) {
        $this->queries[] = ['sql' => $sql, 'params' => $params];
    }

    public function run() {
        echo "Starting verification of Pedido optimizations (Mocked DB)...\n";

        try {
            $this->testCounts();
            $this->testSargability();
            $this->testActiveFilter();
            echo "\n✅ All optimizations verified successfully!\n";
        } catch (\Exception $e) {
            echo "\n❌ Verification failed: " . $e->getMessage() . "\n";
            echo "Queries captured:\n";
            print_r($this->queries);
            exit(1);
        }
    }

    private function testCounts() {
        echo "Testing Pedido::getCountsBySucursal()... ";
        $this->queries = [];
        $this->pedidoModel->getCountsBySucursal(1);

        $lastQuery = end($this->queries);
        if (strpos($lastQuery['sql'], 'GROUP BY estado_pedido') === false) {
            throw new \Exception("getCountsBySucursal did not use GROUP BY correctly");
        }
        if ($lastQuery['params'] !== [1]) {
            throw new \Exception("getCountsBySucursal used wrong params: " . print_r($lastQuery['params'], true));
        }
        echo "OK\n";
    }

    private function testSargability() {
        echo "Testing SARGable query logic... ";
        $this->queries = [];

        $reflection = new ReflectionClass($this->pedidoModel);
        $method = $reflection->getMethod('generarNumeroPedido');
        $method->setAccessible(true);
        $method->invoke($this->pedidoModel);

        $lastQuery = end($this->queries);
        if (strpos($lastQuery['sql'], 'fecha_pedido >= CURDATE()') === false) {
            throw new \Exception("generarNumeroPedido is NOT SARGable. SQL: " . $lastQuery['sql']);
        }
        echo "OK\n";
    }

    private function testActiveFilter() {
        echo "Testing active status filtering and IN clause... ";
        $this->queries = [];
        $activeStatuses = ['pendiente', 'en_proceso', 'en_camino'];
        $this->pedidoModel->getWithDetails(1, $activeStatuses);

        $lastQuery = end($this->queries);
        if (strpos($lastQuery['sql'], 'IN (?,?,?)') === false) {
            throw new \Exception("getWithDetails did not use IN clause correctly for array statuses. SQL: " . $lastQuery['sql']);
        }

        $expectedParams = array_merge([1], $activeStatuses);
        if ($lastQuery['params'] !== $expectedParams) {
             throw new \Exception("getWithDetails used wrong params for IN clause: " . print_r($lastQuery['params'], true));
        }
        echo "OK\n";
    }
}

$tester = new TestPedidoOptimization();
$tester->run();
