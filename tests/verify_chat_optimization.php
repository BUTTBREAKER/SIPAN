<?php

namespace App\Core {
    class Database {
        private static $instance = null;
        public $queries = [];
        public $params = [];

        public static function getInstance() {
            if (self::$instance === null) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        public function fetchAll($sql, $params = []) {
            $this->queries[] = $sql;
            $this->params[] = $params;
            return [];
        }

        public function fetchOne($sql, $params = []) {
            $this->queries[] = $sql;
            $this->params[] = $params;
            return [];
        }

        public function execute($sql, $params = []) {
            $this->queries[] = $sql;
            $this->params[] = $params;
            return true;
        }
    }
}

namespace {
    require_once __DIR__ . '/../app/Models/ChatMensaje.php';

    use App\Models\ChatMensaje;
    use App\Core\Database;

    $chatModel = new ChatMensaje();
    $db = Database::getInstance();

    echo "Verifying ChatMensaje SQL efficiency...\n";

    // 1. Check getConversaciones
    $chatModel->getConversaciones(123);
    $query1 = end($db->queries);
    // The query uses: LEFT JOIN ( SELECT cm2.id_conversacion, COUNT(*) as count FROM chat_mensajes cm2 ...
    $isOptimized1 = (strpos($query1, '(SELECT COUNT(*)') === false) &&
                    (strpos($query1, 'LEFT JOIN (') !== false);

    echo "getConversaciones query: " . (strpos($query1, '(SELECT COUNT(*)') !== false ? "UNOPTIMIZED (Correlated subquery)" : "OPTIMIZED") . "\n";

    // 2. Check contarNoLeidos
    $chatModel->contarNoLeidos(123);
    $query2 = end($db->queries);
    $isOptimized2 = (strpos($query2, 'SELECT (') === false) &&
                    (strpos($query2, 'INNER JOIN') !== false);

    echo "contarNoLeidos query: " . (strpos($query2, 'SELECT (') !== false ? "UNOPTIMIZED (Nested subquery)" : "OPTIMIZED") . "\n";

    if ($isOptimized1 && $isOptimized2) {
        echo "\n✅ SUCCESS: Queries are optimized.\n";
        exit(0);
    } else {
        echo "\n❌ FAILURE: One or more queries are still using sub-optimal patterns.\n";
        exit(1);
    }
}
