<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Models\Caja;
use App\Models\Sucursal;

// Mock Database to track queries
class MockDatabase {
    public $queryCount = 0;
    public $queries = [];

    public function fetchOne($sql, $params = []) {
        $this->queryCount++;
        $this->queries[] = ['sql' => $sql, 'params' => $params];
        if (strpos($sql, 'FROM cajas') !== false && strpos($sql, 'id_sucursal = ?') !== false) {
            return ['id' => 1, 'estado' => 'abierta', 'monto_apertura' => 100];
        }
        if (strpos($sql, 'FROM sucursales') !== false) {
            return [['id' => 1, 'nombre' => 'Sucursal 1']];
        }
        if (strpos($sql, 'FROM caja_movimientos') !== false) {
            return ['ingresos' => 50, 'egresos' => 20];
        }
        if (strpos($sql, 'FROM cajas') !== false && strpos($sql, 'id = ?') !== false) {
            return ['id' => 1, 'monto_apertura' => 100];
        }
        return null;
    }

    public function fetchAll($sql, $params = []) {
        $this->queryCount++;
        $this->queries[] = ['sql' => $sql, 'params' => $params];
        if (strpos($sql, 'FROM sucursales') !== false) {
            return [['id' => 1, 'nombre' => 'Sucursal 1']];
        }
        return [];
    }

    public function execute($sql, $params = []) {
        $this->queryCount++;
        $this->queries[] = ['sql' => $sql, 'params' => $params];
        return 1;
    }

    public function lastInsertId() {
        return 1;
    }
}

// Helper to inject mock DB
class TestCaja extends Caja {
    public function __construct($db) { $this->db = $db; $this->table = 'cajas'; }
}

class TestSucursal extends Sucursal {
    public function __construct($db) { $this->db = $db; $this->table = 'sucursales'; }
}

function testCajaOptimization() {
    $mockDb = new MockDatabase();
    $cajaModel = new TestCaja($mockDb);
    $sucursalModel = new TestSucursal($mockDb);

    echo "--- Testing Caja::getActiva caching ---\n";
    $cajaModel->getActiva(1);
    $cajaModel->getActiva(1);

    if ($mockDb->queryCount > 1) {
        echo "❌ Request-level caching NOT implemented for Caja::getActiva (Query count: {$mockDb->queryCount})\n";
    } else {
        echo "✅ Request-level caching working for Caja::getActiva\n";
    }

    $mockDb->queryCount = 0;
    echo "\n--- Testing Sucursal::getActivas caching ---\n";
    $sucursalModel->getActivas();
    $sucursalModel->getActivas();

    if ($mockDb->queryCount > 1) {
        echo "❌ Request-level caching NOT implemented for Sucursal::getActivas (Query count: {$mockDb->queryCount})\n";
    } else {
        echo "✅ Request-level caching working for Sucursal::getActivas\n";
    }

    $mockDb->queryCount = 0;
    echo "\n--- Testing Caja::getResumen consolidated query ---\n";
    $cajaModel->getResumen(1);

    $foundJoin = false;
    foreach ($mockDb->queries as $q) {
        if (strpos($q['sql'], 'LEFT JOIN caja_movimientos') !== false) {
            $foundJoin = true;
        }
    }

    if ($foundJoin && $mockDb->queryCount === 1) {
        echo "✅ Caja::getResumen consolidated into a single query with JOIN\n";
    } else {
        echo "❌ Caja::getResumen still using multiple queries (Count: {$mockDb->queryCount})\n";
    }
}

testCajaOptimization();
