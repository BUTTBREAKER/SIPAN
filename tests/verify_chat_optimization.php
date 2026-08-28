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

        public function lastInsertId() {
            return 1;
        }
    }
}

namespace {
    require_once __DIR__ . '/../vendor/autoload.php';
    use App\Models\ChatMensaje;
    use App\Core\Database;

    function verify_chat_sql() {
        $db = Database::getInstance();
        $model = new ChatMensaje();

        echo "--- Testing ChatMensaje::contarNoLeidos ---\n";
        $db->queries = [];
        $model->contarNoLeidos(1);
        $sql = $db->queries[0]['sql'];

        // Check for correlated subquery in the inner SELECT
        $hasCorrelated = preg_match('/SELECT\s+\(\s*SELECT\s+COUNT/i', $sql);
        if ($hasCorrelated) {
            echo "RESULT_CONTAR: CORRELATED\n";
            echo "SQL: " . $sql . "\n";
        } else {
            echo "RESULT_CONTAR: OPTIMIZED\n";
        }

        echo "\n--- Testing ChatMensaje::getConversaciones ---\n";
        $db->queries = [];
        $model->getConversaciones(1);
        $sql = $db->queries[0]['sql'];

        // Extract SELECT list (before the first top-level FROM)
        // This is a bit tricky with nested queries, but for ChatMensaje it's usually enough
        $parts = preg_split('/\s+FROM\s+/i', $sql, 2);
        $selectList = $parts[0];

        $hasCorrelatedInSelect = preg_match('/\(SELECT/i', $selectList);

        // Also check for correlated subqueries in JOIN conditions
        $hasCorrelatedInJoin = preg_match('/ON\s+[\w\.]+\s+=\s+\(SELECT/i', $sql);

        if ($hasCorrelatedInSelect || $hasCorrelatedInJoin) {
            echo "RESULT_CONVERSACIONES: CORRELATED\n";
            if ($hasCorrelatedInSelect) echo "- Found correlated subquery in SELECT list\n";
            if ($hasCorrelatedInJoin) echo "- Found correlated subquery in JOIN condition\n";
            echo "SQL: " . $sql . "\n";
        } else {
            echo "RESULT_CONVERSACIONES: OPTIMIZED\n";
        }
    }

    verify_chat_sql();
}
