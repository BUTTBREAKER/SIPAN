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
        return true;
    }
}

namespace Tests;

require_once __DIR__ . '/../app/Models/ChatMensaje.php';

use App\Models\ChatMensaje;
use App\Core\Database;

function verify_no_scalar_subqueries($sql, $methodName) {
    echo "Verifying SQL for $methodName...\n";

    // Split by FROM to isolate the SELECT list
    $parts = preg_split('/\sFROM\s/i', $sql, 2);
    $selectList = $parts[0];

    // Check for scalar subqueries in SELECT list: (SELECT ... )
    if (preg_match('/\(\s*SELECT\s/i', $selectList)) {
        throw new \Exception("FAILED: $methodName contains a scalar subquery in the SELECT list.\nSQL: $sql");
    }

    // Check for correlated subqueries in JOINs or WHERE that are not derived tables
    // Derived tables are like JOIN (SELECT ...) AS alias
    // We want to avoid JOIN table ON col = (SELECT ...)
    if (preg_match('/\=\s*\(\s*SELECT\s/i', $sql)) {
         throw new \Exception("FAILED: $methodName contains a correlated subquery in a comparison.\nSQL: $sql");
    }

    echo "OK: $methodName SQL looks optimized (no obvious scalar subqueries).\n";
}

function runTest() {
    $chatModel = new ChatMensaje();
    $db = Database::getInstance();

    echo "--- Testing ChatMensaje Optimizations ---\n";

    // Test getConversaciones
    $chatModel->getConversaciones(1);
    try {
        verify_no_scalar_subqueries($db->lastSql, 'getConversaciones');
    } catch (\Exception $e) {
        echo $e->getMessage() . "\n";
    }

    // Test contarNoLeidos
    $chatModel->contarNoLeidos(1);
    try {
        verify_no_scalar_subqueries($db->lastSql, 'contarNoLeidos');
    } catch (\Exception $e) {
        echo $e->getMessage() . "\n";
    }

    echo "--- Test Complete ---\n";
}

runTest();
