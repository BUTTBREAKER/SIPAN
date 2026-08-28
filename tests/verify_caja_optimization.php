<?php

require_once __DIR__ . '/../app/Models/BaseModel.php';
require_once __DIR__ . '/../app/Models/Caja.php';

class MockDB {
    public $queryCount = 0;
    public $lastSql = '';
    public $data = [];
    public $lastInsertId = 100;

    public function fetchOne($sql, $params = []) {
        $this->queryCount++;
        $this->lastSql = $sql;

        if (strpos($sql, 'caja_movimientos') !== false) {
            // Mock getResumen response
            return [
                'monto_apertura' => 100.0,
                'ingresos' => 50.0,
                'egresos' => 20.0
            ];
        }

        if (strpos($sql, "WHERE id_sucursal = ? AND estado = 'abierta'") !== false) {
            return $this->data['active_caja'] ?? null;
        }

        if (strpos($sql, "WHERE id = ?") !== false) {
            return $this->data['caja_' . $params[0]] ?? null;
        }

        return null;
    }

    public function execute($sql, $params = []) {
        $this->queryCount++;
        $this->lastSql = $sql;
        return true;
    }

    public function lastInsertId() {
        return $this->lastInsertId;
    }
}

class MockCaja extends \App\Models\Caja {
    public function __construct($db) {
        $this->db = $db;
    }

    // Helper to clear static cache for testing
    public static function clearCache() {
        $reflection = new ReflectionClass(\App\Models\Caja::class);
        $property = $reflection->getProperty('activeCajaCache');
        $property->setAccessible(true);
        $property->setValue([]);
    }
}

function runTest() {
    echo "--- Iniciando Test de Optimización de Caja ---\n";

    $mockDb = new MockDB();
    $cajaModel = new MockCaja($mockDb);
    MockCaja::clearCache();

    // 1. Test getActiva Caching
    echo "Test 1: Verificando caching en getActiva...\n";
    $mockDb->data['active_caja'] = ['id' => 1, 'id_sucursal' => 10, 'estado' => 'abierta'];

    $c1 = $cajaModel->getActiva(10);
    assert($c1['id'] === 1);
    assert($mockDb->queryCount === 1);

    $c2 = $cajaModel->getActiva(10);
    assert($c2['id'] === 1);
    assert($mockDb->queryCount === 1, "Debe usar el cache y no incrementar queries");
    echo "OK: Caching funcional.\n";

    // 2. Test Invalidation on abrir
    echo "Test 2: Verificando invalidación en abrir...\n";
    $cajaModel->abrir(10, 1, 50, 0, 50);
    assert($mockDb->queryCount === 2); // 1 (getActiva) + 1 (INSERT)

    $c3 = $cajaModel->getActiva(10);
    assert($mockDb->queryCount === 3, "Debe realizar una nueva query después de invalidar");
    echo "OK: Invalidación en abrir funcional.\n";

    // 3. Test Invalidation on cerrar
    echo "Test 3: Verificando invalidación en cerrar...\n";
    $mockDb->data['caja_1'] = ['id' => 1, 'id_sucursal' => 10];
    $cajaModel->cerrar(1, 1, 100, 0, 50);
    // Queries: 1 (getResumen) + 1 (find) + 1 (update)
    assert($mockDb->queryCount === 6); // 3 previo + 3

    $c4 = $cajaModel->getActiva(10);
    assert($mockDb->queryCount === 7, "Debe realizar una nueva query después de cerrar");
    echo "OK: Invalidación en cerrar funcional.\n";

    // 4. Test getResumen Consolidated Query
    echo "Test 4: Verificando query consolidada en getResumen...\n";
    $mockDb->queryCount = 0;
    $resumen = $cajaModel->getResumen(1);
    assert($mockDb->queryCount === 1, "Debe realizar solo UNA query");
    assert($resumen['apertura'] === 100.0);
    assert($resumen['ingresos'] === 50.0);
    assert($resumen['egresos'] === 20.0);
    assert($resumen['esperado'] === 130.0);
    echo "OK: Query consolidada funcional y matemáticamente correcta.\n";

    echo "--- Todos los tests de Caja pasaron exitosamente ---\n";
}

try {
    runTest();
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
    exit(1);
}
