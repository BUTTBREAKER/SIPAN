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
            return 1;
        }
    }
}

namespace {
    require_once __DIR__ . '/../app/Models/ChatMensaje.php';
    use App\Models\ChatMensaje;
    use App\Core\Database;

    $model = new ChatMensaje();
    $db = Database::getInstance();

    echo "Testing ChatMensaje optimizations...\n";

    // Test getConversaciones
    $userId = 1;
    $model->getConversaciones($userId);
    $lastQuery = end($db->queries);
    $lastParams = end($db->params);

    echo "Checking getConversaciones SQL...\n";
    if (strpos($lastQuery, '(SELECT COUNT(*)') !== false || strpos($lastQuery, '(SELECT m2.id') !== false) {
        echo "FAILED: getConversaciones still contains correlated subqueries.\n";
        exit(1);
    }

    if (strpos($lastQuery, 'last_msg') === false || strpos($lastQuery, 'unr') === false) {
        echo "FAILED: getConversaciones is missing optimized derived table joins.\n";
        exit(1);
    }

    if (count($lastParams) !== 5) {
        echo "FAILED: getConversaciones expects 5 parameters, got " . count($lastParams) . "\n";
        exit(1);
    }
    echo "SUCCESS: getConversaciones query is optimized.\n";

    // Test contarNoLeidos
    $model->contarNoLeidos($userId);
    $lastQuery = end($db->queries);
    $lastParams = end($db->params);

    echo "Checking contarNoLeidos SQL...\n";
    if (strpos($lastQuery, 'SELECT (') !== false || strpos($lastQuery, 'FROM (') !== false) {
        echo "FAILED: contarNoLeidos still contains nested/correlated subqueries.\n";
        exit(1);
    }

    if (strpos($lastQuery, 'INNER JOIN chat_mensajes') === false) {
        echo "FAILED: contarNoLeidos is missing the direct JOIN optimization.\n";
        exit(1);
    }
    echo "SUCCESS: contarNoLeidos query is optimized.\n";

    echo "\nAll chat optimizations verified successfully.\n";
}
