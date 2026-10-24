<?php

declare(strict_types=1);

// Mock Database class
namespace App\Core;

class Database
{
    private static $instance = null;
    public array $queries = [];

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function fetchAll(string $sql, array $params = []): array
    {
        $this->queries[] = ['sql' => $sql, 'params' => $params];
        return [];
    }

    public function fetchOne(string $sql, array $params = []): array|false
    {
        $this->queries[] = ['sql' => $sql, 'params' => $params];
        return ['total' => 5];
    }

    public function execute(string $sql, array $params = []): bool
    {
        $this->queries[] = ['sql' => $sql, 'params' => $params];
        return true;
    }
}

namespace App\Models;

require_once __DIR__ . '/../app/Models/BaseModel.php';
require_once __DIR__ . '/../app/Models/ChatMensaje.php';

use App\Models\ChatMensaje;
use App\Core\Database;

echo "=== Running ChatMensaje Optimization Verification ===\n\n";

$db = Database::getInstance();
$chatModel = new ChatMensaje();

// Test 1: getConversaciones SQL analysis
$chatModel->getConversaciones(10);
$lastQuery = end($db->queries);
$sql = $lastQuery['sql'];
$params = $lastQuery['params'];

echo "Test 1: ChatMensaje::getConversaciones\n";
echo "Parameter count matches expected (5): " . (count($params) === 5 && array_unique($params) === [10] ? "PASS" : "FAIL") . "\n";

// Ensure no correlated scalar subqueries remain in SELECT
$hasCorrelatedSubqueryInSelect = preg_match('/SELECT\s+.*?\(\s*SELECT\s+COUNT/is', $sql);
echo "No correlated subqueries in SELECT clause: " . (!$hasCorrelatedSubqueryInSelect ? "PASS" : "FAIL") . "\n";

// Ensure derived tables unread and m_last are present
$hasUnreadDerived = strpos($sql, 'unread.id_conversacion') !== false;
$hasLastMsgDerived = strpos($sql, 'm_last.id_conversacion') !== false;
echo "Uses derived table JOINs for unread and latest message: " . ($hasUnreadDerived && $hasLastMsgDerived ? "PASS" : "FAIL") . "\n\n";

// Test 2: contarNoLeidos SQL analysis
$db->queries = [];
$total = $chatModel->contarNoLeidos(10);
$lastQuery = end($db->queries);
$sql = $lastQuery['sql'];
$params = $lastQuery['params'];

echo "Test 2: ChatMensaje::contarNoLeidos\n";
echo "Returns integer value: " . ($total === 5 ? "PASS" : "FAIL") . "\n";
echo "Parameter count matches expected (2): " . (count($params) === 2 && $params === [10, 10] ? "PASS" : "FAIL") . "\n";

// Ensure no subqueries are used in contarNoLeidos
$hasSubqueryInContar = (strpos($sql, 'SELECT (') !== false || strpos($sql, 'FROM (') !== false);
echo "No subqueries in contarNoLeidos: " . (!$hasSubqueryInContar ? "PASS" : "FAIL") . "\n";
echo "Uses direct INNER JOIN: " . (strpos($sql, 'INNER JOIN chat_participantes') !== false ? "PASS" : "FAIL") . "\n\n";

echo "=== All ChatMensaje Optimization Tests Complete ===\n";
