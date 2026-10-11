<?php

declare(strict_types=1);

namespace App\Core {
    // Mock Database class to record executed queries and parameters
    class Database {
        private static $instance = null;
        public static $lastSql = '';
        public static $lastParams = [];

        public static function getInstance(): self {
            if (self::$instance === null) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        public function fetchAll(string $sql, array $params = []): array {
            self::$lastSql = $sql;
            self::$lastParams = $params;
            return [];
        }

        public function fetchOne(string $sql, array $params = []): array|false {
            self::$lastSql = $sql;
            self::$lastParams = $params;
            return ['total' => 5];
        }
    }
}

namespace {
    require_once __DIR__ . '/../app/Models/BaseModel.php';
    require_once __DIR__ . '/../app/Models/ChatMensaje.php';

    use App\Models\ChatMensaje;
    use App\Core\Database;

    echo "Running ChatMensaje Optimization Verification...\n";

    $chatModel = new ChatMensaje();

    // 1. Verify getConversaciones
    $conversaciones = $chatModel->getConversaciones(10);
    $sqlConversaciones = Database::$lastSql;
    $paramsConversaciones = Database::$lastParams;

    echo "Query generated for getConversaciones:\n" . trim($sqlConversaciones) . "\n\n";

    // Strip SQL comments before checking for correlated subqueries
    $cleanSql = preg_replace('/--.*$/m', '', $sqlConversaciones);

    // Isolate SELECT list before FROM
    $parts = preg_split('/\bFROM\b/i', $cleanSql, 2);
    $selectList = $parts[0] ?? '';

    if (preg_match('/\(\s*SELECT\s+COUNT/i', $selectList)) {
        echo "[FAIL] getConversaciones still contains correlated subquery in SELECT list!\n";
        exit(1);
    }

    if (count($paramsConversaciones) !== 5) {
        echo "[FAIL] Expected 5 bound parameters for getConversaciones, got " . count($paramsConversaciones) . "\n";
        exit(1);
    }

    echo "[PASS] getConversaciones uses derived tables and correct parameter bindings (5 parameters).\n";

    // 2. Verify contarNoLeidos
    $noLeidos = $chatModel->contarNoLeidos(10);
    $sqlNoLeidos = Database::$lastSql;
    $paramsNoLeidos = Database::$lastParams;

    echo "Query generated for contarNoLeidos:\n" . trim($sqlNoLeidos) . "\n\n";

    if (str_contains($sqlNoLeidos, 'FROM (') || str_contains($sqlNoLeidos, '(SELECT')) {
        echo "[FAIL] contarNoLeidos still contains subqueries!\n";
        exit(1);
    }

    if (count($paramsNoLeidos) !== 2) {
        echo "[FAIL] Expected 2 bound parameters for contarNoLeidos, got " . count($paramsNoLeidos) . "\n";
        exit(1);
    }

    echo "[PASS] contarNoLeidos uses single direct JOIN and 2 parameters.\n";
    echo "All ChatMensaje optimization checks PASSED successfully!\n";
}
