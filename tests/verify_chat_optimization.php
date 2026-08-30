<?php

namespace App\Core {
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
            return true;
        }

        public function lastInsertId() {
            return 1;
        }
    }
}

namespace {
    require_once __DIR__ . '/../app/Models/ChatMensaje.php';

    use App\Models\ChatMensaje;
    use App\Core\Database;

    $chatModel = new ChatMensaje();
    $db = Database::getInstance();

    echo "Testing ChatMensaje optimizations...\n";

    // 1. Test getConversaciones
    echo "1. Testing getConversaciones SQL transformation...\n";
    $userId = 5;
    $chatModel->getConversaciones($userId);
    $lastQuery = end($db->queries);

    if (strpos($lastQuery['sql'], 'MAX(id)') !== false && strpos($lastQuery['sql'], 'GROUP BY id_conversacion') !== false) {
        echo "✅ getConversaciones uses derived table for latest message.\n";
    } else {
        echo "❌ getConversaciones optimization failed.\n";
        exit(1);
    }

    if (strpos($lastQuery['sql'], 'unr.id_conversacion') !== false) {
        echo "✅ getConversaciones uses derived table for unread counts.\n";
    } else {
        echo "❌ getConversaciones unread count optimization failed.\n";
        exit(1);
    }

    if (count($lastQuery['params']) === 4) {
         echo "✅ getConversaciones uses correct number of parameters (4).\n";
    } else {
         echo "❌ getConversaciones parameter count mismatch. Found " . count($lastQuery['params']) . " expected 4.\n";
         exit(1);
    }

    // 2. Test contarNoLeidos
    echo "2. Testing contarNoLeidos SQL transformation...\n";
    $chatModel->contarNoLeidos($userId);
    $lastQuery = end($db->queries);

    if (strpos($lastQuery['sql'], 'INNER JOIN chat_participantes') !== false && strpos($lastQuery['sql'], 'sub.no_leidos') === false) {
        echo "✅ contarNoLeidos uses INNER JOIN instead of subqueries.\n";
    } else {
        echo "❌ contarNoLeidos optimization failed.\n";
        exit(1);
    }

    echo "All ChatMensaje optimizations verified successfully!\n";
}
