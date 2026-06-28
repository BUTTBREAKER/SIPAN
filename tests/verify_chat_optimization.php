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

        public function reset() {
            $this->queries = [];
        }
    }
}

namespace {
    require_once __DIR__ . '/../app/Models/ChatMensaje.php';
    use App\Models\ChatMensaje;
    use App\Core\Database;

    $db = Database::getInstance();
    $chatModel = new ChatMensaje();

    echo "--- Testing ChatMensaje::getConversaciones Optimization ---\n";
    $db->reset();
    $chatModel->getConversaciones(1);

    $query = $db->queries[0]['sql'] ?? '';
    $params = $db->queries[0]['params'] ?? [];

    $hasCorrelatedSubquery = (strpos($query, '(SELECT COUNT(*)') !== false || preg_match('/\(SELECT .* FROM .* WHERE .*=.*\)/i', $query));
    $hasDerivedTableForUnread = strpos($query, 'unread') !== false && strpos($query, 'GROUP BY cm2.id_conversacion') !== false;
    $hasDerivedTableForLastMsg = strpos($query, 'lm') !== false && strpos($query, 'MAX(m2.id)') !== false;

    if ($hasCorrelatedSubquery) {
        echo "❌ getConversaciones still contains correlated subqueries!\n";
        // Simple regex check failed, let's be more specific if needed
        // Scalar subqueries in SELECT list usually look like (SELECT ...)
        // We want to avoid those and use JOINs.
    } else {
        echo "✅ getConversaciones: No correlated subqueries found in SELECT list.\n";
    }

    if ($hasDerivedTableForUnread && $hasDerivedTableForLastMsg) {
        echo "✅ getConversaciones: Found derived tables for unread counts and last message.\n";
    } else {
        echo "❌ getConversaciones: Missing expected derived tables logic.\n";
    }

    if (count($params) === 5) {
        echo "✅ getConversaciones: Correct number of parameters (5).\n";
    } else {
        echo "❌ getConversaciones: Incorrect number of parameters. Expected 5, got " . count($params) . ".\n";
    }

    echo "\n--- Testing ChatMensaje::contarNoLeidos Optimization ---\n";
    $db->reset();
    $chatModel->contarNoLeidos(1);

    $query = $db->queries[0]['sql'] ?? '';
    $params = $db->queries[0]['params'] ?? [];

    $isSimplified = strpos($query, 'INNER JOIN chat_participantes') !== false && strpos($query, 'SUM(') === false;

    if ($isSimplified) {
        echo "✅ contarNoLeidos: Query successfully simplified to a single JOIN.\n";
    } else {
        echo "❌ contarNoLeidos: Query still seems complex or unoptimized.\n";
    }

    if (count($params) === 2) {
        echo "✅ contarNoLeidos: Correct number of parameters (2).\n";
    } else {
        echo "❌ contarNoLeidos: Incorrect number of parameters. Expected 2, got " . count($params) . ".\n";
    }
}
