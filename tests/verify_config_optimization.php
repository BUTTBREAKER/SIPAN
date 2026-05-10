<?php

// Mocking the database connection since we can't easily override the class in PHP if it's already loaded or via include
namespace App\Core {
    class Database {
        public static $queryCount = 0;
        private static $instance = null;
        public static function getInstance() {
            if (self::$instance === null) self::$instance = new self();
            return self::$instance;
        }
        public function fetchOne($sql, $params = []) {
            self::$queryCount++;
            if (strpos($sql, 'tasa_bcv') !== false) {
                return ['valor' => '55.5', 'updated_at' => date('Y-m-d H:i:s')];
            }
            return ['valor' => 'mock_value'];
        }
        public function fetchAll($sql, $params = []) {
            self::$queryCount++;
            return [];
        }
        public function execute($sql, $params = []) {
            self::$queryCount++;
            return 1;
        }
        public function lastInsertId() {
            return 1;
        }
    }
}

// We need to define the classes in a way that doesn't conflict with real ones if we were including them,
// but since we are running a standalone script, we can just define them if they are not loaded.

namespace App\Models {
    // We define a MockBaseModel to avoid conflict if BaseModel is already there, but in a separate process it should be fine.
    // However, the real Configuracion extends App\Models\BaseModel.
    // So we MUST define App\Models\BaseModel.
    if (!class_exists('App\Models\BaseModel')) {
        class BaseModel {
            protected $db;
            public function __construct() {
                $this->db = \App\Core\Database::getInstance();
            }
        }
    }
}

// Now we need to define Configuracion but it's already in app/Models/Configuracion.php
// We can't easily mock the DB inside it without changing its code or using a DI.
// Since we can't use DI easily here, let's use a trick:
// we will read the file, strip the namespace and class declaration, and eval it? No, too complex.
// Better: The real Configuracion uses App\Core\Database::getInstance().
// If we define App\Core\Database before including Configuracion, it might work if not autloaded.

namespace {
    // The real code uses \App\Core\Database.
    // Our mock is already in App\Core\Database.

    require_once __DIR__ . '/../app/Models/Configuracion.php';

    use App\Models\Configuracion;
    use App\Core\Database;

    $config = new Configuracion();

    echo "--- Testing get() cache ---\n";
    Database::$queryCount = 0;

    $val1 = $config->get('test_key');
    $val2 = $config->get('test_key');
    $val3 = $config->get('test_key');

    echo "Queries for 3 get() calls: " . Database::$queryCount . " (Expected: 1)\n";
    if (Database::$queryCount !== 1) {
        echo "FAILED: get() cache not working as expected.\n";
        exit(1);
    }

    echo "--- Testing set() cache updates ---\n";
    $config->set('test_key', 'new_value');
    $val4 = $config->get('test_key');
    if ($val4 !== 'new_value') {
        echo "FAILED: set() did not update cache correctly. Got: " . var_export($val4, true) . "\n";
        exit(1);
    }
    echo "set() and subsequent get() working correctly.\n";

    echo "--- Testing getTasaBCV() guard ---\n";
    Database::$queryCount = 0;

    $rate1 = $config->getTasaBCV();
    $rate2 = $config->getTasaBCV();
    $rate3 = $config->getTasaBCV();

    echo "Queries for 3 getTasaBCV() calls: " . Database::$queryCount . " (Expected: 1)\n";
    if (Database::$queryCount !== 1) {
        echo "FAILED: getTasaBCV() guard not working as expected.\n";
        exit(1);
    }

    echo "--- SUCCESS: All optimizations verified! ---\n";
}
