<?php

namespace App\Core {
    class Database {
        private static $instance = null;
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

    function hasScalarSubquery($sql) {
        // Check for (SELECT ...) in the SELECT list or as a value
        // We look for a SELECT that is inside parentheses and is followed by AS or is in a SELECT list (not after FROM)

        // A simple way is to check if there is a (SELECT ...) that is NOT part of a FROM or JOIN clause
        // This is tricky with regex, but we can try to look for SELECTs that are not preceded by FROM ( or JOIN (

        // Let's use a simpler heuristic for this codebase:
        // Scalar subqueries for unread counts often look like (SELECT COUNT(*) FROM chat_mensajes ...)
        if (preg_match('/\(\s*SELECT\s+COUNT\(\*\)\s+FROM\s+chat_mensajes/is', $sql)) {
            return true;
        }

        // Scalar subqueries for latest message ID often look like m.id = (SELECT ...)
        if (preg_match('/=\s*\(\s*SELECT.*?FROM\s+chat_mensajes/is', $sql)) {
            return true;
        }

        return false;
    }

    function verifyChatOptimization() {
        $db = Database::getInstance();
        $chatModel = new ChatMensaje();
        $userId = 1;

        echo "--- Checking ChatMensaje::contarNoLeidos ---\n";
        $db->queries = [];
        $chatModel->contarNoLeidos($userId);
        $query = $db->queries[0]['sql'];

        if (hasScalarSubquery($query)) {
            echo "❌ contarNoLeidos contains a scalar subquery (Unoptimized)\n";
        } else {
            echo "✅ contarNoLeidos uses JOINs (Optimized)\n";
        }
        echo "SQL: " . str_replace("\n", " ", $query) . "\n\n";

        echo "--- Checking ChatMensaje::getConversaciones ---\n";
        $db->queries = [];
        $chatModel->getConversaciones($userId);
        $query = $db->queries[0]['sql'];

        if (hasScalarSubquery($query)) {
            echo "❌ getConversaciones contains scalar subqueries (Unoptimized)\n";
        } else {
            echo "✅ getConversaciones SELECT list is clean (Optimized)\n";
        }

        echo "SQL: " . str_replace("\n", " ", $query) . "\n";
    }

    verifyChatOptimization();
}
