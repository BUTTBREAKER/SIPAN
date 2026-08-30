<?php

namespace App\Core {
    class Database {
        public static $instance = null;
        public $queries = [];

        public static function getInstance() {
            if (self::$instance === null) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        public function fetchAll($sql, $params = []) {
            $this->queries[] = ['sql' => $sql, 'params' => $params];
            return [];
        }

        public function fetchOne($sql, $params = []) {
            $this->queries[] = ['sql' => $sql, 'params' => $params];
            return [];
        }

        public function execute($sql, $params = []) {
            $this->queries[] = ['sql' => $sql, 'params' => $params];
            return 1;
        }
    }
}

namespace {
    require_once __DIR__ . '/../app/Models/ChatMensaje.php';
    use App\Models\ChatMensaje;
    use App\Core\Database;

    function verifyOptimization() {
        $db = Database::getInstance();
        $model = new ChatMensaje();

        echo "--- Testing ChatMensaje::getConversaciones Optimization ---\n";
        $db->queries = [];
        $model->getConversaciones(1);

        $sql = $db->queries[0]['sql'];

        // Regex to detect correlated subqueries: scalar subqueries in SELECT
        // This looks for a subquery that starts after a comma or the initial SELECT,
        // and is not part of a JOIN or FROM.
        $hasCorrelated = preg_match('/SELECT\s+.*,\s*\(\s*SELECT/is', $sql) || preg_match('/SELECT\s+\(\s*SELECT/is', $sql);

        if ($hasCorrelated) {
            echo "❌ ChatMensaje::getConversaciones contains correlated subqueries!\n";
            echo "SQL: " . $sql . "\n";
        } else {
            echo "✅ ChatMensaje::getConversaciones does not contain obvious correlated subqueries.\n";
        }

        // Check for user-level filter in derived tables
        $hasUserFilterInSubquery = preg_match('/FROM\s+chat_mensajes.*WHERE\s+cp_sub\.id_usuario\s*=\s*\?/is', $sql);
        if (!$hasUserFilterInSubquery) {
            echo "❌ ChatMensaje::getConversaciones missing user filter in derived tables (Performance Risk)!\n";
        } else {
            echo "✅ ChatMensaje::getConversaciones contains user filter in derived tables.\n";
        }

        echo "\n--- Testing ChatMensaje::contarNoLeidos Optimization ---\n";
        $db->queries = [];
        $model->contarNoLeidos(1);

        $sql = $db->queries[0]['sql'];
        // For contarNoLeidos, we just want to make sure it's not using a nested subquery pattern like before
        $hasCorrelated = preg_match('/SELECT\s+.*,\s*\(\s*SELECT/is', $sql) || preg_match('/SELECT\s+.*\(\s*SELECT/is', $sql);

        if ($hasCorrelated) {
            echo "❌ ChatMensaje::contarNoLeidos contains correlated subqueries!\n";
            echo "SQL: " . $sql . "\n";
        } else {
            echo "✅ ChatMensaje::contarNoLeidos does not contain obvious correlated subqueries.\n";
        }

        return !$hasCorrelated;
    }

    verifyOptimization();
}
