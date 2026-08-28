<?php
// Mock Database Class
namespace App\Core {
    class Database {
        private static $instance = null;
        public $queries = [];
        public $params = [];
        public $mockResults = [];

        public static function getInstance() {
            if (self::$instance === null) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        public function fetchAll($sql, $params = []) {
            $this->queries[] = $sql;
            $this->params[] = $params;
            return array_shift($this->mockResults) ?: [];
        }

        public function fetchOne($sql, $params = []) {
            $this->queries[] = $sql;
            $this->params[] = $params;
            return array_shift($this->mockResults) ?: null;
        }

        public function execute($sql, $params = []) {
            $this->queries[] = $sql;
            $this->params[] = $params;
            return 1;
        }
    }
}

namespace {
    use App\Models\ChatMensaje;
    use App\Core\Database;

    // Autoload classes
    require_once __DIR__ . '/../app/Models/ChatMensaje.php';

    function testGetConversaciones() {
        echo "Testing ChatMensaje::getConversaciones optimization...\n";
        $db = Database::getInstance();
        $chat = new ChatMensaje();

        $userId = 1;
        $chat->getConversaciones($userId);

        $sql = end($db->queries);
        $params = end($db->params);

        // Verify JOIN instead of correlated subqueries
        if (strpos($sql, 'LEFT JOIN (') !== false && strpos($sql, 'GROUP BY') !== false) {
            echo "✅ SUCCESS: Correlated subqueries replaced with derived table joins.\n";
        } else {
            echo "❌ FAILURE: SQL does not contain the expected optimizations.\n";
            echo "SQL: $sql\n";
            exit(1);
        }

        // Verify params count (should be 2: [userId, userId] for the joins/filters)
        if (count($params) === 2) {
            echo "✅ SUCCESS: Correct number of parameters passed.\n";
        } else {
            echo "❌ FAILURE: Expected 2 parameters, got " . count($params) . ".\n";
            var_dump($params);
            exit(1);
        }
    }

    function testContarNoLeidos() {
        echo "\nTesting ChatMensaje::contarNoLeidos optimization...\n";
        $db = Database::getInstance();
        $chat = new ChatMensaje();

        $userId = 1;
        $chat->contarNoLeidos($userId);

        $sql = end($db->queries);
        $params = end($db->params);

        // Verify JOIN instead of nested subqueries
        if (strpos($sql, 'INNER JOIN chat_participantes') !== false && strpos($sql, 'SELECT COUNT(*)') !== false) {
            echo "✅ SUCCESS: Nested subqueries replaced with INNER JOIN.\n";
        } else {
            echo "❌ FAILURE: SQL does not contain the expected JOIN optimization.\n";
            echo "SQL: $sql\n";
            exit(1);
        }

        // Verify params count (should be 2: [userId, userId])
        if (count($params) === 2) {
            echo "✅ SUCCESS: Correct number of parameters passed.\n";
        } else {
            echo "❌ FAILURE: Expected 2 parameters, got " . count($params) . ".\n";
            var_dump($params);
            exit(1);
        }
    }

    try {
        testGetConversaciones();
        testContarNoLeidos();
        echo "\n✨ All ChatMensaje optimizations verified successfully!\n";
    } catch (Exception $e) {
        echo "❌ TEST ERROR: " . $e->getMessage() . "\n";
        exit(1);
    }
}
