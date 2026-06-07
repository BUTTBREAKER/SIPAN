<?php

namespace App\Core {
    class Database {
        private static $instance = null;
        public $queryCount = 0;
        public $lastSql = '';
        public $lastParams = [];
        public $mockData = [];

        public static function getInstance() {
            if (self::$instance === null) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        public function fetchOne($sql, $params = []) {
            $this->queryCount++;
            $this->lastSql = $sql;
            $this->lastParams = $params;

            if (strpos($sql, "caja_movimientos") !== false) {
                return ['monto_apertura' => 100, 'ingresos' => 50, 'egresos' => 20];
            }

            if (strpos($sql, "WHERE id_sucursal = ? AND estado = 'abierta'") !== false) {
                return $this->mockData['active_caja'] ?? null;
            }

            if (strpos($sql, "FROM cajas WHERE id = ?") !== false) {
                 return ['id' => $params[0], 'id_sucursal' => 1, 'monto_apertura' => 100];
            }

            return null;
        }

        public function fetchAll($sql, $params = []) {
            $this->queryCount++;
            $this->lastSql = $sql;
            $this->lastParams = $params;

            if (strpos($sql, "WHERE estado = 'activa'") !== false) {
                return [['id' => 1, 'nombre' => 'Sucursal 1'], ['id' => 2, 'nombre' => 'Sucursal 2']];
            }

            return [];
        }

        public function execute($sql, $params = []) {
            $this->queryCount++;
            $this->lastSql = $sql;
            $this->lastParams = $params;
            return 1;
        }

        public function create($table, $data) {
             $this->queryCount++;
             return 1;
        }

        public function lastInsertId() {
            return 1;
        }

        public function beginTransaction() { return true; }
        public function commit() { return true; }
        public function rollback() { return true; }
    }
}

namespace {
    require_once __DIR__ . '/../app/Models/BaseModel.php';
    require_once __DIR__ . '/../app/Models/Caja.php';
    require_once __DIR__ . '/../app/Models/Sucursal.php';

    use App\Models\Caja;
    use App\Models\Sucursal;
    use App\Core\Database;

    function runTest() {
        echo "--- Iniciando Test de Optimización de Caja y Sucursal ---\n";

        $db = Database::getInstance();
        $cajaModel = new Caja();
        $sucursalModel = new Sucursal();

        // --- TEST CAJA ---

        // Test 1: getActiva caching
        echo "Test 1: getActiva() caching...\n";
        $db->mockData['active_caja'] = ['id' => 1, 'id_sucursal' => 1, 'estado' => 'abierta'];
        $db->queryCount = 0;

        $caja1 = $cajaModel->getActiva(1);
        assert($caja1['id'] === 1);
        assert($db->queryCount === 1);

        $caja2 = $cajaModel->getActiva(1);
        assert($caja2['id'] === 1);
        assert($db->queryCount === 1); // Debe usar cache
        echo "OK: getActiva() usa cache.\n";

        // Test 2: Invalidation on abrir
        echo "Test 2: Invalidation on abrir()...\n";
        $cajaModel->abrir(1, 1, 100, 0, 50);
        assert($db->queryCount === 2); // 1 create

        $caja3 = $cajaModel->getActiva(1);
        assert($db->queryCount === 3); // Debe consultar DB de nuevo
        echo "OK: Cache invalidado al abrir caja.\n";

        // Test 3: Consolidated getResumen
        echo "Test 3: getResumen() consolidation...\n";
        $db->queryCount = 0;
        $resumen = $cajaModel->getResumen(1);
        assert($db->queryCount === 1); // Solo una query con JOIN
        assert($resumen['apertura'] == 100);
        assert($resumen['ingresos'] == 50);
        assert($resumen['egresos'] == 20);
        assert($resumen['esperado'] == 130);
        echo "OK: getResumen() consolidado en 1 query.\n";

        // Test 4: Invalidation on cerrar
        echo "Test 4: Invalidation on cerrar()...\n";
        $db->queryCount = 0;
        $cajaModel->cerrar(1, 1, 100, 0, 50);
        // cerrar llama a find (1), getResumen (1), update (1) = 3 queries
        assert($db->queryCount === 3);

        $caja4 = $cajaModel->getActiva(1);
        assert($db->queryCount === 4); // Debe consultar DB de nuevo
        echo "OK: Cache invalidado al cerrar caja.\n";

        // --- TEST SUCURSAL ---

        // Test 5: getActivas caching
        echo "Test 5: getActivas() caching...\n";
        $db->queryCount = 0;
        $sucursales1 = $sucursalModel->getActivas();
        assert(count($sucursales1) === 2);
        assert($db->queryCount === 1);

        $sucursales2 = $sucursalModel->getActivas();
        assert(count($sucursales2) === 2);
        assert($db->queryCount === 1); // Debe usar cache
        echo "OK: getActivas() usa cache.\n";

        echo "--- Todos los tests pasaron exitosamente ---\n";
    }

    try {
        runTest();
    } catch (Exception $e) {
        echo "ERROR: " . $e->getMessage() . "\n";
        echo "Trace: " . $e->getTraceAsString() . "\n";
        exit(1);
    }
}
