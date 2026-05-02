<?php

/**
 * Test: Configuracion Request-Level Caching Verification
 */

require_once __DIR__ . '/../app/Models/BaseModel.php';
require_once __DIR__ . '/../app/Models/Configuracion.php';

class MockDB {
    public $queryCount = 0;
    public $lastSql = '';
    public $lastParams = [];
    public $mockResult = null;

    public function fetchOne($sql, $params = []) {
        $this->queryCount++;
        $this->lastSql = $sql;
        $this->lastParams = $params;
        return $this->mockResult;
    }

    public function execute($sql, $params = []) {
        $this->queryCount++;
        $this->lastSql = $sql;
        $this->lastParams = $params;
        return 1;
    }
}

function verify_config_caching() {
    $mockDb = new MockDB();

    // Create instance without triggering constructor
    $reflection = new ReflectionClass(\App\Models\Configuracion::class);
    $configModel = $reflection->newInstanceWithoutConstructor();

    // Inject mock database
    $dbProp = new ReflectionProperty(\App\Models\BaseModel::class, 'db');
    $dbProp->setAccessible(true);
    $dbProp->setValue($configModel, $mockDb);

    echo "--- Testing Configuracion Request-Level Caching ---\n";

    // Test 1: get() caching
    echo "Test 1: Multiple get() calls for the same key\n";
    $mockDb->mockResult = ['valor' => 'some_value'];

    $val1 = $configModel->get('test_key');
    $val2 = $configModel->get('test_key');

    if ($mockDb->queryCount === 1 && $val1 === 'some_value' && $val2 === 'some_value') {
        echo "✅ PASS: Database queried only once for 'test_key'.\n";
    } else {
        echo "❌ FAIL: Expected 1 query, got {$mockDb->queryCount}.\n";
        exit(1);
    }

    // Test 2: set() cache update
    echo "\nTest 2: set() updates the cache\n";
    $configModel->set('test_key', 'new_value');
    $val3 = $configModel->get('test_key');

    if ($val3 === 'new_value') {
        echo "✅ PASS: get() returned the value set in-memory.\n";
    } else {
        echo "❌ FAIL: get() returned '{$val3}', expected 'new_value'.\n";
        exit(1);
    }

    // Test 3: getTasaBCV() caching
    echo "\nTest 3: Multiple getTasaBCV() calls\n";
    $mockDb->queryCount = 0;
    $mockDb->mockResult = ['valor' => '55.50', 'updated_at' => date('Y-m-d H:i:s')];

    $tasa1 = $configModel->getTasaBCV();
    $tasa2 = $configModel->getTasaBCV();

    if ($mockDb->queryCount === 1 && $tasa1 === '55.50' && $tasa2 === '55.50') {
        echo "✅ PASS: Database queried only once for 'tasa_bcv'.\n";
    } else {
        echo "❌ FAIL: Expected 1 query, got {$mockDb->queryCount}.\n";
        exit(1);
    }

    echo "\nSUCCESS: Configuracion optimization verified.\n";
}

verify_config_caching();
