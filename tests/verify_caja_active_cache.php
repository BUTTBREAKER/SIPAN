<?php

namespace App\Core {
    class Database {
        private static $instance = null;
        public $mock;

        public static function getInstance() {
            if (self::$instance === null) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        public function __call($name, $args) {
            return call_user_func_array([$this->mock, $name], $args);
        }

        public function beginTransaction() { return true; }
        public function commit() { return true; }
        public function rollBack() { return true; }
    }
}

namespace {
    require_once __DIR__ . '/../vendor/autoload.php';
    use App\Models\Caja;
    use App\Core\Database;

    // Mock Database class to capture SQL and count queries
    class MockDatabase {
        public $queryCount = 0;
        public $queries = [];

        public function fetchOne($sql, $params = []) {
            $this->queryCount++;
            $this->queries[] = ['sql' => $sql, 'params' => $params];
            return ['id' => 1, 'id_sucursal' => 1, 'estado' => 'abierta'];
        }

        public function fetchAll($sql, $params = []) {
            $this->queryCount++;
            return [['id' => 1, 'id_sucursal' => 1, 'estado' => 'abierta']];
        }

        public function execute($sql, $params = []) {
            $this->queryCount++;
            return 1;
        }

        public function lastInsertId() {
            return 101;
        }
    }

    $mockDb = new MockDatabase();
    Database::getInstance()->mock = $mockDb;

    $cajaModel = new Caja();

    echo "--- Testing Caja::getActiva Cache (Mocked) ---\n";

    // First call - should trigger DB query
    $cajaModel->getActiva(1);
    $countAfterFirst = $mockDb->queryCount;
    echo "Query count after first call: $countAfterFirst\n";

    // Second call - should NOT trigger DB query
    $cajaModel->getActiva(1);
    $countAfterSecond = $mockDb->queryCount;
    echo "Query count after second call: $countAfterSecond\n";

    if ($countAfterFirst !== $countAfterSecond) {
        echo "❌ Error: Redundant database query detected for getActiva!\n";
        exit(1);
    }
    echo "✅ Success: Request-level cache working for getActiva.\n";

    echo "\n--- Testing Cache Invalidation on abrir() ---\n";

    $cajaModel->abrir(1, 1, 100, 0, 50);

    // Call getActiva again - should trigger DB query after invalidation
    $cajaModel->getActiva(1);
    $countAfterAbrir = $mockDb->queryCount;
    echo "Query count after abrir and getActiva: $countAfterAbrir\n";

    // In abrir():
    // 1. create() calls execute() -> +1
    // 2. unset cache
    // 3. getActiva() calls fetchOne() -> +1

    if ($mockDb->queryCount !== $countAfterSecond + 2) {
        echo "❌ Error: Cache was not invalidated after abrir()! (Expected " . ($countAfterSecond + 2) . " queries, got " . $mockDb->queryCount . ")\n";
        exit(1);
    }
    echo "✅ Success: Cache invalidated after abrir().\n";

    echo "\n--- Testing Cache Invalidation on cerrar() ---\n";
    $prevCount = $mockDb->queryCount;

    $cajaModel->cerrar(1, 1, 100, 0, 50);

    // Call getActiva again
    $cajaModel->getActiva(1);

    if ($mockDb->queryCount <= $prevCount + 1) {
         echo "❌ Error: Cache was not invalidated after cerrar()!\n";
         exit(1);
    }
    echo "✅ Success: Cache invalidated after cerrar().\n";

    echo "\n--- ALL CAJA OPTIMIZATION TESTS PASSED ---\n";
}
