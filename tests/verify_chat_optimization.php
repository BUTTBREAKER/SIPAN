<?php

declare(strict_types=1);

// Standard mock Database singleton setup for testing App\Models\ChatMensaje without full DB instance
namespace App\Core {
    class Database {
        private static ?Database $instance = null;
        public array $lastQuery = [];

        public static function getInstance(): self {
            if (self::$instance === null) {
                $ref = new \ReflectionClass(self::class);
                self::$instance = $ref->newInstanceWithoutConstructor();
            }
            return self::$instance;
        }

        public function fetchAll(string $sql, array $params = []): array {
            $this->lastQuery = ['sql' => $sql, 'params' => $params];
            return [];
        }

        public function fetchOne(string $sql, array $params = []): array|false {
            $this->lastQuery = ['sql' => $sql, 'params' => $params];
            return ['total' => 5];
        }
    }
}

namespace {
    require_once __DIR__ . '/../vendor/autoload.php';

    use App\Models\ChatMensaje;
    use App\Core\Database;

    echo "=== Verifying ChatMensaje SQL Optimization ===\n\n";

    $chatModel = new ChatMensaje();
    $db = Database::getInstance();

    // 1. Verify getConversaciones
    $chatModel->getConversaciones(42);
    $queryConv = $db->lastQuery;

    echo "1. Checking getConversaciones SQL...\n";
    $sqlConv = $queryConv['sql'];
    $paramsConv = $queryConv['params'];

    // Ensure no scalar subqueries in SELECT list (e.g. (SELECT COUNT(*) FROM...)
    $selectPart = explode('FROM', $sqlConv)[0];
    if (str_contains($selectPart, '(SELECT')) {
        echo "❌ FAIL: Scalar subquery found in SELECT list of getConversaciones!\n";
        exit(1);
    } else {
        echo "✅ PASS: No scalar subquery found in SELECT list of getConversaciones.\n";
    }

    // Verify derived tables are joined
    if (str_contains($sqlConv, 'LEFT JOIN (') && str_contains($sqlConv, 'm_last') && str_contains($sqlConv, 'unread')) {
        echo "✅ PASS: Derived tables (m_last, unread) successfully joined in getConversaciones.\n";
    } else {
        echo "❌ FAIL: Derived tables missing in getConversaciones SQL!\n";
        exit(1);
    }

    if (count($paramsConv) === 5 && array_diff($paramsConv, [42]) === []) {
        echo "✅ PASS: Parameter bindings for getConversaciones are correct (5 bindings for userId).\n";
    } else {
        echo "❌ FAIL: Incorrect parameter bindings for getConversaciones! Got: " . print_r($paramsConv, true) . "\n";
        exit(1);
    }

    echo "\n";

    // 2. Verify contarNoLeidos
    $chatModel->contarNoLeidos(42);
    $queryCount = $db->lastQuery;

    echo "2. Checking contarNoLeidos SQL...\n";
    $sqlCount = $queryCount['sql'];
    $paramsCount = $queryCount['params'];

    if (str_contains($sqlCount, 'FROM chat_participantes cp') && str_contains($sqlCount, 'INNER JOIN chat_mensajes cm')) {
        echo "✅ PASS: contarNoLeidos converted from subquery to direct INNER JOIN.\n";
    } else {
        echo "❌ FAIL: contarNoLeidos SQL does not use INNER JOIN!\n";
        exit(1);
    }

    if (count($paramsCount) === 2 && $paramsCount[0] === 42 && $paramsCount[1] === 42) {
        echo "✅ PASS: Parameter bindings for contarNoLeidos are correct (2 bindings for userId).\n";
    } else {
        echo "❌ FAIL: Incorrect parameter bindings for contarNoLeidos!\n";
        exit(1);
    }

    echo "\n🎉 ALL CHAT OPTIMIZATION CHECKS PASSED!\n";
}
