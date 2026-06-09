<?php

namespace App\Core;

class Database {
    private static $instance = null;
    public $queries = [];
    public static function getInstance() {
        if (self::$instance === null) self::$instance = new self();
        return self::$instance;
    }
    public function fetchOne($sql, $params = []) {
        $this->queries[] = ['sql' => $sql, 'params' => $params];
        if (strpos($sql, 'cajas') !== false && strpos($sql, 'estado = \'abierta\'') !== false) {
            return ['id' => 1, 'id_sucursal' => 1, 'estado' => 'abierta', 'monto_apertura' => 100.0];
        }
        if (strpos($sql, 'cajas') !== false && strpos($sql, 'GROUP BY c.id') !== false) {
            return ['monto_apertura' => 100.0, 'ingresos' => 50.0, 'egresos' => 20.0];
        }
        if (strpos($sql, 'cajas') !== false && strpos($sql, 'WHERE id = ?') !== false) {
            return ['id' => 1, 'id_sucursal' => 1];
        }
        return null;
    }
    public function fetchAll($sql, $params = []) {
        $this->queries[] = ['sql' => $sql, 'params' => $params];
        if (strpos($sql, 'sucursales') !== false) {
            return [['id' => 1, 'nombre' => 'Sucursal 1']];
        }
        return [];
    }
    public function execute($sql, $params = []) {
        $this->queries[] = ['sql' => $sql, 'params' => $params];
        return 1;
    }
    public function create($sql, $params = []) { return $this->execute($sql, $params); }
    public function lastInsertId() { return 1; }
}

namespace App\Models;
require_once __DIR__ . '/../app/Models/BaseModel.php';
require_once __DIR__ . '/../app/Models/Caja.php';
require_once __DIR__ . '/../app/Models/Sucursal.php';

use App\Core\Database;

function testCajaCaching() {
    echo "Testing Caja::getActiva caching...\n";
    $db = Database::getInstance();
    $caja = new Caja();

    $db->queries = [];
    $caja->getActiva(1);
    $caja->getActiva(1);

    if (count($db->queries) === 1) {
        echo "✅ Caja::getActiva cache working (1 query for 2 calls)\n";
    } else {
        echo "❌ Caja::getActiva cache failed (" . count($db->queries) . " queries)\n";
    }

    echo "Testing Caja::abrir cache invalidation...\n";
    $db->queries = [];
    $caja->abrir(1, 1, 100, 0, 1); // Should invalidate
    $caja->getActiva(1);
    if (count($db->queries) >= 2) {
        echo "✅ Caja::abrir invalidated cache\n";
    } else {
        echo "❌ Caja::abrir failed to invalidate cache\n";
    }
}

function testSucursalCaching() {
    echo "Testing Sucursal::getActivas caching...\n";
    $db = Database::getInstance();
    $sucursal = new Sucursal();

    $db->queries = [];
    $sucursal->getActivas();
    $sucursal->getActivas();

    if (count($db->queries) === 1) {
        echo "✅ Sucursal::getActivas cache working (1 query for 2 calls)\n";
    } else {
        echo "❌ Sucursal::getActivas cache failed (" . count($db->queries) . " queries)\n";
    }
}

function testCajaResumenOptimization() {
    echo "Testing Caja::getResumen optimization...\n";
    $db = Database::getInstance();
    $caja = new Caja();

    $db->queries = [];
    $resumen = $caja->getResumen(1);

    if (count($db->queries) === 1) {
        echo "✅ Caja::getResumen uses 1 consolidated query\n";
        $sql = $db->queries[0]['sql'];
        if (strpos($sql, 'LEFT JOIN caja_movimientos') !== false && strpos($sql, 'monto_apertura') !== false) {
             echo "✅ Query structure looks correct (JOIN + monto_apertura)\n";
        } else {
             echo "❌ Query structure is missing required parts: $sql\n";
        }

        if ($resumen['esperado'] == 130.0) {
            echo "✅ Calculation correct (100 + 50 - 20 = 130)\n";
        } else {
            echo "❌ Calculation wrong: " . $resumen['esperado'] . "\n";
        }
    } else {
        echo "❌ Caja::getResumen failed to consolidate (" . count($db->queries) . " queries)\n";
    }
}

testCajaCaching();
testSucursalCaching();
testCajaResumenOptimization();
