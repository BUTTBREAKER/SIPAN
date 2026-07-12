<?php

// Defined to avoid 'Class not found' errors during model instantiation
namespace App\Core {
    class Database {
        private static $instance = null;
        public $lastQuery = '';
        public $lastParams = [];

        public static function getInstance() {
            if (self::$instance === null) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        public function fetchAll($sql, $params = []) {
            $this->lastQuery = $sql;
            $this->lastParams = $params;
            return [];
        }

        public function fetchOne($sql, $params = []) {
            $this->lastQuery = $sql;
            $this->lastParams = $params;
            return [];
        }

        public function execute($sql, $params = []) {
            $this->lastQuery = $sql;
            $this->lastParams = $params;
            return 1;
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

    $db = Database::getInstance();
    $model = new ChatMensaje();

    echo "--- Testing ChatMensaje::getConversaciones Optimization ---\n";
    $model->getConversaciones(1);
    $sql = $db->lastQuery;
    echo "SQL: $sql\n";

    // Strip comments to avoid false positives
    $cleanSql = preg_replace('/--.*$/m', '', $sql);

    // Identify SELECT list
    $fromPos = strpos(strtoupper($cleanSql), 'FROM');
    $selectList = substr($cleanSql, 0, $fromPos);

    if (preg_match('/\(SELECT/i', $selectList)) {
        echo "❌ Error: Correlated subquery detected in SELECT list of getConversaciones.\n";
        exit(1);
    }

    if (strpos($sql, 'unread') === false || strpos($sql, 'm_last') === false) {
        echo "❌ Error: Optimization JOINs (unread or m_last) not found in getConversaciones SQL.\n";
        exit(1);
    }

    if (count($db->lastParams) !== 5) {
        echo "❌ Error: Parameter count mismatch in getConversaciones. Expected 5, got " . count($db->lastParams) . ".\n";
        exit(1);
    }

    echo "✅ getConversaciones optimization verified!\n\n";

    echo "--- Testing ChatMensaje::contarNoLeidos Optimization ---\n";
    $model->contarNoLeidos(1);
    $sql = $db->lastQuery;
    echo "SQL: $sql\n";

    if (preg_match('/SELECT.*\(SELECT/is', $sql)) {
        echo "❌ Error: Nested/Correlated subquery detected in contarNoLeidos.\n";
        exit(1);
    }

    if (strpos(strtoupper($sql), 'INNER JOIN') === false && strpos(strtoupper($sql), 'JOIN') === false) {
        echo "❌ Error: JOIN not found in contarNoLeidos SQL.\n";
        exit(1);
    }

    echo "✅ contarNoLeidos optimization verified!\n";
}
