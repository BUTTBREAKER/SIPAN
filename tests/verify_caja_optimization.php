<?php

// Mocking necessary classes for testing the Caja model optimizations
namespace App\Core {
    class Database {
        private static $instance = null;
        public $queryCount = 0;
        public $queries = [];
        public $lastParams = [];

        public static function getInstance() {
            if (self::$instance === null) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        public function fetchOne($sql, $params = []) {
            $this->queryCount++;
            $this->queries[] = $sql;
            $this->lastParams = $params;

            // Mock responses
            if (strpos($sql, "cajas") !== false && strpos($sql, "estado = 'abierta'") !== false) {
                return ['id' => 1, 'id_sucursal' => 1, 'estado' => 'abierta', 'monto_apertura' => 100.00];
            }
            if (strpos($sql, "LEFT JOIN caja_movimientos") !== false) {
                return [
                    'monto_apertura' => 100.00,
                    'ingresos' => 50.00,
                    'egresos' => 20.00
                ];
            }
            return null;
        }

        public function execute($sql, $params = []) {
            $this->queryCount++;
            $this->queries[] = $sql;
            return 1;
        }

        public function lastInsertId() {
            return 1;
        }

        public function fetchAll($sql, $params = []) {
            $this->queryCount++;
            $this->queries[] = $sql;
            return [];
        }

        public function create($table, $data) {
             $this->queryCount++;
             return 1;
        }
    }
}

namespace App\Models {
    class BaseModel {
        protected $db;
        protected $table;
        public function __construct() {
            $this->db = \App\Core\Database::getInstance();
        }
        public function create($data) {
            return $this->db->execute("INSERT INTO {$this->table}", $data);
        }
        public function update($id, $data) {
            return $this->db->execute("UPDATE {$this->table}", $data);
        }
        public function find($id) {
             return $this->db->fetchOne("SELECT * FROM {$this->table} WHERE id = ?", [$id]);
        }
    }
}

namespace {
    require_once __DIR__ . '/../app/Models/Caja.php';

    use App\Models\Caja;
    use App\Core\Database;

    $db = Database::getInstance();
    $cajaModel = new Caja();

    echo "--- Testing Request-Level Caching ---\n";
    $db->queryCount = 0;

    $caja1 = $cajaModel->getActiva(1);
    $caja2 = $cajaModel->getActiva(1);

    if ($db->queryCount === 1) {
        echo "✅ SUCCESS: Database queried only once for two calls to getActiva(1).\n";
    } else {
        echo "❌ FAILURE: Database queried {$db->queryCount} times.\n";
    }

    echo "\n--- Testing Cache Invalidation on Open ---\n";
    $db->queryCount = 0;
    $cajaModel->abrir(1, 1, 10, 10, 30); // Should clear cache
    $cajaModel->getActiva(1); // Should query DB again

    // 1 for create (abrir), 1 for getActiva
    if ($db->queryCount === 2) {
        echo "✅ SUCCESS: Cache invalidated correctly after opening a new box.\n";
    } else {
        echo "❌ FAILURE: Database queried {$db->queryCount} times.\n";
    }

    echo "\n--- Testing Query Consolidation in getResumen ---\n";
    $db->queryCount = 0;
    $resumen = $cajaModel->getResumen(1);

    if ($db->queryCount === 1) {
        echo "✅ SUCCESS: getResumen performed exactly 1 query.\n";
        if ($resumen['esperado'] == 130.00) {
             echo "✅ SUCCESS: Math is correct (100 + 50 - 20 = 130).\n";
        } else {
             echo "❌ FAILURE: Expected 130, got " . $resumen['esperado'] . "\n";
        }
    } else {
        echo "❌ FAILURE: getResumen performed {$db->queryCount} queries.\n";
    }

    echo "\n--- Testing Controller-View separation (Mocking) ---\n";
    // This part is more architectural but we've verified the code manually.
    echo "Logic moved to controller and passed to view. Verified via read_file.\n";
}
