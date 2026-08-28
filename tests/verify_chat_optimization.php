<?php

namespace App\Core {
    class Database {
        private static $instance = null;
        public $lastSql = '';
        public $lastParams = [];

        public static function getInstance() {
            if (self::$instance === null) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        public function fetchAll($sql, $params = []) {
            $this->lastSql = $sql;
            $this->lastParams = $params;
            return [];
        }

        public function fetchOne($sql, $params = []) {
            $this->lastSql = $sql;
            $this->lastParams = $params;
            return [];
        }

        public function execute($sql, $params = []) {
            $this->lastSql = $sql;
            $this->lastParams = $params;
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
    $userId = 1;

    echo "--- Testing ChatMensaje Optimizations ---\n";

    // Test 1: contarNoLeidos
    echo "Testing contarNoLeidos...\n";
    $model->contarNoLeidos($userId);
    $sql = $db->lastSql;

    $hasCorrelatedSubquery = preg_match('/SELECT\s+\(\s*SELECT\s+COUNT\(\*\)/i', $sql);
    if ($hasCorrelatedSubquery) {
        echo "❌ contarNoLeidos: Still contains correlated subquery.\n";
    } else {
        echo "✅ contarNoLeidos: No correlated subquery detected.\n";
    }

    // Test 2: getConversaciones
    echo "Testing getConversaciones...\n";
    $model->getConversaciones($userId);
    $sql = $db->lastSql;

    $hasCorrelatedSubqueryCount = preg_match('/SELECT\s+COUNT\(\*\)/i', $sql) && !strpos(strtolower($sql), 'group by');
    // More specific for getConversaciones
    $hasCorrelatedSubqueryCount = preg_match('/\(\s*SELECT\s+COUNT\(\*\)/i', $sql);
    $hasCorrelatedSubqueryLastMsg = strpos($sql, 'm.id = (') !== false;

    if ($hasCorrelatedSubqueryCount || $hasCorrelatedSubqueryLastMsg) {
        echo "❌ getConversaciones: Still contains correlated subqueries.\n";
        if ($hasCorrelatedSubqueryCount) echo "   - Correlated count detected\n";
        if ($hasCorrelatedSubqueryLastMsg) echo "   - Correlated last message detected\n";
    } else {
        echo "✅ getConversaciones: No correlated subqueries detected.\n";
    }

    echo "\nSummary of SQL queries:\n";
    echo "contarNoLeidos SQL:\n$sql\n"; // Note: this will be the last SQL executed (getConversaciones)

    // Rerunning to get specific SQLs for output
    $model->contarNoLeidos($userId);
    echo "\ncontarNoLeidos SQL:\n" . $db->lastSql . "\n";

    $model->getConversaciones($userId);
    echo "\ngetConversaciones SQL:\n" . $db->lastSql . "\n";
}
