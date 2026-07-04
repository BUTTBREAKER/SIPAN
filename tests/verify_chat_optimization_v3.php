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
    require_once __DIR__ . '/../app/Models/ChatMensaje.php';

    use App\Models\ChatMensaje;
    use App\Core\Database;

    function verifyOptimization() {
        $db = Database::getInstance();
        $model = new ChatMensaje();
        $userId = 123;

        echo "--- Testing ChatMensaje::contarNoLeidos Optimization ---\n";
        $db->queries = [];
        $model->contarNoLeidos($userId);

        if (empty($db->queries)) {
            die("❌ No queries executed for contarNoLeidos\n");
        }

        $sql = $db->queries[0]['sql'];
        $params = $db->queries[0]['params'];

        echo "SQL: $sql\n";

        // Check for scalar subquery in SELECT list
        if (preg_match('/SELECT\s+\(SELECT/i', $sql)) {
            die("❌ Scalar subquery detected in contarNoLeidos\n");
        }

        // Check if it's a JOIN based approach
        if (stripos($sql, 'JOIN') === false) {
            die("❌ No JOIN detected in contarNoLeidos optimization\n");
        }

        echo "✅ contarNoLeidos optimization passed (No scalar subquery)\n\n";

        echo "--- Testing ChatMensaje::getConversaciones Optimization ---\n";
        $db->queries = [];
        $model->getConversaciones($userId);

        if (empty($db->queries)) {
            die("❌ No queries executed for getConversaciones\n");
        }

        $sql = $db->queries[0]['sql'];
        echo "SQL: $sql\n";

        // Extract SELECT part to check for scalar subqueries
        $fromPos = stripos($sql, 'FROM');
        $selectPart = substr($sql, 0, $fromPos);

        if (preg_match('/\(SELECT/i', $selectPart)) {
             // Exception for derived tables in FROM/JOIN is fine, but not in SELECT list
             // However, getConversaciones usually has them in SELECT list for counts
             die("❌ Scalar subquery detected in getConversaciones SELECT list\n");
        }

        // Check for the correlated subquery in JOIN m.id = (SELECT ...)
        if (stripos($sql, 'm.id = (SELECT') !== false) {
            die("❌ Correlated subquery detected in JOIN for latest message\n");
        }

        echo "✅ getConversaciones optimization passed (No correlated subqueries in SELECT/JOIN)\n";
    }

    try {
        verifyOptimization();
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
        exit(1);
    }
}
