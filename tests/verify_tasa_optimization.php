<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Models\Configuracion;

class MockDatabase {
    public $queriesCount = 0;
    public $tasaValue = 55.50;

    public function fetchOne($sql, $params = []) {
        $this->queriesCount++;
        return ['valor' => $this->tasaValue, 'updated_at' => date('Y-m-d H:i:s')];
    }

    public function execute($sql, $params = []) {
        return 1;
    }
}

class MockConfiguracion extends Configuracion {
    public function __construct($db) {
        $this->db = $db;
        $this->table = 'configuracion';
    }
}

function testTasaOptimization() {
    $mockDb = new MockDatabase();
    $configModel = new MockConfiguracion($mockDb);

    echo "--- Testing Tasa BCV Caching ---\n";

    // First call - should trigger query
    $tasa1 = $configModel->getTasaBCV();
    echo "Call 1: $tasa1 (Queries: {$mockDb->queriesCount})\n";

    // Second call - should NOT trigger query
    $tasa2 = $configModel->getTasaBCV();
    echo "Call 2: $tasa2 (Queries: {$mockDb->queriesCount})\n";

    if ($mockDb->queriesCount === 1) {
        echo "✅ Optimization verified: Only 1 query executed for multiple calls.\n";
    } else {
        echo "❌ Optimization failed: Expected 1 query, but executed {$mockDb->queriesCount}.\n";
        exit(1);
    }

    // Test that set() updates cache
    echo "\n--- Testing Cache Update via set() ---\n";
    $configModel->set('tasa_bcv', 60.00);
    $tasa3 = $configModel->getTasaBCV();
    echo "Call 3 after set(60): $tasa3\n";

    if ($tasa3 == 60.00) {
        echo "✅ Cache updated correctly via set().\n";
    } else {
        echo "❌ Cache update via set() failed. Got $tasa3.\n";
        exit(1);
    }
}

try {
    testTasaOptimization();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
