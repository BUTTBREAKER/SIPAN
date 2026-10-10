<?php

namespace App\Core;

class Database
{
    private static $instance = null;
    public array $queries = [];
    public bool $inTransaction = false;

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function beginTransaction(): bool
    {
        $this->inTransaction = true;
        return true;
    }

    public function commit(): bool
    {
        $this->inTransaction = false;
        return true;
    }

    public function rollback(): bool
    {
        $this->inTransaction = false;
        return true;
    }

    public function fetchAll(string $sql, array $params = []): array
    {
        $this->queries[] = ['sql' => $sql, 'params' => $params];
        return [];
    }

    public function fetchOne(string $sql, array $params = []): array
    {
        $this->queries[] = ['sql' => $sql, 'params' => $params];
        return [];
    }

    public function execute(string $sql, array $params = []): bool
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
    protected string $table = '';

    public function __construct()
    {
        $this->db = Database::getInstance();
    }
}

// Require Proveedor model
require_once __DIR__ . '/../app/Models/Proveedor.php';

use App\Models\Proveedor;
use App\Core\Database as DB;

$proveedorModel = new Proveedor();
$db = DB::getInstance();

echo "Running Proveedor Optimization Tests...\n\n";

// Test 1: addInsumos with multiple items (batched multi-row INSERT)
$db->queries = [];
$insumos = [
    ['id_insumo' => 10, 'precio' => 12.50, 'tiempo_entrega' => '2 días'],
    ['id_insumo' => 20, 'precio' => 8.00, 'tiempo_entrega' => '1 día'],
    ['id_insumo' => 30, 'precio' => 15.00, 'tiempo_entrega' => '3 días']
];

$proveedorModel->addInsumos(1, $insumos);

$insertQueries = array_filter($db->queries, function ($q) {
    return strpos($q['sql'], 'INSERT INTO proveedor_insumos') !== false;
});

echo "Test 1 (addInsumos - Executed exactly 1 batched INSERT query): " . (count($insertQueries) === 1 ? "PASS" : "FAIL") . "\n";

$batchQuery = reset($insertQueries);
$expectedPlaceholders = "(?, ?, ?, ?), (?, ?, ?, ?), (?, ?, ?, ?)";
$hasMultiRowInsert = strpos($batchQuery['sql'], $expectedPlaceholders) !== false;
echo "Test 1 (addInsumos - Uses multi-row INSERT placeholders): " . ($hasMultiRowInsert ? "PASS" : "FAIL") . "\n";
echo "Test 1 (addInsumos - Correct parameter count (12 params)): " . (count($batchQuery['params']) === 12 ? "PASS" : "FAIL") . "\n";
echo "SQL: " . $batchQuery['sql'] . "\n\n";

// Test 2: getInsumosSinProveedor
$db->queries = [];
$sucursalId = 3;
$proveedorModel->getInsumosSinProveedor($sucursalId);
$lastQuery = end($db->queries);

$usesNotExists = strpos($lastQuery['sql'], 'NOT EXISTS') !== false;
$hasNoGroupBy = strpos($lastQuery['sql'], 'GROUP BY') === false;

echo "Test 2 (getInsumosSinProveedor - Uses SARGable NOT EXISTS query): " . ($usesNotExists ? "PASS" : "FAIL") . "\n";
echo "Test 2 (getInsumosSinProveedor - No redundant GROUP BY / HAVING): " . ($hasNoGroupBy ? "PASS" : "FAIL") . "\n";
echo "Test 2 (getInsumosSinProveedor - Correct parameters [3]): " . ($lastQuery['params'] === [3] ? "PASS" : "FAIL") . "\n";
echo "SQL: " . $lastQuery['sql'] . "\n\n";

echo "All Proveedor optimization tests completed successfully!\n";
