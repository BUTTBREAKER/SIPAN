<?php

namespace TestRunner;

require_once __DIR__ . '/../vendor/autoload.php';

use App\Models\ChatMensaje;

class MockDatabase {
    public array $queries = [];

    public function fetchAll(string $sql, array $params = []): array {
        $this->queries[] = ['sql' => $sql, 'params' => $params];
        return [];
    }

    public function fetchOne(string $sql, array $params = []): array {
        $this->queries[] = ['sql' => $sql, 'params' => $params];
        return ['total' => 0];
    }
}

class TestableChatMensaje extends ChatMensaje {
    public function __construct($db) {
        // Explicitly override parent constructor DB property
        $ref = new \ReflectionClass(ChatMensaje::class);
        $prop = $ref->getProperty('db');
        $prop->setAccessible(true);
        $prop->setValue($this, $db);
    }
}

function runTest(): void {
    echo "=== Testing ChatMensaje Query Optimizations ===\n";

    $mockDb = new MockDatabase();
    $chatModel = new TestableChatMensaje($mockDb);
    $userId = 42;

    // Test 1: getConversaciones
    $mockDb->queries = [];
    $chatModel->getConversaciones($userId);

    if (count($mockDb->queries) === 0) {
        echo "❌ Test 1 Failed: No query executed for getConversaciones.\n";
        exit(1);
    }

    $query1 = $mockDb->queries[0]['sql'];
    $params1 = $mockDb->queries[0]['params'];

    echo "\n1. Generated SQL for getConversaciones:\n$query1\n";
    echo "   Bound parameters count: " . count($params1) . "\n";

    // Check that correlated subqueries in SELECT list are removed
    $selectPart = explode('FROM', $query1)[0];
    if (preg_match('/SELECT\s+.*?\(SELECT/i', $selectPart)) {
        echo "❌ Test 1 Failed: Correlated subquery found in SELECT clause!\n";
        exit(1);
    }

    if (count($params1) !== 5) {
        echo "❌ Test 1 Failed: Expected 5 bound parameters for getConversaciones, got " . count($params1) . "\n";
        exit(1);
    }

    echo "✅ Test 1 Passed: getConversaciones refactored to eliminate SELECT list correlated subquery.\n";

    // Test 2: contarNoLeidos
    $mockDb->queries = [];
    $chatModel->contarNoLeidos($userId);

    if (count($mockDb->queries) === 0) {
        echo "❌ Test 2 Failed: No query executed for contarNoLeidos.\n";
        exit(1);
    }

    $query2 = $mockDb->queries[0]['sql'];
    $params2 = $mockDb->queries[0]['params'];

    echo "\n2. Generated SQL for contarNoLeidos:\n$query2\n";
    echo "   Bound parameters count: " . count($params2) . "\n";

    if (preg_match('/\(SELECT/i', $query2)) {
        echo "❌ Test 2 Failed: Subquery found in contarNoLeidos!\n";
        exit(1);
    }

    if (count($params2) !== 2) {
        echo "❌ Test 2 Failed: Expected 2 bound parameters for contarNoLeidos, got " . count($params2) . "\n";
        exit(1);
    }

    echo "✅ Test 2 Passed: contarNoLeidos refactored to single direct JOIN query.\n";

    echo "\n✨ All ChatMensaje optimizations verified successfully!\n";
}

try {
    runTest();
} catch (\Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}
