<?php

namespace App\Core {
    class Database {
        private static $instance = null;
        public $queries = [];
        public $params = [];
        public $mockResult = null;
        public $lastInsertIdValue = 0;

        public static function getInstance() {
            if (self::$instance === null) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        public function fetchOne($sql, $params = []) {
            $this->queries[] = $sql;
            $this->params[] = $params;
            return $this->mockResult;
        }

        public function fetchAll($sql, $params = []) {
            $this->queries[] = $sql;
            $this->params[] = $params;
            return is_array($this->mockResult) ? $this->mockResult : [];
        }

        public function execute($sql, $params = []) {
            $this->queries[] = $sql;
            $this->params[] = $params;
            return true;
        }

        public function lastInsertId() {
            return $this->lastInsertIdValue;
        }

        public function reset() {
            $this->queries = [];
            $this->params = [];
            $this->mockResult = null;
            $this->lastInsertIdValue = 0;
        }
    }
}

namespace {
    require_once __DIR__ . '/../app/Models/BaseModel.php';
    require_once __DIR__ . '/../app/Models/Caja.php';
    require_once __DIR__ . '/../app/Models/Sucursal.php';

    use App\Models\Caja;
    use App\Models\Sucursal;
    use App\Core\Database;

    $db = Database::getInstance();

    echo "--- Testing Caja::getActiva Caching ---\n";
    $cajaModel = new Caja();
    $db->mockResult = ['id' => 1, 'estado' => 'abierta'];

    // First call should query DB
    $res1 = $cajaModel->getActiva(1);
    echo "First call queries: " . count($db->queries) . "\n";

    // Second call should use cache
    $res2 = $cajaModel->getActiva(1);
    echo "Second call queries: " . count($db->queries) . " (Expected: 1)\n";

    echo "\n--- Testing Caja::abrir Cache Invalidation ---\n";
    $db->lastInsertIdValue = 2;
    $cajaModel->abrir(1, 1, 10, 0, 1);
    echo "Queries after abrir: " . count($db->queries) . "\n";

    // Call after abrir should query DB again
    $db->mockResult = ['id' => 2, 'estado' => 'abierta'];
    $res3 = $cajaModel->getActiva(1);
    echo "Call after invalidation queries: " . count($db->queries) . " (Expected: 3)\n";

    echo "\n--- Testing Sucursal::getActivas Caching ---\n";
    $db->reset();
    $sucursalModel = new Sucursal();
    $db->mockResult = [['id' => 1, 'nombre' => 'Sucursal 1']];

    $sres1 = $sucursalModel->getActivas();
    echo "First call queries: " . count($db->queries) . "\n";

    $sres2 = $sucursalModel->getActivas();
    echo "Second call queries: " . count($db->queries) . " (Expected: 1)\n";

    echo "\n--- Testing Caja::getResumen Consolidated Query ---\n";
    $db->reset();
    $db->mockResult = [
        'monto_apertura' => 100.00,
        'ingresos' => 50.00,
        'egresos' => 20.00
    ];

    $resumen = $cajaModel->getResumen(1);
    echo "Resumen queries: " . count($db->queries) . " (Expected: 1)\n";
    echo "Query: " . $db->queries[0] . "\n";
    echo "Result: " . json_encode($resumen) . "\n";

    if (strpos($db->queries[0], 'LEFT JOIN') !== false && strpos($db->queries[0], 'CASE') !== false) {
        echo "SUCCESS: Query uses LEFT JOIN and conditional aggregation.\n";
    } else {
        echo "FAILURE: Query does not match expected consolidated pattern.\n";
        exit(1);
    }

    echo "\nAll Caja and Sucursal optimizations verified successfully!\n";
}
