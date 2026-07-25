<?php

namespace App\Core;

class Database
{
    private static $instance = null;
    public $lastSql = '';
    public $lastParams = [];

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function fetchAll($sql, $params = [])
    {
        $this->lastSql = $sql;
        $this->lastParams = $params;
        return [];
    }

    public function fetchOne($sql, $params = [])
    {
        $this->lastSql = $sql;
        $this->lastParams = $params;
        return ['total' => 5];
    }
}

namespace Tests;

require_once __DIR__ . '/../vendor/autoload.php';

use App\Models\ChatMensaje;
use App\Core\Database;

function runVerification()
{
    echo "--- Testing ChatMensaje SQL Optimization ---\n";

    $db = Database::getInstance();
    $chatModel = new ChatMensaje();

    // 1. Test getConversaciones($userId)
    $userId = 42;
    echo "Test 1: Verifying getConversaciones($userId) SQL optimization...\n";
    $chatModel->getConversaciones($userId);

    $sql = $db->lastSql;
    $params = $db->lastParams;

    echo "  Generated SQL: " . preg_replace('/\s+/', ' ', $sql) . "\n";
    echo "  Params: " . json_encode($params) . "\n";

    // Assert that there are no correlated subqueries in the SELECT list.
    // Specifically, check that SELECT list does not contain any nested SELECT count/max/etc.
    // e.g., SELECT ... (SELECT COUNT(*) FROM chat_mensajes cm2 ... ) AS no_leidos
    // The select list is everything before the first top-level FROM.
    $fromPos = stripos($sql, 'FROM');
    $selectList = substr($sql, 0, $fromPos);

    if (stripos($selectList, '(SELECT') !== false) {
        echo "❌ Test 1 Failed: Correlated subquery found in SELECT list!\n";
        exit(1);
    } else {
        echo "✅ No correlated subqueries found in the SELECT clause.\n";
    }

    // Also assert that the query contains JOINs to derived tables instead.
    if (stripos($sql, 'LEFT JOIN (') !== false && (stripos($sql, 'm_last') !== false || stripos($sql, 'unread') !== false)) {
        echo "✅ Found optimized JOINs to derived tables.\n";
    } else {
        echo "❌ Test 1 Failed: Optimized derived tables not found in query structure!\n";
        exit(1);
    }

    // Assert bindings size and matching user ID
    if (count($params) !== 5) {
        echo "❌ Test 1 Failed: Expected exactly 5 bound parameters, got " . count($params) . "\n";
        exit(1);
    }
    foreach ($params as $idx => $param) {
        if ($param !== $userId) {
            echo "❌ Test 1 Failed: Parameter at index $idx is $param, expected $userId\n";
            exit(1);
        }
    }
    echo "✅ Bound parameters are correct and completely safe.\n";

    // 2. Test contarNoLeidos($userId)
    echo "\nTest 2: Verifying contarNoLeidos($userId) SQL optimization...\n";
    $chatModel->contarNoLeidos($userId);

    $sqlNoLeidos = $db->lastSql;
    $paramsNoLeidos = $db->lastParams;

    echo "  Generated SQL: " . preg_replace('/\s+/', ' ', $sqlNoLeidos) . "\n";
    echo "  Params: " . json_encode($paramsNoLeidos) . "\n";

    // Assert it uses INNER JOIN and does not contain subqueries or sub-selects.
    if (stripos($sqlNoLeidos, 'sub') !== false || stripos($sqlNoLeidos, 'SELECT') !== stripos($sqlNoLeidos, 'SELECT', 1)) {
        // More than one SELECT keyword or reference to sub-queries indicates non-optimized nested loops
        // Let's count occurrence of SELECT
        $selectCount = substr_count(strtoupper($sqlNoLeidos), 'SELECT');
        if ($selectCount > 1) {
            echo "❌ Test 2 Failed: Subquery or nested SELECT found in contarNoLeidos!\n";
            exit(1);
        }
    }

    if (stripos($sqlNoLeidos, 'INNER JOIN') !== false) {
        echo "✅ INNER JOIN used for direct counting of unread messages.\n";
    } else {
        echo "❌ Test 2 Failed: Expected INNER JOIN instead of correlated subqueries.\n";
        exit(1);
    }

    if (count($paramsNoLeidos) !== 2) {
        echo "❌ Test 2 Failed: Expected exactly 2 bound parameters, got " . count($paramsNoLeidos) . "\n";
        exit(1);
    }
    foreach ($paramsNoLeidos as $idx => $param) {
        if ($param !== $userId) {
            echo "❌ Test 2 Failed: Parameter at index $idx is $param, expected $userId\n";
            exit(1);
        }
    }
    echo "✅ Bound parameters for contarNoLeidos are correct.\n";

    echo "\n✨ ChatMensaje SQL optimizations verified successfully!\n";
}

runVerification();
