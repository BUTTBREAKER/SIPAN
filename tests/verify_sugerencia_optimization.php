<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Models\SugerenciaCompra;

// Mock Database
class MockDatabase {
    public $lastQuery = '';
    public function fetchAll($sql, $params = []) {
        $this->lastQuery = $sql;
        return [
            [
                'id' => 1,
                'item_nombre' => 'Harina',
                'stock_actual' => 10,
                'stock_minimo' => 20,
                'tipo' => 'insumo',
                'id_item' => 1,
                'unidad_medida' => 'kg',
                'precio_unitario' => 1.5,
                'prioridad' => 'alta',
                'fecha_sugerencia' => '2023-01-01 10:00:00',
                'estado' => 'pendiente',
                'razon' => 'Stock bajo'
            ]
        ];
    }
}

class MockSugerencia extends SugerenciaCompra {
    public function __construct($db) {
        $this->db = $db;
        $this->table = 'sugerencias_compra';
    }
}

function verifyOptimization() {
    $db = new MockDatabase();
    $model = new MockSugerencia($db);

    echo "Running SugerenciaCompra::getWithDetails()...\n";
    $results = $model->getWithDetails(1);

    echo "Last Query:\n" . $db->lastQuery . "\n\n";

    if (strpos($db->lastQuery, 'LEFT JOIN productos') !== false) {
        throw new Exception("Optimization failed: LEFT JOIN productos still present in query.");
    }

    if (strpos($db->lastQuery, 'INNER JOIN insumos') === false) {
        throw new Exception("Optimization failed: INNER JOIN insumos missing from query.");
    }

    if (strpos($db->lastQuery, 'COALESCE') !== false) {
        throw new Exception("Optimization failed: COALESCE still present in query.");
    }

    $requiredFields = ['item_nombre', 'tipo', 'stock_actual', 'stock_minimo', 'id_item', 'unidad_medida', 'precio_unitario', 'razon', 'prioridad', 'fecha_sugerencia', 'estado'];
    foreach ($requiredFields as $field) {
        if (!isset($results[0][$field])) {
            throw new Exception("Functional regression: $field missing from results.");
        }
    }

    echo "✅ SugerenciaCompra::getWithDetails optimization verified successfully!\n";
}

try {
    verifyOptimization();
} catch (Exception $e) {
    echo "❌ Verification failed: " . $e->getMessage() . "\n";
    exit(1);
}
