<?php

namespace App\Core;

// Mock Database class to capture SQL queries and parameters
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
        return ['total' => 0];
    }
}

namespace Tests;

require_once __DIR__ . '/../vendor/autoload.php';

use App\Models\ChatMensaje;
use App\Core\Database;

function testChatOptimization() {
    echo "=== Running ChatMensaje Optimization Verification ===\n\n";

    $chatModel = new ChatMensaje();
    $db = Database::getInstance();

    // 1. Test getConversaciones SQL
    echo "Test 1: getConversaciones SQL analysis...\n";
    $chatModel->getConversaciones(42);
    $sql1 = $db->lastSql;
    $params1 = $db->lastParams;

    echo "SQL: \n$sql1\n\n";
    echo "Params: " . json_encode($params1) . "\n\n";

    // Strip comments to avoid false positives
    $cleanSql1 = preg_replace('!--.*$!m', '', $sql1);

    // Isolate SELECT clause before FROM
    $parts = preg_split('/\bFROM\b/i', $cleanSql1, 2);
    $selectClause = $parts[0] ?? '';

    if (preg_match('/\(\s*SELECT/i', $selectClause)) {
        echo "❌ FAIL: getConversaciones contains correlated subquery in SELECT clause!\n";
        exit(1);
    }

    if (preg_match('/ON\s+m\.id\s*=\s*\(\s*SELECT/i', $cleanSql1)) {
        echo "❌ FAIL: getConversaciones contains correlated subquery in JOIN clause!\n";
        exit(1);
    }

    if (!stristr($cleanSql1, 'm_last') || !stristr($cleanSql1, 'unread')) {
        echo "❌ FAIL: getConversaciones missing expected derived tables (m_last, unread)!\n";
        exit(1);
    }

    if (count($params1) !== 4 || array_diff($params1, [42]) !== []) {
        echo "❌ FAIL: getConversaciones parameters incorrect! Expected 4 x [42], got: " . json_encode($params1) . "\n";
        exit(1);
    }

    echo "✅ PASS: getConversaciones uses set-based derived tables with 4 user parameters!\n\n";

    // 2. Test contarNoLeidos SQL
    echo "Test 2: contarNoLeidos SQL analysis...\n";
    $chatModel->contarNoLeidos(42);
    $sql2 = $db->lastSql;
    $params2 = $db->lastParams;

    echo "SQL: \n$sql2\n\n";
    echo "Params: " . json_encode($params2) . "\n\n";

    $cleanSql2 = preg_replace('!--.*$!m', '', $sql2);

    if (preg_match('/\(\s*SELECT/i', $cleanSql2)) {
        echo "❌ FAIL: contarNoLeidos contains nested subquery!\n";
        exit(1);
    }

    if (!stristr($cleanSql2, 'INNER JOIN chat_mensajes')) {
        echo "❌ FAIL: contarNoLeidos does not use INNER JOIN!\n";
        exit(1);
    }

    if (count($params2) !== 2 || array_diff($params2, [42]) !== []) {
        echo "❌ FAIL: contarNoLeidos parameters incorrect! Expected 2 x [42], got: " . json_encode($params2) . "\n";
        exit(1);
    }

    echo "✅ PASS: contarNoLeidos uses single INNER JOIN with 2 user parameters!\n\n";

    echo "✨ All ChatMensaje optimizations verified successfully!\n";
}

testChatOptimization();
