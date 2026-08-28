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
            return [];
        }

        public function execute($sql, $params = []) {
            $this->queries[] = ['sql' => $sql, 'params' => $params];
            return 1;
        }

        public function lastInsertId() { return 123; }
    }
}

namespace {
    require_once __DIR__ . '/../app/Models/ChatMensaje.php';

    use App\Models\ChatMensaje;
    use App\Core\Database;

    function verifyChatOptimization() {
        $db = Database::getInstance();
        $model = new ChatMensaje();
        $userId = 1;

        echo "--- Verifying ChatMensaje::getConversaciones Optimization ---\n";
        $db->queries = [];
        $model->getConversaciones($userId);

        $sql = $db->queries[0]['sql'];
        $params = $db->queries[0]['params'];

        // Check for correlated subqueries in SELECT (Scalar subqueries)
        // Scalar subquery pattern: , (SELECT ... ) as
        $hasCorrelated = preg_match('/,\s*\(\s*SELECT/is', $sql);
        if ($hasCorrelated) {
            echo "❌ FAILED: Found scalar subquery in SELECT list. This usually indicates O(N*M) complexity.\n";
            echo "SQL: " . $sql . "\n";
        } else {
            echo "✅ PASSED: No scalar subqueries found in SELECT list.\n";
        }

        // Check for JOIN with subquery (Derived Table)
        $hasDerivedTable = preg_match('/JOIN\s*\(\s*SELECT/is', $sql);
        if ($hasDerivedTable) {
            echo "✅ PASSED: Found derived table join. This is efficient O(N+M).\n";
        } else {
            echo "❌ FAILED: No derived table join found. Expected optimized join pattern.\n";
            echo "SQL: " . $sql . "\n";
        }

        // Check for context-aware filter (user_id inside subquery)
        $internalFiltersCount = preg_match_all('/WHERE\s+cp_filter\.id_usuario\s*=\s*\?/is', $sql, $matches);
        if ($internalFiltersCount === 2) {
            echo "✅ PASSED: Found context-aware filters inside BOTH derived tables (latest_m and nl).\n";
        } else {
            echo "❌ FAILED: Expected 2 context-aware filters inside derived tables, found " . $internalFiltersCount . ".\n";
            echo "SQL: " . $sql . "\n";
        }

        // Check for sucursales join
        $hasSucursalesJoin = preg_match('/LEFT\s+JOIN\s+sucursales\s+s/is', $sql);
        if ($hasSucursalesJoin) {
            echo "✅ PASSED: sucursales join is present.\n";
        } else {
            echo "❌ FAILED: sucursales join is missing. This will cause an 'Unknown column' error.\n";
        }

        // Check param count (should be 5)
        if (count($params) === 5) {
            echo "✅ PASSED: Parameter count is correct (5).\n";
        } else {
            echo "❌ FAILED: Parameter count incorrect. Got " . count($params) . ", expected 5.\n";
        }

        echo "\n--- Verifying ChatMensaje::contarNoLeidos Optimization ---\n";
        $db->queries = [];
        $model->contarNoLeidos($userId);
        $sqlCount = $db->queries[0]['sql'];

        // Should use JOIN and avoid SELECT COUNT(*) FROM (SELECT COUNT(*) ...) or nested scalar queries
        $isFlatJoin = strpos(strtoupper($sqlCount), 'JOIN') !== false && !preg_match('/SELECT\s+.*\(\s*SELECT/is', $sqlCount);

        if ($isFlatJoin) {
            echo "✅ PASSED: contarNoLeidos uses a flat JOIN instead of nested subqueries.\n";
        } else {
            echo "❌ FAILED: contarNoLeidos does not appear to use a simple JOIN or still has scalar subqueries.\n";
            echo "SQL: " . $sqlCount . "\n";
        }
    }

    try {
        verifyChatOptimization();
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
}
