<?php

namespace App\Core {
    class Database {
        public static $instance = null;
        public $queries = [];
        public $lastInsertId = 100;

        public static function getInstance() {
            if (self::$instance === null) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        public function fetchOne($sql, $params = []) {
            $this->queries[] = ['sql' => $sql, 'params' => $params];
            if (strpos($sql, "FROM cajas") !== false) {
                return ['id' => 1, 'id_sucursal' => 1, 'estado' => 'abierta'];
            }
            return null;
        }

        public function fetchAll($sql, $params = []) {
            $this->queries[] = ['sql' => $sql, 'params' => $params];
            if (strpos($sql, "FROM sucursales") !== false) {
                return [['id' => 1, 'nombre' => 'Sucursal 1']];
            }
            return [];
        }

        public function execute($sql, $params = []) {
            $this->queries[] = ['sql' => $sql, 'params' => $params];
            return true;
        }

        public function lastInsertId() {
            return $this->lastInsertId++;
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

    // First call should trigger a query
    $db->queries = [];
    $cajaModel->getActiva(1);
    $queryCount = count($db->queries);
    echo "First call queries: $queryCount\n";
    if ($queryCount !== 1) {
        echo "❌ Expected 1 query, got $queryCount\n";
    }

    // Second call should NOT trigger a query
    $db->queries = [];
    $cajaModel->getActiva(1);
    $queryCount = count($db->queries);
    echo "Second call queries: $queryCount\n";
    if ($queryCount !== 0) {
        echo "❌ Expected 0 queries (cached), got $queryCount\n";
    } else {
        echo "✅ Cache hit for getActiva\n";
    }

    // Opening a caja should invalidate cache
    echo "--- Testing Caja::abrir Cache Invalidation ---\n";
    $cajaModel->abrir(1, 1, 100, 100, 50);
    $db->queries = [];
    $cajaModel->getActiva(1);
    $queryCount = count($db->queries);
    echo "Post-abrir call queries: $queryCount\n";
    if ($queryCount !== 1) {
        echo "❌ Expected 1 query (cache invalidated), got $queryCount\n";
    } else {
        echo "✅ Cache invalidated after abrir\n";
    }

    // Closing a caja should invalidate cache
    echo "--- Testing Caja::cerrar Cache Invalidation ---\n";
    // We need to mock getResumen and find for cerrar
    // find is in BaseModel, getResumen is in Caja.
    // getActiva is now cached again.
    $cajaModel->getActiva(1); // refill cache
    $db->queries = [];
    $cajaModel->cerrar(1, 1, 100, 100, 50);
    $db->queries = [];
    $cajaModel->getActiva(1);
    $queryCount = count($db->queries);
    echo "Post-cerrar call queries: $queryCount\n";
    if ($queryCount !== 1) {
        echo "❌ Expected 1 query (cache invalidated), got $queryCount\n";
    } else {
        echo "✅ Cache invalidated after cerrar\n";
    }

    echo "\n--- Testing Sucursal::getActivas Caching ---\n";
    $sucursalModel = new Sucursal();

    $db->queries = [];
    $sucursalModel->getActivas();
    $queryCount = count($db->queries);
    echo "First call queries: $queryCount\n";
    if ($queryCount !== 1) {
        echo "❌ Expected 1 query, got $queryCount\n";
    }

    $db->queries = [];
    $sucursalModel->getActivas();
    $queryCount = count($db->queries);
    echo "Second call queries: $queryCount\n";
    if ($queryCount !== 0) {
        echo "❌ Expected 0 queries (cached), got $queryCount\n";
    } else {
        echo "✅ Cache hit for getActivas\n";
    }
}
