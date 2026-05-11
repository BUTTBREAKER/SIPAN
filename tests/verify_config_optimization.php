<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Models\Configuracion;

// Mock Database
class MockDatabase {
    public $queryCount = 0;
    public $queries = [];
    public $data = [];

    public function fetchOne($sql, $params = []) {
        $this->queryCount++;
        $this->queries[] = ['sql' => $sql, 'params' => $params];
        $key = $params[0] ?? null;
        return $this->data[$key] ?? null;
    }

    public function execute($sql, $params = []) {
        $this->queryCount++;
        $this->queries[] = ['sql' => $sql, 'params' => $params];
        return 1;
    }
}

// Subclass to inject mock DB
class OptimizedConfiguracion extends Configuracion {
    public function __construct($db) {
        $this->db = $db;
    }
}

function testConfigCaching() {
    $mockDb = new MockDatabase();
    $mockDb->data['test_key'] = ['valor' => 'initial_value'];

    $config = new OptimizedConfiguracion($mockDb);

    echo "--- Testing Configuracion::get() caching ---\n";

    $val1 = $config->get('test_key');
    $val2 = $config->get('test_key');

    if ($val1 === 'initial_value' && $val2 === 'initial_value') {
        echo "Values retrieved correctly.\n";
    } else {
        echo "❌ Values retrieved incorrectly: $val1, $val2\n";
    }

    if ($mockDb->queryCount === 1) {
        echo "✅ Cache works! Only 1 DB query for 2 get() calls.\n";
    } else {
        echo "❌ Cache failed! Got {$mockDb->queryCount} DB queries for 2 get() calls.\n";
    }

    echo "\n--- Testing Configuracion::set() cache update ---\n";
    $mockDb->queryCount = 0;
    $config->set('test_key', 'new_value');

    $val3 = $config->get('test_key');
    if ($val3 === 'new_value') {
        echo "✅ Set() updated cache correctly. Value: $val3\n";
    } else {
        echo "❌ Set() did not update cache correctly. Value: $val3\n";
    }

    // get() after set() should ideally not hit DB if we updated cache in set()
    // However, the current implementation of set() calls get() to check existence.
    // If we optimize set() to also use cache, we can reduce queries further.
}

try {
    // Note: This test will fail before optimization and should pass after.
    testConfigCaching();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
