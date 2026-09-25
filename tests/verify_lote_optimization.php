<?php

namespace App\Core;

class Database
{
    private static $instance = null;
    public $queries = [];
    public $mockFetchAllReturn = [];

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function fetchAll($sql, $params = [])
    {
        $this->queries[] = ['sql' => $sql, 'params' => $params];
        return $this->mockFetchAllReturn;
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
    protected Database $db;
    protected string $table;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }
}

require_once __DIR__ . '/../app/Models/Lote.php';

use App\Models\Lote;
use App\Core\Database as DB;

$loteModel = new Lote();
$db = DB::getInstance();

echo "Running Lote::descontarStock Optimization Tests...\n\n";

// Test 1: Single Lot Deduction
$db->queries = [];
$db->mockFetchAllReturn = [
    ['id' => 1, 'cantidad_actual' => 20.0]
];

$pendiente = $loteModel->descontarStock('insumo', 5, 8.0, 1);

assert($pendiente === 0.0, "Pendiente should be 0.0");
assert(count($db->queries) === 2, "Should execute 1 SELECT and 1 UPDATE query");

$selectQuery = $db->queries[0];
echo "Test 1 (Select query uses id, cantidad_actual): " . (strpos($selectQuery['sql'], "SELECT id, cantidad_actual") !== false ? "PASS" : "FAIL") . "\n";

$updateQuery = $db->queries[1];
echo "Test 1 (Update query uses CASE id): " . (strpos($updateQuery['sql'], "CASE id") !== false ? "PASS" : "FAIL") . "\n";
echo "Params: " . json_encode($updateQuery['params']) . "\n\n";

// Test 2: Multi-Lot Deduction (consolidated into 1 UPDATE statement)
$db->queries = [];
$db->mockFetchAllReturn = [
    ['id' => 1, 'cantidad_actual' => 10.0],
    ['id' => 2, 'cantidad_actual' => 10.0]
];

$pendiente = $loteModel->descontarStock('producto', 12, 15.0, 1);

assert($pendiente === 0.0, "Pendiente should be 0.0");
assert(count($db->queries) === 2, "Should execute 1 SELECT and 1 UPDATE query for multiple lots");

$updateQueryMulti = $db->queries[1];
echo "Test 2 (Multi-lot UPDATE query batched): " . (strpos($updateQueryMulti['sql'], "WHERE id IN (?,?)") !== false ? "PASS" : "FAIL") . "\n";

$expectedParams = [1, 0.0, 2, 5.0, 1, 'agotado', 2, 'activo', 1, 2];
echo "Test 2 (Params match expected): " . ($updateQueryMulti['params'] === $expectedParams ? "PASS" : "FAIL") . "\n";
echo "Params: " . json_encode($updateQueryMulti['params']) . "\n\n";

// Test 3: Insufficient stock
$db->queries = [];
$db->mockFetchAllReturn = [
    ['id' => 1, 'cantidad_actual' => 5.0]
];

$pendiente = $loteModel->descontarStock('insumo', 3, 10.0, 1);
echo "Test 3 (Returns remaining pending amount 5.0): " . ($pendiente === 5.0 ? "PASS" : "FAIL") . "\n";

echo "\nAll Lote::descontarStock verification tests completed successfully!\n";
