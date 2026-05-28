<?php

namespace App\Core;

// Mock Database class
class Database {
    private static $instance = null;
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
        return array_shift($this->responses) ?? null;
    }

    public function fetchAll($sql, $params = []) {
        $this->queries[] = ['sql' => $sql, 'params' => $params];
        return array_shift($this->responses) ?? [];
    }

    public function execute($sql, $params = []) {
        $this->queries[] = ['sql' => $sql, 'params' => $params];
        return 1;
    }

    public function create($sql, $params = []) {
        return $this->execute($sql, $params);
    }

    public function lastInsertId() { return 123; }
}

require_once __DIR__ . '/../app/Models/BaseModel.php';
require_once __DIR__ . '/../app/Models/Caja.php';

use App\Models\Caja;

function testCajaOptimization() {
    $db = Database::getInstance();
    $model = new Caja();

    echo "--- Testing Caja::getResumen (Consolidated Query) ---\n";
    $db->responses = [
        [
            'monto_apertura' => 100.0,
            'ingresos' => 50.0,
            'egresos' => 20.0
        ]
    ];

    $resumen = $model->getResumen(1);

    if (count($db->queries) === 1) {
        echo "✅ SUCCESS: getResumen uses exactly 1 query.\n";
    } else {
        echo "❌ FAILURE: getResumen used " . count($db->queries) . " queries.\n";
    }

    if ($resumen['esperado'] == 130.0) {
        echo "✅ SUCCESS: Calculation correct (100 + 50 - 20 = 130).\n";
    } else {
        echo "❌ FAILURE: Calculation incorrect. Got " . $resumen['esperado'] . "\n";
    }

    echo "\n--- Testing Caja::getActiva (Request-Level Cache) ---\n";
    $db->queries = [];
    $db->responses = [
        ['id' => 1, 'estado' => 'abierta']
    ];

    // First call - should query DB
    $model->getActiva(1);
    // Second call - should use cache
    $model->getActiva(1);

    if (count($db->queries) === 1) {
        echo "✅ SUCCESS: getActiva uses cache (only 1 query for 2 calls).\n";
    } else {
        echo "❌ FAILURE: getActiva used " . count($db->queries) . " queries for 2 calls.\n";
    }

    echo "\n--- Testing Cache Invalidation on abrir ---\n";
    $db->queries = [];
    // abrir will call create (which calls execute)
    $model->abrir(1, 1, 100, 0, 1);

    // Now getActiva(1) should query DB again
    $db->responses = [['id' => 2, 'estado' => 'abierta']];
    $model->getActiva(1);

    $foundQuery = false;
    foreach($db->queries as $q) {
        if (strpos($q['sql'], 'SELECT * FROM cajas WHERE id_sucursal = ? AND estado = \'abierta\'') !== false) {
            $foundQuery = true;
            break;
        }
    }

    if ($foundQuery) {
        echo "✅ SUCCESS: Cache invalidated after abrir.\n";
    } else {
        echo "❌ FAILURE: Cache NOT invalidated after abrir.\n";
    }
}

testCajaOptimization();
