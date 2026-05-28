<?php

namespace App\Core;

// Mock Database class
class Database {
    private static $instance = null;
    public $queries = 0;
    public $responses = [];

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function fetchOne($sql, $params = []) {
        $this->queries++;
        return array_shift($this->responses) ?? null;
    }

    public function execute($sql, $params = []) {
        $this->queries++;
        return 1;
    }
}

require_once __DIR__ . '/../app/Models/BaseModel.php';
require_once __DIR__ . '/../app/Models/Configuracion.php';

use App\Models\Configuracion;

function testConfigOptimization() {
    $db = Database::getInstance();
    $model = new Configuracion();

    echo "--- Testing Configuracion::get (Request-Level Cache) ---\n";
    $db->queries = 0;
    $db->responses = [
        ['valor' => 'SIPAN']
    ];

    // First call - should query DB
    $model->get('site_name');
    // Second call - should use cache
    $model->get('site_name');

    if ($db->queries === 1) {
        echo "✅ SUCCESS: get() uses cache (only 1 query for 2 calls).\n";
    } else {
        echo "❌ FAILURE: get() used $db->queries queries for 2 calls.\n";
    }

    echo "\n--- Testing Configuracion::getTasaBCV (Request-Level Cache) ---\n";
    $db->queries = 0;
    $db->responses = [
        ['valor' => '50.00', 'updated_at' => date('Y-m-d H:i:s')]
    ];

    // First call - should query DB
    $model->getTasaBCV();
    // Second call - should use cache
    $model->getTasaBCV();

    if ($db->queries === 1) {
        echo "✅ SUCCESS: getTasaBCV() uses cache (only 1 query for 2 calls).\n";
    } else {
        echo "❌ FAILURE: getTasaBCV() used $db->queries queries for 2 calls.\n";
    }

    echo "\n--- Testing Cache Update on set() ---\n";
    $db->queries = 0;
    // set() first calls get() (1 query) then execute() (1 query)
    $db->responses = [['valor' => 'original']];
    $model->set('test_key', 'new_value');

    // Now get() should NOT query DB because set() updated the cache
    $db->queries = 0;
    $val = $model->get('test_key');

    if ($db->queries === 0 && $val === 'new_value') {
        echo "✅ SUCCESS: set() updates cache and avoids subsequent DB lookup.\n";
    } else {
        echo "❌ FAILURE: get() after set() queried DB or got wrong value ($val).\n";
    }
}

testConfigOptimization();
