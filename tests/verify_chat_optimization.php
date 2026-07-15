<?php

namespace App\Core {
    class Database {
        public static $instance;
        public $lastQuery = '';
        public $lastParams = [];

        public static function getInstance() {
            if (!self::$instance) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        public function fetchAll($sql, $params = []) {
            $this->lastQuery = $sql;
            $this->lastParams = $params;
            return [];
        }

        public function fetchOne($sql, $params = []) {
            $this->lastQuery = $sql;
            $this->lastParams = $params;
            return [];
        }

        public function execute($sql, $params = []) {
            $this->lastQuery = $sql;
            $this->lastParams = $params;
            return true;
        }
    }
}

namespace {
    // We don't use require_once __DIR__ . '/../vendor/autoload.php' because it would load the real Database class
    // Instead we load our mock first, then the model.
    require_once __DIR__ . '/../app/Models/ChatMensaje.php';

    use App\Models\ChatMensaje;
    use App\Core\Database;

    $db = Database::getInstance();
    $model = new ChatMensaje();

    function analyze_sql($method, $sql) {
        echo "Analyzing SQL for $method...\n";
        // echo "SQL: $sql\n";

        $errors = 0;

        // 1. Check for correlated subqueries in SELECT list
        // Strategy: extract everything before the FIRST 'FROM' that is not inside parentheses
        // Simplified: check for (SELECT ...) before the first FROM
        $fromPos = stripos($sql, 'FROM');
        $selectList = substr($sql, 0, $fromPos);

        if (preg_match('/\(\s*SELECT/i', $selectList)) {
            echo "❌ Error in $method: Correlated/Scalar subquery detected in SELECT list.\n";
            $errors++;
        }

        // 2. Check for correlated subqueries in JOIN/ON
        if (preg_match('/JOIN\s+.*?\s+ON\s+.*?\=\s*\(\s*SELECT/i', $sql)) {
             echo "❌ Error in $method: Correlated subquery detected in JOIN ON clause.\n";
             $errors++;
        }

        if ($errors === 0) {
            echo "✅ $method looks optimized.\n";
            return true;
        }
        return false;
    }

    $all_ok = true;

    // Test getConversaciones
    $model->getConversaciones(1);
    if (!analyze_sql('getConversaciones', $db->lastQuery)) $all_ok = false;

    // Test contarNoLeidos
    $model->contarNoLeidos(1);
    if (!analyze_sql('contarNoLeidos', $db->lastQuery)) $all_ok = false;

    if ($all_ok) {
        echo "\n🚀 All chat optimizations verified successfully!\n";
        exit(0);
    } else {
        echo "\n❌ Chat optimization verification failed.\n";
        exit(1);
    }
}
