<?php

namespace App\Core;

class Database
{
    private static $instance = null;
    public array $queries = [];

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function fetchAll(string $sql, array $params = []): array
    {
        $this->queries[] = ['sql' => $sql, 'params' => $params];
        return [];
    }

    public function fetchOne(string $sql, array $params = []): array|false
    {
        $this->queries[] = ['sql' => $sql, 'params' => $params];
        return [];
    }

    public function execute(string $sql, array $params = []): int|bool
    {
        $this->queries[] = ['sql' => $sql, 'params' => $params];
        return true;
    }

    public function beginTransaction(): bool
    {
        return true;
    }

    public function commit(): bool
    {
        return true;
    }

    public function rollback(): bool
    {
        return true;
    }
}

namespace App\Models;

require_once __DIR__ . '/../app/Models/BaseModel.php';
require_once __DIR__ . '/../app/Models/Proveedor.php';

use App\Models\Proveedor;
use App\Core\Database;

$db = Database::getInstance();
$proveedorModel = new Proveedor();

echo "Running Proveedor Model Optimization Verification...\n\n";

// Test 1: addInsumos - Batched multi-row INSERT verification
$db->queries = [];
$insumos = [
    ['id_insumo' => 10, 'precio' => 15.50, 'tiempo_entrega' => 3],
    ['id_insumo' => 11, 'precio' => 20.00, 'tiempo_entrega' => 5],
    ['id_insumo' => 12, 'precio' => 5.25, 'tiempo_entrega' => 1],
];

$proveedorModel->addInsumos(1, $insumos);

assert(count($db->queries) === 2, "Expected exactly 2 queries (1 DELETE + 1 batched INSERT)");
echo "Query 1 (DELETE): " . $db->queries[0]['sql'] . "\n";
echo "Query 2 (Batched INSERT): " . $db->queries[1]['sql'] . "\n";
echo "INSERT Params: " . json_encode($db->queries[1]['params']) . "\n";

assert(
    str_contains($db->queries[1]['sql'], "VALUES (?, ?, ?, ?), (?, ?, ?, ?), (?, ?, ?, ?)"),
    "INSERT query should contain multi-row placeholders"
);
assert(
    count($db->queries[1]['params']) === 12,
    "Expected 12 parameters (4 params * 3 insumos)"
);
echo "[PASS] Proveedor::addInsumos uses single batched INSERT statement.\n\n";

// Test 2: getInsumosSinProveedor - NOT EXISTS verification
$db->queries = [];
$proveedorModel->getInsumosSinProveedor(2);

assert(count($db->queries) === 1, "Expected 1 query");
$sql = $db->queries[0]['sql'];
echo "Query (getInsumosSinProveedor):\n" . $sql . "\n";

assert(
    str_contains($sql, "NOT EXISTS"),
    "Query should use NOT EXISTS clause"
);
assert(
    !str_contains($sql, "GROUP BY"),
    "Query should not use GROUP BY"
);
assert(
    !str_contains($sql, "HAVING"),
    "Query should not use HAVING clause"
);
assert(
    $db->queries[0]['params'] === [2],
    "Params should match sucursal_id = 2"
);

echo "[PASS] Proveedor::getInsumosSinProveedor uses optimized NOT EXISTS clause.\n\n";

echo "All Proveedor optimizations verified successfully!\n";
