<?php

require_once __DIR__ . '/../app/Models/BaseModel.php';
require_once __DIR__ . '/../app/Models/Lote.php';

class MockLoteDB {
    public $queries = [];
    public $lotesData = [];

    public function fetchAll($sql, $params = []) {
        $this->queries[] = ['sql' => $sql, 'params' => $params];
        return $this->lotesData;
    }

    public function execute($sql, $params = []) {
        $this->queries[] = ['sql' => $sql, 'params' => $params];
        return true;
    }
}

class TestLote extends \App\Models\Lote {
    public function __construct($db) {
        $this->db = $db;
    }
}

function verifyLoteOptimization() {
    echo "--- Testing Lote::descontarStock Optimization ---\n";

    // Test 1: Single Lot Deduction
    $mockDb1 = new MockLoteDB();
    $mockDb1->lotesData = [
        ['id' => 101, 'cantidad_actual' => 50]
    ];

    $loteModel1 = new TestLote($mockDb1);
    $pendiente1 = $loteModel1->descontarStock('insumo', 5, 20, 1);

    assert($pendiente1 == 0, "Pendiente should be 0");
    assert(count($mockDb1->queries) == 2, "Should execute 1 SELECT and 1 UPDATE");
    assert(strpos($mockDb1->queries[0]['sql'], 'SELECT id, cantidad_actual') !== false, "SELECT should be optimized");
    assert(strpos($mockDb1->queries[1]['sql'], 'UPDATE lotes SET cantidad_actual = ?, estado = ? WHERE id = ?') !== false, "UPDATE single lot query");
    assert($mockDb1->queries[1]['params'] === [30, 'activo', 101], "Update params correct");
    echo "✅ Test 1 Passed: Single lot deduction works as expected!\n";

    // Test 2: Multi-Lot Deduction (consolidated into 1 batch UPDATE query)
    $mockDb2 = new MockLoteDB();
    $mockDb2->lotesData = [
        ['id' => 201, 'cantidad_actual' => 15],
        ['id' => 202, 'cantidad_actual' => 20]
    ];

    $loteModel2 = new TestLote($mockDb2);
    $pendiente2 = $loteModel2->descontarStock('insumo', 5, 25, 1);

    assert($pendiente2 == 0, "Pendiente should be 0");
    assert(count($mockDb2->queries) == 2, "Should execute 1 SELECT and 1 batch UPDATE (2 queries total instead of 3)");
    assert(strpos($mockDb2->queries[1]['sql'], 'CASE') !== false, "Batch UPDATE should use CASE statement");

    // Check parameters for batch update
    // Expected params: [201, 0, 202, 10, 201, 'agotado', 202, 'activo', 201, 202]
    $expectedParams = [201, 0, 202, 10, 201, 'agotado', 202, 'activo', 201, 202];
    assert($mockDb2->queries[1]['params'] === $expectedParams, "Batch update params correct");
    echo "✅ Test 2 Passed: Multi-lot deduction consolidated into single batch UPDATE!\n";

    echo "--- All Lote::descontarStock tests passed successfully! ---\n";
}

try {
    verifyLoteOptimization();
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}
