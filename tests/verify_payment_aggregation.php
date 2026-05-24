<?php

namespace Tests;

// Mock del core de Database ANTES de cargar los modelos
namespace App\Core;
class Database {
    public static function getInstance() { return new \Tests\MockDatabase(); }
}

namespace Tests;

use App\Models\Venta;
use ReflectionProperty;

// Mock del core de Database
class MockDatabase {
    public $queries = [];
    public $responses = [];

    public static function getInstance() {
        return new self();
    }

    public function fetchAll($sql, $params = []) {
        $this->queries[] = ['sql' => $sql, 'params' => $params];
        return array_shift($this->responses) ?: [];
    }

    public function fetchOne($sql, $params = []) {
        $this->queries[] = ['sql' => $sql, 'params' => $params];
        return array_shift($this->responses) ?: null;
    }
}

// Cargar clases necesarias (simulación mínima)
require_once __DIR__ . '/../app/Models/BaseModel.php';
require_once __DIR__ . '/../app/Models/Venta.php';

function runTest() {
    echo "--- Testing Venta::getPaymentBreakdown Optimization ---\n";

    $mockDb = new MockDatabase();
    $ventaModel = new Venta();

    // Inyectar el mock de DB
    $reflection = new ReflectionProperty(Venta::class, 'db');
    $reflection->setAccessible(true);
    $reflection->setValue($ventaModel, $mockDb);

    // Configurar respuesta esperada
    $mockDb->responses[] = [
        ['metodo_pago' => 'efectivo_usd', 'total' => 150.50],
        ['metodo_pago' => 'tarjeta', 'total' => 300.00]
    ];

    $sucursal_id = 1;
    $fecha_inicio = '2023-10-01';
    $fecha_fin = '2023-10-31';

    $result = $ventaModel->getPaymentBreakdown($sucursal_id, $fecha_inicio, $fecha_fin);

    // Verificar SQL generado
    $lastQuery = $mockDb->queries[0];

    echo "SQL Query generated:\n" . $lastQuery['sql'] . "\n\n";

    $expectedParams = [
        1, '2023-10-01 00:00:00', '2023-10-31 23:59:59',
        1, '2023-10-01 00:00:00', '2023-10-31 23:59:59'
    ];

    if ($lastQuery['params'] === $expectedParams) {
        echo "✅ Parameters match expected values.\n";
    } else {
        echo "❌ Parameters mismatch!\n";
        print_r($lastQuery['params']);
        exit(1);
    }

    if (strpos($lastQuery['sql'], 'UNION ALL') !== false && strpos($lastQuery['sql'], 'GROUP BY metodo_pago') !== false) {
        echo "✅ SQL contains UNION ALL and GROUP BY aggregation.\n";
    } else {
        echo "❌ SQL structure is incorrect!\n";
        exit(1);
    }

    if (count($result) === 2 && $result[0]['total'] == 150.50) {
        echo "✅ Results are correctly returned.\n";
    } else {
        echo "❌ Results mismatch!\n";
        exit(1);
    }

    echo "--- Test Passed Successfully! ---\n";
}

runTest();
