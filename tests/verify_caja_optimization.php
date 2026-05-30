<?php

namespace App\Core {
    class Database {
        private static $instance = null;
        public $queries = [];
        public $results = [];
        public $lastInsertId = 1;

        public static function getInstance() {
            if (self::$instance === null) self::$instance = new self();
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
            return $this->lastInsertId;
        }

        public function reset() {
            $this->queries = [];
            $this->results = [];
        }
    }
}

namespace {
    require_once __DIR__ . '/../app/Models/BaseModel.php';
    require_once __DIR__ . '/../app/Models/Caja.php';

    use App\Models\Caja;
    use App\Core\Database;

    $db = Database::getInstance();
    $cajaModel = new Caja();

    echo "--- Testing Caja::getResumen (Consolidated Query) ---\n";
    $db->reset();
    $db->results[] = [
        'monto_apertura' => 100.0,
        'ingresos' => 50.0,
        'egresos' => 20.0
    ];

    $resumen = $cajaModel->getResumen(1);

    if (count($db->queries) === 1) {
        echo "✅ PASS: Only 1 query executed.\n";
    } else {
        echo "❌ FAIL: " . count($db->queries) . " queries executed.\n";
    }

    if ($resumen['esperado'] == 130.0) {
        echo "✅ PASS: Correct expected total (130.0).\n";
    } else {
        echo "❌ FAIL: Incorrect expected total: " . $resumen['esperado'] . "\n";
    }

    echo "\n--- Testing Caja::getActiva (Request-level Cache) ---\n";
    $db->reset();
    $db->results[] = ['id' => 1, 'id_sucursal' => 1, 'estado' => 'abierta'];

    // First call - should query DB
    $caja1 = $cajaModel->getActiva(1);
    if (count($db->queries) === 1) {
        echo "✅ PASS: First call queried DB.\n";
    } else {
        echo "❌ FAIL: First call didn't query DB.\n";
    }

    // Second call - should use cache
    $caja2 = $cajaModel->getActiva(1);
    if (count($db->queries) === 1) {
        echo "✅ PASS: Second call used cache (no new query).\n";
    } else {
        echo "❌ FAIL: Second call queried DB again.\n";
    }

    echo "\n--- Testing Cache Invalidation (abrir) ---\n";
    $db->reset();
    $db->results[] = ['id' => 2, 'id_sucursal' => 1, 'estado' => 'abierta'];

    // abrir should clear cache
    $cajaModel->abrir(1, 1, 100, 0, 1);

    // This call should query DB again
    $cajaModel->getActiva(1);
    if (count($db->queries) >= 2) { // 1 for create (in abrir), 1 for getActiva
        echo "✅ PASS: Cache invalidated after abrir().\n";
    } else {
        echo "❌ FAIL: Cache NOT invalidated after abrir().\n";
    }

    echo "\n--- Testing Cache Invalidation (cerrar) ---\n";
    $db->reset();
    // Setup for cerrar: getResumen (1 query), find (1 query), update (1 query)
    $db->results[] = ['monto_apertura' => 100, 'ingresos' => 0, 'egresos' => 0]; // for getResumen
    $db->results[] = ['id' => 2, 'id_sucursal' => 1]; // for find

    // Pre-fill cache
    $db->results[] = ['id' => 2, 'id_sucursal' => 1, 'estado' => 'abierta'];
    $cajaModel->getActiva(1); // queries DB

    $cajaModel->cerrar(2, 1, 100, 0, 1); // should clear cache

    $db->reset();
    $db->results[] = null; // simulate no active box
    $cajaModel->getActiva(1);
    if (count($db->queries) === 1) {
        echo "✅ PASS: Cache invalidated after cerrar().\n";
    } else {
        echo "❌ FAIL: Cache NOT invalidated after cerrar().\n";
    }
}
