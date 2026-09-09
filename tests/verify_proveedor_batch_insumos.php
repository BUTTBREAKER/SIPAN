<?php

namespace App\Core;

class Database
{
    private static $instance = null;
    public $queries = [];

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function beginTransaction()
    {
        return true;
    }

    public function commit()
    {
        return true;
    }

    public function rollback()
    {
        return true;
    }

    public function fetchAll($sql, $params = [])
    {
        $this->queries[] = ['sql' => $sql, 'params' => $params];
        return [];
    }

    public function fetchOne($sql, $params = [])
    {
        $this->queries[] = ['sql' => $sql, 'params' => $params];
        return [];
    }

    public function execute($sql, $params = [])
    {
        $this->queries[] = ['sql' => $sql, 'params' => $params];
        return true;
    }
}

namespace App\Models;

use App\Core\Database;

class BaseModel
{
    protected string $table;
    protected $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }
}

require_once __DIR__ . '/../app/Models/Proveedor.php';

use App\Models\Proveedor;
use App\Core\Database as DB;

$proveedor = new Proveedor();
$db = DB::getInstance();

echo "Running Proveedor::addInsumos Optimization Verification...\n\n";

$insumos = [
    ['id_insumo' => 10, 'precio' => 15.50, 'tiempo_entrega' => 3],
    ['id_insumo' => 20, 'precio' => 25.00, 'tiempo_entrega' => 5],
    ['id_insumo' => 30, 'precio' => 5.75, 'tiempo_entrega' => 1],
];

$db->queries = [];
$proveedor->addInsumos(1, $insumos);

// We expect 2 queries: 1 DELETE, 1 INSERT (instead of 1 DELETE + 3 separate INSERTs)
$totalQueries = count($db->queries);
echo "Total queries executed: {$totalQueries}\n";

assert($totalQueries === 2, "Expected 2 queries (1 DELETE, 1 multi-row INSERT), got {$totalQueries}");

$deleteQuery = $db->queries[0];
echo "Delete query SQL: {$deleteQuery['sql']}\n";
assert(str_contains($deleteQuery['sql'], "DELETE FROM proveedor_insumos WHERE id_proveedor = ?"), "Expected DELETE query");

$insertQuery = $db->queries[1];
echo "Insert query SQL: {$insertQuery['sql']}\n";
echo "Insert query params: " . json_encode($insertQuery['params']) . "\n";

assert(str_contains($insertQuery['sql'], "VALUES (?, ?, ?, ?), (?, ?, ?, ?), (?, ?, ?, ?)"), "Expected multi-row VALUES in INSERT query");
assert(count($insertQuery['params']) === 12, "Expected 12 parameters (3 insumos * 4 columns)");
assert($insertQuery['params'] === [1, 10, 15.50, 3, 1, 20, 25.00, 5, 1, 30, 5.75, 1], "Parameters match expected values");

echo "\nAll assertions PASSED successfully!\n";
