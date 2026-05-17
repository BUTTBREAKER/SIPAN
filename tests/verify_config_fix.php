<?php

namespace App\Models;

class BaseModel {
    protected $db;
    public function __construct() {}
}

// Redefine Configuracion for testing without DB
class Configuracion extends BaseModel {
    protected $table = 'configuracion';
    public $db;
    protected static $cache = [];
    protected static $tasaBcvChecked = false;

    public function get($key, $default = null) {
        if (array_key_exists($key, self::$cache)) {
            return self::$cache[$key] !== null ? self::$cache[$key] : $default;
        }
        $sql = "SELECT valor FROM {$this->table} WHERE clave = ? LIMIT 1";
        $result = $this->db->fetchOne($sql, [$key]);
        $value = $result ? $result['valor'] : null;
        self::$cache[$key] = $value;
        return $value !== null ? $value : $default;
    }

    public function getTasaBCV() {
        $key = 'tasa_bcv';
        if (self::$tasaBcvChecked && array_key_exists($key, self::$cache)) {
            echo "Returning from cache\n";
            return (float)(self::$cache[$key] ?? 50.00);
        }
        echo "Fetching from DB\n";
        $sql = "SELECT valor, updated_at FROM {$this->table} WHERE clave = ? LIMIT 1";
        $row = $this->db->fetchOne($sql, [$key]);
        $rate = $row ? (float)$row['valor'] : 50.00;
        self::$cache[$key] = $rate;
        self::$tasaBcvChecked = true;
        return (float)$rate;
    }

    public static function clearCache() {
        self::$cache = [];
        self::$tasaBcvChecked = false;
    }
}

class MockDB {
    public $queryCount = 0;
    public function fetchOne($sql, $params = []) {
        $this->queryCount++;
        return ['valor' => '55.5', 'updated_at' => date('Y-m-d H:i:s')];
    }
}

$mockDb = new MockDB();
$config = new Configuracion();
$config->db = $mockDb;

echo "--- Testing Configuracion::getTasaBCV Caching ---\n";
Configuracion::clearCache();

echo "Call 1: ";
$config->getTasaBCV();
echo "Call 2: ";
$config->getTasaBCV();
echo "Call 3: ";
$config->getTasaBCV();

echo "Total DB Queries: " . $mockDb->queryCount . "\n";

if ($mockDb->queryCount === 1) {
    echo "SUCCESS: Only one DB query was made for multiple calls.\n";
} else {
    echo "FAILURE: Multiple DB queries were made: " . $mockDb->queryCount . "\n";
    exit(1);
}

echo "\n--- All Verifications Passed ---\n";
