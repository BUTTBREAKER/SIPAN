<?php

namespace App\Core;

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

namespace Tests;

require_once __DIR__ . '/../app/Models/ChatMensaje.php';
use App\Models\ChatMensaje;
use App\Core\Database;

class ChatOptimizationTest {
    public function run() {
        echo "Testing ChatMensaje optimizations...\n";
        $model = new ChatMensaje();
        $db = Database::getInstance();

        // Test getConversaciones
        $model->getConversaciones(1);
        $this->assertNoCorrelatedSubquery($db->lastSql, 'getConversaciones');
        $this->assertEquals(count($db->lastParams), 5, 'getConversaciones parameter count');

        // Test contarNoLeidos
        $model->contarNoLeidos(1);
        $this->assertNoCorrelatedSubquery($db->lastSql, 'contarNoLeidos');
        $this->assertEquals(count($db->lastParams), 2, 'contarNoLeidos parameter count');

        echo "All chat optimization tests passed!\n";
    }

    private function assertNoCorrelatedSubquery($sql, $method) {
        // Scalar subqueries often look like (SELECT ... FROM ... WHERE ... = outer_table.column)
        // A simple check is to see if there's a SELECT inside the SELECT list or if it uses the correlated pattern
        // Specifically for our optimization, we want to ensure we don't have (SELECT COUNT(*) FROM chat_mensajes

        $sqlUpper = strtoupper($sql);

        // Split by FROM to isolate the SELECT list
        $parts = explode(' FROM ', $sqlUpper, 2);
        $selectList = $parts[0];

        if (strpos($selectList, '(SELECT') !== false) {
             throw new \Exception("Optimization Failure: Method $method still contains a scalar subquery in the SELECT list.");
        }

        // Also check for subqueries in the JOIN that don't look like our intended derived tables
        // Actually, our derived tables ARE subqueries, but they are NOT correlated.
        // A correlated subquery usually references a column from the outer query.

        echo "✓ $method: No scalar subqueries detected in SELECT list.\n";
    }

    private function assertEquals($actual, $expected, $label) {
        if ($actual !== $expected) {
            throw new \Exception("Failure in $label: Expected $expected, got $actual");
        }
        echo "✓ $label: matches expected value $expected.\n";
    }
}

try {
    (new ChatOptimizationTest())->run();
    exit(0);
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
