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
            return true;
        }
    }
}

namespace {
    require_once __DIR__ . '/../vendor/autoload.php';
    use App\Models\ChatMensaje;
    use App\Core\Database;

    function verifyChatOptimization() {
        $db = Database::getInstance();
        $model = new ChatMensaje();
        $userId = 1;

        echo "--- Verifying ChatMensaje::getConversaciones Optimization ---\n";
        $db->queries = [];
        $model->getConversaciones($userId);
        $sql = $db->queries[0]['sql'];

        // Check for correlated subqueries in SELECT list
        // Simplified check: look for "(SELECT" after "SELECT" but before "FROM"
        $selectPart = substr($sql, 0, stripos($sql, ' FROM '));
        if (preg_match('/\(\s*SELECT/i', $selectPart)) {
            echo "❌ Correlated subquery found in SELECT list of getConversaciones\n";
        } else {
            echo "✅ No correlated subquery in SELECT list of getConversaciones\n";
        }

        // Check for correlated subquery in JOIN
        if (preg_match('/JOIN\s*\(?\s*SELECT.*WHERE.*=.*c\.id/i', $sql) || preg_match('/JOIN\s*chat_mensajes\s*m\s*ON\s*m\.id\s*=\s*\(\s*SELECT/i', $sql)) {
             echo "❌ Correlated subquery found in JOIN of getConversaciones\n";
        } else {
             echo "✅ No correlated subquery in JOIN of getConversaciones\n";
        }

        echo "\n--- Verifying ChatMensaje::contarNoLeidos Optimization ---\n";
        $db->queries = [];
        $model->contarNoLeidos($userId);
        $sql = $db->queries[0]['sql'];

        // A better check: count occurrences of SELECT.
        // The original has 3 SELECTs. Optimized should have 1 or 2 (depending if using a derived table).
        // More importantly, it shouldn't have a SELECT inside the column list.
        $selectCount = substr_count(strtoupper($sql), 'SELECT');
        $hasSubqueryInSelect = preg_match('/SELECT.*\(SELECT/is', $sql) && !preg_match('/FROM\s*\(SELECT/is', $sql);

        if ($selectCount > 2 || $hasSubqueryInSelect) {
            echo "❌ Potential scalar or nested subquery found in contarNoLeidos (Select count: $selectCount)\n";
        } else {
            echo "✅ No scalar subquery in contarNoLeidos\n";
        }
    }

    try {
        verifyChatOptimization();
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
}
