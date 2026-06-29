<?php

namespace App\Core;

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
        return ['total' => 0];
    }

    public function execute($sql, $params = []) {
        $this->queries[] = ['sql' => $sql, 'params' => $params];
        return 1;
    }
}

namespace App\Models;

require_once __DIR__ . '/../app/Models/ChatMensaje.php';

use App\Core\Database;

$chat = new ChatMensaje();
$db = Database::getInstance();

echo "Running ChatMensaje Optimization Verification...\n";

// Test getConversaciones
$userId = 123;
$chat->getConversaciones($userId);

$lastQuery = end($db->queries);
$sql = $lastQuery['sql'];
$params = $lastQuery['params'];

echo "Checking getConversaciones...\n";
if (strpos($sql, '(SELECT COUNT(*)') !== false && strpos($sql, 'cm2.id_conversacion = c.id') !== false) {
    echo "❌ FAIL: getConversaciones still contains a correlated subquery for unread counts.\n";
    exit(1);
}

if (strpos($sql, 'm.id = (SELECT') !== false) {
    echo "❌ FAIL: getConversaciones still contains a correlated subquery for the last message.\n";
    exit(1);
}

if (strpos($sql, 'LEFT JOIN (') === false || strpos($sql, 'GROUP BY m2.id_conversacion') === false) {
    echo "❌ FAIL: getConversaciones is missing the optimized JOIN with a derived table for the last message.\n";
    exit(1);
}

if (strpos($sql, 'WHERE cp_m.id_usuario = ?') === false) {
    echo "❌ FAIL: getConversaciones is missing the user-specific filter in the last message derived table.\n";
    exit(1);
}

if (count($params) !== 5) {
    echo "❌ FAIL: getConversaciones expected 5 parameters, got " . count($params) . ".\n";
    var_dump($params);
    exit(1);
}
echo "✅ getConversaciones optimization verified.\n";

// Test contarNoLeidos
$db->queries = [];
$chat->contarNoLeidos($userId);

$lastQuery = end($db->queries);
$sql = $lastQuery['sql'];
$params = $lastQuery['params'];

echo "Checking contarNoLeidos...\n";
if (strpos($sql, 'SELECT (SELECT COUNT(*)') !== false) {
    echo "❌ FAIL: contarNoLeidos still contains a nested subquery pattern.\n";
    exit(1);
}

if (strpos($sql, 'JOIN chat_participantes') === false) {
    echo "❌ FAIL: contarNoLeidos is missing the optimized JOIN.\n";
    exit(1);
}

if (count($params) !== 2) {
    echo "❌ FAIL: contarNoLeidos expected 2 parameters, got " . count($params) . ".\n";
    exit(1);
}
echo "✅ contarNoLeidos optimization verified.\n";

echo "All ChatMensaje optimizations verified successfully!\n";
