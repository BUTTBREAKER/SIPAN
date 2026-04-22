<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Models\Configuracion;

// Mock Database to count queries
class MockDatabase {
    public $queryCount = 0;
    public $queries = [];

    public function fetchOne($sql, $params = []) {
        $this->queryCount++;
        $this->queries[] = ['sql' => $sql, 'params' => $params];

        if (strpos($sql, 'clave = ?') !== false) {
            if ($params[0] === 'tasa_bcv') {
                return ['valor' => 55.50, 'updated_at' => date('Y-m-d H:i:s')];
            }
            if ($params[0] === 'some_key') {
                return ['valor' => 'some_value'];
            }
        }
        return null;
    }

    public function execute($sql, $params = []) {
        $this->queryCount++;
        $this->queries[] = ['sql' => $sql, 'params' => $params];
        return 1;
    }
}

// Subclass to inject mock DB
class MockConfiguracion extends Configuracion {
    public function __construct($db) {
        $this->db = $db;
        $this->table = 'configuracion';
    }
}

function testConfigOptimization() {
    $mockDb = new MockDatabase();
    $config = new MockConfiguracion($mockDb);

    echo "--- Testing Configuracion::get() Request-Level Caching ---\n";

    // First call - should hit DB
    $val1 = $config->get('some_key');
    echo "First call 'some_key': $val1 (Query count: {$mockDb->queryCount})\n";

    // Second call - should hit cache
    $val2 = $config->get('some_key');
    echo "Second call 'some_key': $val2 (Query count: {$mockDb->queryCount})\n";

    if ($mockDb->queryCount === 1 && $val1 === $val2) {
        echo "✅ Configuracion::get() caching verified!\n";
    } else {
        echo "❌ Configuracion::get() caching failed! Query count: {$mockDb->queryCount}\n";
    }

    echo "\n--- Testing Configuracion::getTasaBCV() Request-Level Caching ---\n";
    $beforeCount = $mockDb->queryCount;

    // First call - should hit DB (and maybe set if expired, but we mocked updated_at to now)
    $rate1 = $config->getTasaBCV();
    echo "First call getTasaBCV: $rate1 (Query count since start of this test: " . ($mockDb->queryCount - $beforeCount) . ")\n";

    // Second call - should hit cache immediately
    $rate2 = $config->getTasaBCV();
    echo "Second call getTasaBCV: $rate2 (Query count since start of this test: " . ($mockDb->queryCount - $beforeCount) . ")\n";

    if (($mockDb->queryCount - $beforeCount) === 1 && $rate1 === $rate2) {
        echo "✅ Configuracion::getTasaBCV() caching verified!\n";
    } else {
        echo "❌ Configuracion::getTasaBCV() caching failed! Query count diff: " . ($mockDb->queryCount - $beforeCount) . "\n";
    }

    echo "\n--- Testing Cache Invalidation on set() ---\n";
    $config->set('some_key', 'new_value');
    $val3 = $config->get('some_key');
    echo "Value after set(): $val3\n";

    if ($val3 === 'new_value') {
        echo "✅ Cache invalidation/update verified!\n";
    } else {
        echo "❌ Cache invalidation/update failed!\n";
    }
}

try {
    testConfigOptimization();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
