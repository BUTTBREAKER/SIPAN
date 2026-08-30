<?php

namespace App\Core;

class Database
{
    private static $instance = null;
    public $queries = [];
    public $responses = [];

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function fetchAll($sql, $params = [])
    {
        $this->queries[] = ['sql' => $sql, 'params' => $params];
        return array_shift($this->responses) ?: [];
    }

    public function fetchOne($sql, $params = [])
    {
        $this->queries[] = ['sql' => $sql, 'params' => $params];
        return array_shift($this->responses) ?: null;
    }

    public function execute($sql, $params = [])
    {
        $this->queries[] = ['sql' => $sql, 'params' => $params];
        return true;
    }

    public function lastInsertId()
    {
        return 1;
    }
}

namespace Tests;

require_once __DIR__ . '/../app/Models/ChatMensaje.php';

use App\Models\ChatMensaje;
use App\Core\Database;

$db = Database::getInstance();
$model = new ChatMensaje();

echo "Running ChatMensaje Optimization Verification...\n";

// Test 1: getConversaciones should not have scalar subqueries in SELECT and should have filtered derived tables
echo "Testing getConversaciones optimization...\n";
$db->responses = [[]]; // Mock empty response
$model->getConversaciones(1);
$query = end($db->queries)['sql'];

// Split query to isolate the SELECT list
$parts = preg_split('/\s+FROM\s+/is', $query, 2);
$selectList = $parts[0];

$errors = [];
if (preg_match('/\(\s*SELECT/is', $selectList)) {
    $errors[] = "❌ FAIL: getConversaciones still contains a scalar subquery in the SELECT list.";
}

// Verify that derived tables have user filters (WHERE cp...id_usuario = ?)
// Looking for the pattern inside the subqueries
if (!preg_match_all('/SELECT.*?FROM.*?chat_participantes.*?WHERE.*?id_usuario\s*=\s*\?/is', $query, $matches)) {
    $errors[] = "❌ FAIL: Derived tables in getConversaciones lack user-level filters (chat_participantes + WHERE id_usuario).";
} elseif (count($matches[0]) < 2) {
    // Should have at least 2: one for unread count, one for last message
    $errors[] = "❌ FAIL: Not all derived tables in getConversaciones have user-level filters.";
}

if (empty($errors)) {
    echo "✅ PASS: getConversaciones optimized and filtered correctly.\n";
} else {
    foreach ($errors as $error) echo $error . "\n";
}

// Test 2: contarNoLeidos should not have nested subqueries
echo "Testing contarNoLeidos optimization...\n";
$db->responses = [['total' => 5]];
$model->contarNoLeidos(1);
$query = end($db->queries)['sql'];

$hasNestedSubquery = preg_match('/FROM\s*\(\s*SELECT/is', $query);

if ($hasNestedSubquery) {
    echo "❌ FAIL: contarNoLeidos still contains a nested subquery (FROM SELECT).\n";
} else {
    echo "✅ PASS: contarNoLeidos optimized successfully.\n";
}

echo "Verification script completed.\n";
