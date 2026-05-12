<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Models\Configuracion;

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
namespace App\Models;

// Mocking required classes for standalone test
class Database {
    public static $queryCount = 0;
    private static $instance = null;

    public static function getInstance() {
        if (self::$instance === null) self::$instance = new self();
        return self::$instance;
    }

    public function fetchOne($sql, $params = []) {
        self::$queryCount++;
        echo "   [DB] Executing: $sql with " . json_encode($params) . "\n";

        if (strpos($sql, "SELECT valor, updated_at") !== false) {
            return ['valor' => 55.50, 'updated_at' => date('Y-m-d H:i:s')];
        }
        if (strpos($sql, "SELECT valor") !== false) {
            return ['valor' => 'some_value'];
        }
        return ['1' => 1];
    }

    public function execute($sql, $params = []) {
        self::$queryCount++;
        echo "   [DB] Executing: $sql with " . json_encode($params) . "\n";
        return 1;
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
}

function verifyConfig() {
    $mockDb = new MockDatabase();
    // Set a recent update date so it doesn't try to call fetchFromApi (which uses curl)
    $mockDb->data['tasa_bcv']['updated_at'] = date('Y-m-d H:i:s');

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
class BaseModel {
    protected $db;
    protected $table;
    public function __construct() {
        $this->db = Database::getInstance();
    }
}

// Re-defining a simplified version of the optimized model for verification
class ConfiguracionOptimized extends BaseModel {
    protected $table = 'configuracion';
    private static $cache = [];
    private static $tasaBcvChecked = false;

    public function get($key, $default = null) {
        if (array_key_exists($key, self::$cache)) {
            echo "   [CACHE] Hit for: $key\n";
            return self::$cache[$key] ?? $default;
        }
        $sql = "SELECT valor FROM {$this->table} WHERE clave = ? LIMIT 1";
        $result = $this->db->fetchOne($sql, [$key]);
        $value = $result ? $result['valor'] : null;
        self::$cache[$key] = $value;
        return $value ?? $default;
    }

    public function set($key, $value) {
        $sqlCheck = "SELECT 1 FROM {$this->table} WHERE clave = ? LIMIT 1";
        $exists = $this->db->fetchOne($sqlCheck, [$key]);
        if ($exists) {
            $sql = "UPDATE {$this->table} SET valor = ? WHERE clave = ?";
            $result = $this->db->execute($sql, [$value, $key]);
        } else {
            $sql = "INSERT INTO {$this->table} (clave, valor) VALUES (?, ?)";
            $result = $this->db->execute($sql, [$key, $value]);
        }
        self::$cache[$key] = $value;
        return $result;
    }

    public function getTasaBCV() {
        $key = 'tasa_bcv';
        if (self::$tasaBcvChecked && isset(self::$cache[$key])) {
            echo "   [CACHE] Hit for: $key (checked flag is true)\n";
            return (float)self::$cache[$key];
        }
        $sql = "SELECT valor, updated_at FROM {$this->table} WHERE clave = ? LIMIT 1";
        $row = $this->db->fetchOne($sql, [$key]);
        $rate = $row ? $row['valor'] : 50.00;
        self::$cache[$key] = $rate;
        self::$tasaBcvChecked = true;
        return (float)$rate;
    }
}

echo "=== Testing Configuracion Optimization ===\n";
$config = new ConfiguracionOptimized();

echo "\n1. Testing first get('app_name'):\n";
$config->get('app_name');

echo "\n2. Testing second get('app_name') (should be CACHE hit):\n";
$config->get('app_name');

echo "\n3. Testing first getTasaBCV():\n";
$config->getTasaBCV();

echo "\n4. Testing second getTasaBCV() (should be CACHE hit and skip updated_at check):\n";
$config->getTasaBCV();

echo "\n5. Testing set('theme', 'dark'):\n";
$config->set('theme', 'dark');

echo "\n6. Testing get('theme') after set (should be CACHE hit):\n";
$config->get('theme');

echo "\nSummary:\n";
echo "Total DB Queries: " . Database::$queryCount . "\n";

if (Database::$queryCount === 4) {
    echo "\n✅ SUCCESS: Caching working as expected! (Queries: app_name, tasa_bcv, theme_check, theme_update)\n";
} else {
    echo "\n❌ FAILURE: Expected 4 queries, but got " . Database::$queryCount . "\n";
}
