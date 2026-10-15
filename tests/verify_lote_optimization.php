<?php

namespace App\Core;

class Database
{
    private static $instance = null;
    public array $queries = [];
    public array $mockFetchAllResults = [];

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
        return $this->mockFetchAllResults;
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

namespace Test;

require_once __DIR__ . '/../app/Models/BaseModel.php';
require_once __DIR__ . '/../app/Models/Lote.php';

use App\Models\Lote;
use App\Core\Database as DB;

$db = DB::getInstance();
$loteModel = new Lote();

echo "Running Lote::descontarStock Optimization Tests...\n\n";

// Test 1: Single Lot Deduction
$db->queries = [];
$db->mockFetchAllResults = [
    ['id' => 10, 'cantidad_actual' => 100.0]
];

$pendiente = $loteModel->descontarStock('insumo', 5, 30, 1);

assert($pendiente === 0.0, "Pendiente should be 0");
assert(count($db->queries) === 2, "Expected 2 queries (1 SELECT, 1 UPDATE)");

$selectQuery = $db->queries[0];
echo "Test 1 SELECT Query: " . $selectQuery['sql'] . "\n";
assert(strpos($selectQuery['sql'], 'SELECT id, cantidad_actual') !== false, "SELECT should only request id, cantidad_actual");

$updateQuery = $db->queries[1];
echo "Test 1 UPDATE Query: " . $updateQuery['sql'] . "\n";
echo "Test 1 UPDATE Params: " . json_encode($updateQuery['params']) . "\n";
assert(strpos($updateQuery['sql'], 'UPDATE lotes SET cantidad_actual = ?, estado = ? WHERE id = ?') !== false, "Single UPDATE query structure correct");
assert($updateQuery['params'] === [70.0, 'activo', 10], "Single UPDATE params correct");
echo "✅ Test 1 (Single Lot Deduction) PASSED\n\n";


// Test 2: Multi-Lot Deduction (Batched CASE UPDATE)
$db->queries = [];
$db->mockFetchAllResults = [
    ['id' => 101, 'cantidad_actual' => 20.0],
    ['id' => 102, 'cantidad_actual' => 50.0],
    ['id' => 103, 'cantidad_actual' => 40.0]
];

$pendiente = $loteModel->descontarStock('insumo', 5, 60, 1);

assert($pendiente === 0.0, "Pendiente should be 0");
assert(count($db->queries) === 2, "Expected 2 queries (1 SELECT, 1 batched UPDATE)");

$batchUpdateQuery = $db->queries[1];
echo "Test 2 Batch UPDATE Query: " . $batchUpdateQuery['sql'] . "\n";
echo "Test 2 Batch UPDATE Params: " . json_encode($batchUpdateQuery['params']) . "\n";

assert(strpos($batchUpdateQuery['sql'], 'CASE id WHEN ? THEN ?') !== false, "Batch UPDATE should use CASE expressions");
assert(strpos($batchUpdateQuery['sql'], 'WHERE id IN (?,?)') !== false, "Batch UPDATE should use WHERE id IN");

// Expected params for 2 affected lots (101 depleted to 0, 102 reduced to 10):
// stockParams: [101, 0.0, 102, 10.0]
// estadoParams: [101, 'agotado', 102, 'activo']
// ids: [101, 102]
$expectedParams = [101, 0.0, 102, 10.0, 101, 'agotado', 102, 'activo', 101, 102];
assert($batchUpdateQuery['params'] === $expectedParams, "Batch UPDATE params match expected order and values");
echo "✅ Test 2 (Multi-Lot Batched Deduction) PASSED\n\n";

echo "ALL LOTE OPTIMIZATION TESTS PASSED SUCCESSFULLY! 🎉\n";
