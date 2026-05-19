<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Models\Configuracion;
use App\Core\Database;

/**
 * Mock Database to track query counts and simulate data
 */
class MockDatabase {
    public $queries = 0;
    public $data = [
        'tasa_bcv' => ['valor' => '50.00', 'updated_at' => null]
    ];

    public function fetchOne($sql, $params = []) {
        $this->queries++;
        $key = $params[0];
        if ($key === 'tasa_bcv' && strpos($sql, 'updated_at') !== false) {
            return $this->data['tasa_bcv'];
        }
        return isset($this->data[$key]) ? ['valor' => $this->data[$key]['valor']] : null;
    }

    public function execute($sql, $params = []) {
        $this->queries++;
        return true;
    }

    public function fetchAll($sql, $params = []) {
        $this->queries++;
        return [];
    }
}

/**
 * Test class to inject MockDatabase
 */
class TestConfiguracion extends Configuracion {
    public function __construct($db) {
        $this->db = $db;
        $this->table = 'configuracion';
    }

    public static function reset() {
        self::$cache = [];
        self::$tasaBcvChecked = false;
    }
}

function verifyConfig() {
    $mockDb = new MockDatabase();
    // Set a recent update date so it doesn't try to call fetchFromApi (which uses curl)
    $mockDb->data['tasa_bcv']['updated_at'] = date('Y-m-d H:i:s');

    TestConfiguracion::reset();
    $config = new TestConfiguracion($mockDb);

    echo "--- Testing Configuracion Optimization ---\n";

    // 1. Test get() caching
    echo "Test 1: get() caching...\n";
    echo "  Calling get('moneda_principal')...\n";
    $config->get('moneda_principal', 'USD');
    $q1 = $mockDb->queries;
    echo "  Queries so far: $q1\n";

    echo "  Calling get('moneda_principal') again...\n";
    $config->get('moneda_principal', 'USD');
    $q2 = $mockDb->queries;
    echo "  Queries so far: $q2\n";

    if ($q1 === $q2 && $q1 > 0) {
        echo "✅ Test 1 Passed: get() request-level cache verified!\n";
    } else {
        echo "❌ Test 1 Failed: get() request-level cache failed! (Queries: $q1 -> $q2)\n";
        exit(1);
    }

    // 2. Test getTasaBCV() caching
    echo "\nTest 2: getTasaBCV() caching...\n";
    $mockDb->queries = 0;
    echo "  Calling getTasaBCV()...\n";
    $config->getTasaBCV();
    $tq1 = $mockDb->queries;
    echo "  Queries for first call: $tq1\n";

    echo "  Calling getTasaBCV() again...\n";
    $config->getTasaBCV();
    $tq2 = $mockDb->queries;
    echo "  Queries after second call: $tq2\n";

    if ($tq1 === $tq2 && $tq1 > 0) {
        echo "✅ Test 2 Passed: getTasaBCV() request-level cache verified!\n";
    } else {
        echo "❌ Test 2 Failed: getTasaBCV() request-level cache failed! (Queries: $tq1 -> $tq2)\n";
        exit(1);
    }

    // 3. Test set() cache update
    echo "\nTest 3: set() cache update...\n";
    echo "  Calling set('site_name', 'SIPAN Panadería')...\n";
    $config->set('site_name', 'SIPAN Panadería');
    // Note: set() calls get() which triggers a query if not in cache.
    // Site name was not in cache, so set() calls get() -> query #1.
    // Then it executes update or insert -> query #2.
    $qBefore = $mockDb->queries;

    echo "  Calling get('site_name')...\n";
    $val = $config->get('site_name');
    $qAfter = $mockDb->queries;

    if ($val === 'SIPAN Panadería' && $qBefore === $qAfter) {
        echo "✅ Test 3 Passed: set() updates cache and avoids subsequent lookup!\n";
    } else {
        echo "❌ Test 3 Failed: set() cache update failed! Got: $val, Queries changed: " . ($qAfter - $qBefore) . "\n";
        exit(1);
    }

    echo "\n✨ All Configuracion optimizations verified successfully!\n";
}

try {
    verifyConfig();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}
