<?php

namespace App\Core {
    if (!class_exists('App\Core\Database')) {
        class Database
        {
            private static $instance = null;
            public $connection = null;
            public $executedQueries = [];

            public function __construct()
            {
                $this->connection = new \PDO('sqlite::memory:');
            }

            public static function getInstance()
            {
                if (self::$instance === null) {
                    self::$instance = new self();
                }
                return self::$instance;
            }

            public function beginTransaction()
            {
                $this->executedQueries[] = ['sql' => 'BEGIN TRANSACTION', 'params' => []];
                return true;
            }

            public function commit()
            {
                $this->executedQueries[] = ['sql' => 'COMMIT', 'params' => []];
                return true;
            }

            public function rollback()
            {
                $this->executedQueries[] = ['sql' => 'ROLLBACK', 'params' => []];
                return true;
            }

            public function execute($sql, $params = [])
            {
                $this->executedQueries[] = ['sql' => $sql, 'params' => $params];
                return true;
            }

            public function fetchAll($sql, $params = [])
            {
                $this->executedQueries[] = ['sql' => $sql, 'params' => $params];
                return [];
            }

            public function resetQueries()
            {
                $this->executedQueries = [];
            }
        }
    }
}

namespace {
    require_once __DIR__ . '/../app/Models/BaseModel.php';
    require_once __DIR__ . '/../app/Models/Proveedor.php';

    use App\Models\Proveedor;
    use App\Core\Database;

    $db = Database::getInstance();
    $proveedor = new Proveedor();

    echo "Testing Proveedor::addInsumos batching...\n";
    $db->resetQueries();

    $insumos = [
        ['id_insumo' => 101, 'precio' => 12.50, 'tiempo_entrega' => 3],
        ['id_insumo' => 102, 'precio' => 25.00, 'tiempo_entrega' => 5],
        ['id_insumo' => 103, 'precio' => 5.75, 'tiempo_entrega' => 1],
    ];

    $proveedor->addInsumos(10, $insumos);

    $queries = $db->executedQueries;
    assert(count($queries) === 4, "Expected 4 database operations (BEGIN, DELETE, batch INSERT, COMMIT). Got " . count($queries));

    $insertQuery = null;
    foreach ($queries as $q) {
        if (strpos($q['sql'], 'INSERT INTO proveedor_insumos') !== false) {
            $insertQuery = $q;
            break;
        }
    }

    assert($insertQuery !== null, "Expected single batch INSERT query for proveedor_insumos");
    assert(substr_count($insertQuery['sql'], '(?, ?, ?, ?)') === 3, "Expected 3 row placeholders in batch INSERT query");
    assert(count($insertQuery['params']) === 12, "Expected 12 bound parameters for 3 rows with 4 columns each");

    echo "✅ addInsumos successfully batches multi-row inserts into a single query!\n\n";

    echo "Testing Proveedor::getInsumosSinProveedor query structure...\n";
    $db->resetQueries();

    $proveedor->getInsumosSinProveedor(1);

    $queries = $db->executedQueries;
    assert(count($queries) === 1, "Expected 1 query for getInsumosSinProveedor");
    $sql = $queries[0]['sql'];

    assert(strpos($sql, 'NOT EXISTS') !== false, "Expected query to use SARGable NOT EXISTS subquery");
    assert(strpos($sql, 'GROUP BY') === false, "Query should not use GROUP BY");
    assert(strpos($sql, 'HAVING') === false, "Query should not use HAVING");

    echo "✅ getInsumosSinProveedor successfully uses NOT EXISTS without GROUP BY/HAVING!\n\n";

    echo "ALL PROVEEDOR OPTIMIZATION TESTS PASSED!\n";
}
