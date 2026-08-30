<?php

namespace App\Core {
    class Database {
        private static $instance = null;
        public $queries = [];
        public $lastParams = [];

        public static function getInstance() {
            if (self::$instance === null) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        public function fetchAll($sql, $params = []) {
            $this->queries[] = $sql;
            $this->lastParams = $params;

            // Mock response for getConversaciones
            if (strpos($sql, 'SELECT') !== false && strpos($sql, 'chat_conversaciones') !== false) {
                return [
                    [
                        'id' => 1,
                        'tipo' => 'directa',
                        'ultimo_mensaje' => 'Hola',
                        'no_leidos' => 2
                    ]
                ];
            }
            return [];
        }

        public function fetchOne($sql, $params = []) {
            $this->queries[] = $sql;
            $this->lastParams = $params;

            // Mock response for contarNoLeidos
            if (strpos($sql, 'COUNT(*)') !== false && strpos($sql, 'chat_mensajes') !== false) {
                return ['total' => 5];
            }
            return null;
        }
    }
}

namespace {
    require_once 'app/Models/ChatMensaje.php';
    use App\Models\ChatMensaje;
    use App\Core\Database;

    $chat = new ChatMensaje();
    $db = Database::getInstance();

    echo "--- Testing ChatMensaje Optimizations ---\n";

    // Test contarNoLeidos
    echo "Testing contarNoLeidos...\n";
    $count = $chat->contarNoLeidos(1);
    $lastQuery = end($db->queries);

    if (strpos($lastQuery, 'INNER JOIN chat_participantes') !== false) {
        echo "✅ contarNoLeidos uses JOIN instead of correlated subquery.\n";
    } else {
        echo "❌ contarNoLeidos is NOT using JOIN.\n";
        exit(1);
    }

    if ($count === 5) {
        echo "✅ contarNoLeidos returns correct mocked value.\n";
    }

    // Test getConversaciones
    echo "\nTesting getConversaciones...\n";
    $convs = $chat->getConversaciones(1);
    $lastQuery = end($db->queries);

    if (strpos($lastQuery, 'LEFT JOIN (') !== false && strpos($lastQuery, 'GROUP BY') !== false) {
        echo "✅ getConversaciones uses derived tables and GROUP BY.\n";
    } else {
        echo "❌ getConversaciones is NOT using derived tables.\n";
        exit(1);
    }

    if (count($db->lastParams) === 5) {
        echo "✅ getConversaciones has correct number of parameters (5).\n";
    } else {
        echo "❌ getConversaciones has " . count($db->lastParams) . " parameters, expected 5.\n";
        exit(1);
    }

    if (!empty($convs)) {
        echo "✅ getConversaciones returns results.\n";
    }

    echo "\n🚀 All ChatMensaje optimizations verified successfully!\n";
}
