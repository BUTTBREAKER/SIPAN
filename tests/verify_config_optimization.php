<?php
namespace App\Core;

class Database {
    public static $instance = null;
    public $queries = [];
    public $responses = [];

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    public function fetchOne($sql, $params = []) {
        $this->queries[] = ['sql' => $sql, 'params' => $params];
        return array_shift($this->responses);
    }
    public function execute($sql, $params = []) {
        $this->queries[] = ['sql' => $sql, 'params' => $params];
        return 1;
    }
}

namespace App\Helpers;
class Environment {
    public static function get($key, $default = null) { return $default; }
}

namespace Test;
use App\Models\Configuracion;
use App\Core\Database;

// Define some dummy constants/environment for the models
if (!defined('BASE_URL')) define('BASE_URL', '/');

require_once __DIR__ . '/../app/Models/BaseModel.php';
require_once __DIR__ . '/../app/Models/Configuracion.php';

function testOptimization() {
    $db = Database::getInstance();
    $config = new Configuracion();

    echo "Testing get() caching...\n";
    $db->responses = [['valor' => '35.50']];

    $val1 = $config->get('test_key');
    $val2 = $config->get('test_key');

    if ($val1 === '35.50' && $val2 === '35.50' && count($db->queries) === 1) {
        echo "✅ get() caching works! (1 query for 2 calls)\n";
    } else {
        echo "❌ get() caching failed. Queries: " . count($db->queries) . "\n";
        print_r($db->queries);
    }

    echo "\nTesting set() optimization...\n";
    $db->queries = [];
    $config->set('new_key', 'new_val');

    $sql = $db->queries[0]['sql'] ?? '';
    if (strpos($sql, 'ON DUPLICATE KEY UPDATE') !== false && strpos($sql, 'AS new_data') !== false) {
        echo "✅ set() uses optimized SQL with alias!\n";
    } else {
        echo "❌ set() logic is not optimized. SQL: $sql\n";
    }

    echo "\nTesting getTasaBCV() request-level caching...\n";
    $db->queries = [];
    $db->responses = [['valor' => '50.00', 'updated_at' => date('Y-m-d H:i:s')]];

    $tasa1 = $config->getTasaBCV();
    $tasa2 = $config->getTasaBCV();

    if ($tasa1 == 50.0 && $tasa2 == 50.0 && count($db->queries) === 1) {
        echo "✅ getTasaBCV() caching works! (1 query for 2 calls)\n";
    } else {
        echo "❌ getTasaBCV() caching failed. Queries: " . count($db->queries) . "\n";
        print_r($db->queries);
    }
}

try {
    testOptimization();
} catch (\Exception $e) {
    echo "Error during test: " . $e->getMessage() . "\n";
    exit(1);
}
