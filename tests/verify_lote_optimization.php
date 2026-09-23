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

    public function fetchAll($sql, $params = [])
    {
        $this->queries[] = ['sql' => $sql, 'params' => $params];
        // Return 2 mock lotes for multi-lot deduction testing
        return [
            ['id' => 10, 'cantidad_actual' => 30.0],
            ['id' => 20, 'cantidad_actual' => 50.0]
        ];
    }

    public function execute($sql, $params = [])
    {
        $this->queries[] = ['sql' => $sql, 'params' => $params];
        return 1;
    }
}

namespace Tests;

require_once __DIR__ . '/../vendor/autoload.php';

use App\Models\Lote;
use App\Core\Database;

echo "--- Testing Lote::descontarStock Optimization ---\n";

$db = Database::getInstance();
$loteModel = new Lote();

// Test 1: Multi-lot deduction (deduct 50 units from lot 10 [30 units] and lot 20 [50 units])
$db->queries = [];
$remaining = $loteModel->descontarStock('insumo', 5, 50.0, 1);

assert($remaining === 0.0, "Expected remaining to be 0");

$selectQuery = $db->queries[0];
echo "Select Query: " . $selectQuery['sql'] . "\n";
assert(strpos($selectQuery['sql'], "SELECT id, cantidad_actual FROM lotes") !== false, "Expected SELECT id, cantidad_actual");

$updateQuery = $db->queries[1];
echo "Batch Update Query: " . $updateQuery['sql'] . "\n";
echo "Params: " . json_encode($updateQuery['params']) . "\n";

assert(strpos($updateQuery['sql'], "UPDATE lotes") !== false, "Expected UPDATE lotes");
assert(strpos($updateQuery['sql'], "SET cantidad_actual = CASE") !== false, "Expected CASE expression for stock");
assert(strpos($updateQuery['sql'], "estado = CASE") !== false, "Expected CASE expression for estado");
assert(strpos($updateQuery['sql'], "WHERE id IN (?,?)") !== false, "Expected WHERE id IN (?,?)");

// Check parameter bindings for batch update:
// Lote 10: new stock = 0, state = agotado
// Lote 20: new stock = 30, state = activo
// Params: [10, 0, 20, 30, 10, 'agotado', 20, 'activo', 10, 20]
assert(count($updateQuery['params']) === 10, "Expected 10 bound parameters for 2 lot updates");
assert($updateQuery['params'][1] == 0, "Lot 10 stock should be 0");
assert($updateQuery['params'][3] == 30, "Lot 20 stock should be 30");
assert($updateQuery['params'][5] === 'agotado', "Lot 10 state should be 'agotado'");
assert($updateQuery['params'][7] === 'activo', "Lot 20 state should be 'activo'");

echo "✅ All Lote::descontarStock optimization assertions passed successfully!\n";
