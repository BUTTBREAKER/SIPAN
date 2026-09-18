<?php

declare(strict_types=1);

namespace App\Core {
    if (!class_exists('App\Core\Database')) {
        class Database {
            private static ?Database $instance = null;
            public array $queries = [];

            public static function getInstance(): self {
                if (self::$instance === null) {
                    self::$instance = new self();
                }
                return self::$instance;
            }

            public function fetchAll(string $sql, array $params = []): array {
                $this->queries[] = ['sql' => $sql, 'params' => $params];
                return [];
            }

            public function fetchOne(string $sql, array $params = []): array {
                $this->queries[] = ['sql' => $sql, 'params' => $params];
                return [];
            }

            public function execute(string $sql, array $params = []): bool {
                $this->queries[] = ['sql' => $sql, 'params' => $params];
                return true;
            }
        }
    }
}

namespace {
    require_once __DIR__ . '/../app/Models/BaseModel.php';
    require_once __DIR__ . '/../app/Models/ChatMensaje.php';

    use App\Models\ChatMensaje;
    use App\Core\Database;

    $db = Database::getInstance();
    $model = new ChatMensaje();

    // 1. Test getConversaciones
    $model->getConversaciones(5);
    $lastQuery = end($db->queries);
    $sql = $lastQuery['sql'];
    $params = $lastQuery['params'];

    echo "=== Testing getConversaciones SQL ===\n";
    echo "Params count: " . count($params) . " (Expected: 5)\n";
    assert(count($params) === 5, "Expected 5 bound parameters");
    assert($params === [5, 5, 5, 5, 5], "Parameters should all be userId 5");

    // Check no correlated subqueries in SELECT list
    $selectPart = explode('FROM', $sql)[0];
    if (str_contains($selectPart, '(SELECT')) {
        echo "FAIL: Correlated scalar subquery found in SELECT list!\n";
        exit(1);
    } else {
        echo "PASS: No scalar subqueries in SELECT list.\n";
    }

    // Check no scalar subqueries in ON clause
    if (preg_match('/ON\s+.*?=\s*\(\s*SELECT/i', $sql)) {
        echo "FAIL: Scalar subquery found in JOIN condition!\n";
        exit(1);
    } else {
        echo "PASS: No scalar subqueries in JOIN conditions.\n";
    }

    // 2. Test contarNoLeidos
    $db->queries = [];
    $model->contarNoLeidos(5);
    $lastQuery = end($db->queries);
    $sql2 = $lastQuery['sql'];
    $params2 = $lastQuery['params'];

    echo "\n=== Testing contarNoLeidos SQL ===\n";
    echo "Params count: " . count($params2) . " (Expected: 2)\n";
    assert(count($params2) === 2, "Expected 2 bound parameters");
    assert($params2 === [5, 5], "Parameters should all be userId 5");

    if (str_contains($sql2, 'SELECT (')) {
        echo "FAIL: Correlated subquery found in contarNoLeidos!\n";
        exit(1);
    } else {
        echo "PASS: contarNoLeidos uses direct JOIN.\n";
    }

    echo "\nAll ChatMensaje SQL optimizations verified successfully!\n";
}
