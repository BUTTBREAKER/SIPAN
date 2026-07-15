<?php

namespace App\Core;

// Mock Database singleton
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
        return true;
    }
}

namespace Tests;

require_once __DIR__ . '/../app/Models/ChatMensaje.php';

use App\Models\ChatMensaje;
use App\Core\Database;

$db = Database::getInstance();
$model = new ChatMensaje();

echo "--- Testing ChatMensaje Performance (Mocked) ---\n";

// 1. Test getConversaciones
echo "Testing getConversaciones...\n";
$model->getConversaciones(1);
$sql = $db->lastQuery;

$hasCorrelatedUnread = preg_match('/SELECT\s+.*?\(\s*SELECT\s+COUNT/is', $sql);
$hasCorrelatedLastMsg = preg_match('/LEFT\s+JOIN\s+chat_mensajes\s+.*?\s+ON\s+.*?\s+=\s+\(\s*SELECT/is', $sql);

if ($hasCorrelatedUnread) {
    echo "❌ Baseline: Found correlated subquery for unread count in getConversaciones.\n";
} else {
    echo "✅ No correlated subquery for unread count in getConversaciones.\n";
}

if ($hasCorrelatedLastMsg) {
    echo "❌ Baseline: Found correlated subquery for last message in getConversaciones.\n";
} else {
    echo "✅ No correlated subquery for last message in getConversaciones.\n";
}

// 2. Test contarNoLeidos
echo "\nTesting contarNoLeidos...\n";
$model->contarNoLeidos(1);
$sql = $db->lastQuery;
echo "SQL: $sql\n";

$hasCorrelatedContar = preg_match('/SELECT\s+\(\s*SELECT\s+COUNT/is', $sql) || preg_match('/FROM\s+\(\s*SELECT\s+\(\s*SELECT\s+COUNT/is', $sql);

if ($hasCorrelatedContar) {
    echo "❌ Baseline: Found correlated subquery in contarNoLeidos.\n";
} else {
    echo "✅ No correlated subquery in contarNoLeidos.\n";
}

// Exit with 1 if any issues found (for baseline, we expect them to be found if not optimized)
// But I want the script to be used for verification later, so I'll make it smarter.

if (isset($argv[1]) && $argv[1] === '--verify') {
    if ($hasCorrelatedUnread || $hasCorrelatedLastMsg || $hasCorrelatedContar) {
        echo "\nStatus: FAILED (Optimizations missing)\n";
        exit(1);
    } else {
        echo "\nStatus: PASSED (Optimized!)\n";
        exit(0);
    }
}
