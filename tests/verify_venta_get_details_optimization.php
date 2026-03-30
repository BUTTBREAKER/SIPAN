<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Models\Venta;

// Mock Database
class MockDatabase {
    public $lastQuery = '';
    public function fetchAll($sql, $params = []) {
        $this->lastQuery = $sql;
        return [
            [
                'id' => 1,
                'total' => 100,
                'cliente_nombre' => 'Juan Perez',
                'usuario_nombre' => 'Admin User',
                'fecha_venta' => '2023-01-01 10:00:00'
            ]
        ];
    }
    public function beginTransaction() {}
    public function commit() {}
    public function rollback() {}
    public function execute($sql, $params = []) {}
}

class MockVenta extends Venta {
    public function __construct($db) {
        $this->db = $db;
        $this->table = 'ventas';
    }
}

function verifyOptimization() {
    $db = new MockDatabase();
    $model = new MockVenta($db);

    echo "Running Venta::getWithDetails()...\n";
    $results = $model->getWithDetails(1);

    echo "Last Query: " . $db->lastQuery . "\n";

    if (strpos($db->lastQuery, 'LEFT JOIN venta_productos') !== false) {
        throw new Exception("Optimization failed: LEFT JOIN venta_productos still present in query.");
    }

    if (strpos($db->lastQuery, 'COUNT(vp.id)') !== false) {
        throw new Exception("Optimization failed: COUNT(vp.id) still present in query.");
    }

    if (strpos($db->lastQuery, 'GROUP BY') !== false) {
        throw new Exception("Optimization failed: GROUP BY still present in query.");
    }

    if (!isset($results[0]['cliente_nombre'])) {
        throw new Exception("Functional regression: cliente_nombre missing from results.");
    }

    echo "✅ Venta::getWithDetails optimization verified successfully!\n";
}

try {
    verifyOptimization();
} catch (Exception $e) {
    echo "❌ Verification failed: " . $e->getMessage() . "\n";
    exit(1);
}
