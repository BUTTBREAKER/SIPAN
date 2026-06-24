<?php

namespace App\Core {
    class Database {
        private static $instance = null;
        public $lastQueries = [];
        public $lastParams = [];

        public static function getInstance() {
            if (self::$instance === null) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        public function fetchAll($sql, $params = []) {
            $this->lastQueries[] = $sql;
            $this->lastParams[] = $params;
            return [];
        }

        public function fetchOne($sql, $params = []) {
            $this->lastQueries[] = $sql;
            $this->lastParams[] = $params;
            return [];
        }

        public function execute($sql, $params = []) {
            $this->lastQueries[] = $sql;
            $this->lastParams[] = $params;
            return 1;
        }
    }
}

namespace {
    require_once __DIR__ . '/../app/Models/ChatMensaje.php';

    use App\Models\ChatMensaje;
    use App\Core\Database;

    $db = Database::getInstance();
    $chatModel = new ChatMensaje();

    function checkCorrelatedSubqueries($sql, $methodName) {
        echo "DEBUG: Checking SQL for $methodName:\n$sql\n";
        // Scalar subquery pattern: (SELECT ... ) in the SELECT list or as a value
        // We look for patterns like ", (SELECT" or "SELECT (SELECT" or "= (SELECT"
        $patterns = [
            'scalar_select' => '/,\s*\(\s*SELECT/is',
            'nested_select' => '/SELECT\s*\(\s*SELECT/is',
            'join_subquery' => '/JOIN\s+.*\s+ON\s+.*=\s*\(\s*SELECT/is'
        ];

        foreach ($patterns as $type => $pattern) {
            if (preg_match($pattern, $sql)) {
                echo "❌ Error in $methodName: $type detected. This suggests O(N*M) complexity.\n";
                return false;
            }
        }

        echo "✅ $methodName SQL looks clean.\n";
        return true;
    }

    echo "--- Checking ChatMensaje Optimizations ---\n\n";

    $failed = false;

    // Test getConversaciones
    $db->lastQueries = [];
    $chatModel->getConversaciones(1);
    foreach ($db->lastQueries as $sql) {
        if (!checkCorrelatedSubqueries($sql, 'getConversaciones')) {
            $failed = true;
        }
    }

    // Test contarNoLeidos
    $db->lastQueries = [];
    $chatModel->contarNoLeidos(1);
    foreach ($db->lastQueries as $sql) {
        if (!checkCorrelatedSubqueries($sql, 'contarNoLeidos')) {
            $failed = true;
        }
    }

    if ($failed) {
        echo "\n❌ Optimization check FAILED.\n";
        exit(1);
    } else {
        echo "\n🎉 Optimization check PASSED.\n";
        exit(0);
    }
}
