<?php

namespace App\Core {
    class Database {
        private static $instance = null;
        public $queries = [];
        public $results = [];

        public static function getInstance() {
            if (self::$instance === null) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        public function fetchOne($sql, $params = []) {
            $this->queries[] = ['sql' => $sql, 'params' => $params];
            return array_shift($this->results);
        }

        public function fetchAll($sql, $params = []) {
            $this->queries[] = ['sql' => $sql, 'params' => $params];
            return array_shift($this->results);
        }

        public function execute($sql, $params = []) {
            $this->queries[] = ['sql' => $sql, 'params' => $params];
            return 1;
        }

        public function lastInsertId() {
            return 123;
        }
    }
}

namespace {
    require_once __DIR__ . '/../app/Models/BaseModel.php';
    require_once __DIR__ . '/../app/Models/Caja.php';

    use App\Models\Caja;
    use App\Core\Database;

    $db = Database::getInstance();
    $caja = new Caja();

    // Test getResumen optimization
    echo "Testing Caja::getResumen optimization...\n";
    $db->results = [
        [
            'monto_apertura' => 100.0,
            'ingresos' => 50.0,
            'egresos' => 20.0
        ]
    ];

    $resumen = $caja->getResumen(1);

    if (count($db->queries) !== 1) {
        echo "FAILED: Expected 1 query, got " . count($db->queries) . "\n";
        exit(1);
    }

    $query = $db->queries[0];
    if (strpos($query['sql'], 'LEFT JOIN caja_movimientos') === false) {
        echo "FAILED: Query does not contain LEFT JOIN\n";
        exit(1);
    }

    if ($resumen['esperado'] !== 130.0) {
        echo "FAILED: Expected 'esperado' to be 130.0, got " . $resumen['esperado'] . "\n";
        exit(1);
    }

    echo "Caja::getResumen optimization verified successfully!\n";

    // Test Caja::getActiva caching
    echo "Testing Caja::getActiva caching...\n";
    $db->queries = [];
    $db->results = [['id' => 1, 'id_sucursal' => 10, 'estado' => 'abierta']];

    $c1 = $caja->getActiva(10);
    $c2 = $caja->getActiva(10);

    if (count($db->queries) !== 1) {
        echo "FAILED: Expected 1 query for 2 calls to getActiva, got " . count($db->queries) . "\n";
        exit(1);
    }
    echo "Caja::getActiva caching verified!\n";

    // Test Caja cache invalidation on abrir
    echo "Testing Caja cache invalidation on abrir...\n";
    $db->results = [124]; // lastInsertId from create
    $caja->abrir(10, 1, 0, 0, 36);

    $db->results = [['id' => 124, 'id_sucursal' => 10, 'estado' => 'abierta']];
    $c3 = $caja->getActiva(10);
    if (count($db->queries) !== 3) { // 1 prev + 1 create + 1 fetch
        echo "FAILED: Expected 3 queries total, got " . count($db->queries) . "\n";
        exit(1);
    }
    echo "Caja cache invalidation on abrir verified!\n";

    // Test Sucursal::getActivas caching
    require_once __DIR__ . '/../app/Models/Sucursal.php';
    $sucursal = new \App\Models\Sucursal();
    echo "Testing Sucursal::getActivas caching...\n";
    $db->queries = [];
    $db->results = [[['id' => 1, 'nombre' => 'S1']]];

    $s1 = $sucursal->getActivas();
    $s2 = $sucursal->getActivas();

    if (count($db->queries) !== 1) {
        echo "FAILED: Expected 1 query for 2 calls to getActivas, got " . count($db->queries) . "\n";
        exit(1);
    }
    echo "Sucursal::getActivas caching verified!\n";
}
