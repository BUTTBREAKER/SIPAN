<?php

namespace App\Core {
    class Database {
        private static $instance = null;
        public $lastQuery = '';
        public $lastParams = [];
        public $queries = [];

        public static function getInstance() {
            if (self::$instance === null) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        public function fetchAll($sql, $params = []) {
            $this->queries[] = ['sql' => $sql, 'params' => $params];
            $this->lastQuery = $sql;
            $this->lastParams = $params;
            return [];
        }

        public function fetchOne($sql, $params = []) {
            $this->queries[] = ['sql' => $sql, 'params' => $params];
            $this->lastQuery = $sql;
            $this->lastParams = $params;
            return [];
        }
    }
}

namespace {
    require_once __DIR__ . '/../app/Models/ChatMensaje.php';
    use App\Models\ChatMensaje;
    use App\Core\Database;

    $db = Database::getInstance();
    $chatModel = new ChatMensaje();

    echo "--- Verifying ChatMensaje SQL Optimizations ---\n";

    // Test 1: getConversaciones
    echo "Testing getConversaciones...\n";
    $chatModel->getConversaciones(1);
    $sql1 = $db->lastQuery;

    // Check for correlated subqueries
    $hasCorrelatedUnread = (strpos($sql1, '(SELECT COUNT(*) FROM chat_mensajes') !== false);
    $hasCorrelatedLastMsg = (strpos($sql1, '= (SELECT m2.id') !== false);

    if ($hasCorrelatedUnread) {
        echo "❌ Error: getConversaciones still has correlated subquery for unread count.\n";
        exit(1);
    }
    if ($hasCorrelatedLastMsg) {
        echo "❌ Error: getConversaciones still has correlated subquery for last message.\n";
        exit(1);
    }

    // Check for derived tables
    if (strpos($sql1, 'GROUP BY id_conversacion') === false) {
        echo "❌ Error: getConversaciones missing GROUP BY in derived table for last message.\n";
        exit(1);
    }

    // Test 2: contarNoLeidos
    echo "Testing contarNoLeidos...\n";
    $chatModel->contarNoLeidos(1);
    $sql2 = $db->lastQuery;

    $hasCorrelatedTotalUnread = (strpos($sql2, '(SELECT COUNT(*) FROM chat_mensajes') !== false);
    if ($hasCorrelatedTotalUnread) {
        echo "❌ Error: contarNoLeidos still has correlated subquery.\n";
        exit(1);
    }

    if (strpos($sql2, 'INNER JOIN chat_mensajes') === false) {
        echo "❌ Error: contarNoLeidos missing INNER JOIN.\n";
        exit(1);
    }

    echo "✅ All ChatMensaje optimizations verified!\n";
}
