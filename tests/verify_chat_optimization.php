<?php

namespace App\Core {
    class Database {
        private static $instance = null;
        public $queries = [];
        public static function getInstance() {
            if (self::$instance === null) self::$instance = new self();
            return self::$instance;
        }
        public function fetchAll($sql, $params = []) {
            $this->queries[] = ['sql' => $sql, 'params' => $params];
            return [];
        }
        public function fetchOne($sql, $params = []) {
            $this->queries[] = ['sql' => $sql, 'params' => $params];
            return [];
        }
        public function execute($sql, $params = []) {
            $this->queries[] = ['sql' => $sql, 'params' => $params];
            return 1;
        }
        public function lastInsertId() { return 1; }
    }
}

namespace {
    require_once __DIR__ . '/../vendor/autoload.php';
    require_once __DIR__ . '/../app/Models/ChatMensaje.php';

    use App\Models\ChatMensaje;
    use App\Core\Database;

    $model = new ChatMensaje();
    $db = Database::getInstance();

    echo "--- Verifying ChatMensaje Optimizations ---\n";

    // 1. Verify getConversaciones
    $model->getConversaciones(1);
    $lastQuery = end($db->queries)['sql'];

    echo "Checking getConversaciones SQL...\n";
    if (stripos($lastQuery, '(SELECT COUNT(*)') !== false || stripos($lastQuery, '(SELECT m2.id') !== false) {
        echo "❌ FAILURE: Correlated subquery detected in getConversaciones SELECT list.\n";
        echo "Query: $lastQuery\n";
        exit(1);
    }

    if (stripos($lastQuery, 'LEFT JOIN (') === false) {
        echo "❌ FAILURE: Derived table JOIN not found in getConversaciones.\n";
        exit(1);
    }
    echo "✅ getConversaciones uses efficient JOINs.\n";

    // 2. Verify contarNoLeidos
    $model->contarNoLeidos(1);
    $lastQuery = end($db->queries)['sql'];

    echo "Checking contarNoLeidos SQL...\n";
    if (stripos($lastQuery, '(SELECT') !== false && stripos($lastQuery, 'FROM (') === false) {
        echo "❌ FAILURE: Scalar subquery or nested subquery pattern detected in contarNoLeidos.\n";
        echo "Query: $lastQuery\n";
        exit(1);
    }

    if (stripos($lastQuery, 'INNER JOIN chat_mensajes') === false) {
        echo "❌ FAILURE: INNER JOIN not found in contarNoLeidos.\n";
        exit(1);
    }
    echo "✅ contarNoLeidos uses efficient JOIN.\n";

    echo "--- All ChatMensaje optimizations verified successfully! ---\n";
}
