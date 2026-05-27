<?php

namespace App\Core;

class Database {
    private static $instance = null;
    public $queryCount = 0;

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function fetchOne($sql, $params = []) {
        $this->queryCount++;
        if (strpos($sql, 'cajas') !== false) {
            return ['id' => 1, 'id_sucursal' => 1, 'estado' => 'abierta'];
        }
        return null;
    }

    public function execute($sql, $params = []) {
        return 1;
    }
}

namespace App\Models;

require_once __DIR__ . '/../app/Models/BaseModel.php';
require_once __DIR__ . '/../app/Models/Caja.php';

use App\Core\Database;

function testCajaCache() {
    $db = Database::getInstance();
    $cajaModel = new Caja();

    echo "--- Testing Caja::getActiva Request-Level Caching ---\n";

    // First call - should trigger DB query
    $db->queryCount = 0;
    $caja1 = $cajaModel->getActiva(1);
    echo "First call query count: " . $db->queryCount . "\n";

    // Second call - should NOT trigger DB query
    $caja2 = $cajaModel->getActiva(1);
    echo "Second call query count: " . $db->queryCount . "\n";

    if ($db->queryCount === 1 && $caja1 === $caja2) {
        echo "✅ Cache works! Multiple calls = 1 DB query.\n";
    } else {
        echo "❌ Cache failed! Query count: " . $db->queryCount . "\n";
        exit(1);
    }

    // Test different sucursal
    $cajaModel->getActiva(2);
    echo "Call for different sucursal query count: " . $db->queryCount . "\n";
    if ($db->queryCount === 2) {
        echo "✅ Cache handles multiple sucursales correctly.\n";
    } else {
        echo "❌ Cache failed for multiple sucursales.\n";
        exit(1);
    }
}

testCajaCache();
