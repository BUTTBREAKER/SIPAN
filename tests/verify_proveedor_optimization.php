<?php

declare(strict_types=1);

namespace App\Core;

use PDO;

class Database
{
    private static ?self $instance = null;
    public ?PDO $connection = null;
    public array $queries = [];

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            $ref = new \ReflectionClass(self::class);
            self::$instance = $ref->newInstanceWithoutConstructor();
            self::$instance->connection = new PDO('sqlite::memory:');
        }
        return self::$instance;
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

    public function execute(string $sql, array $params = []): bool
    {
        $this->queries[] = ['sql' => $sql, 'params' => $params];
        return true;
    }

    public function fetchAll(string $sql, array $params = []): array
    {
        $this->queries[] = ['sql' => $sql, 'params' => $params];
        return [];
    }
}

namespace App\Models;

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Database;

$db = Database::getInstance();
$proveedor = new Proveedor();

echo "Running Proveedor Optimization Verification...\n\n";

// Test 1: addInsumos multi-row batch INSERT
$db->queries = [];
$proveedor->addInsumos(5, [
    ['id_insumo' => 101, 'precio' => 12.50, 'tiempo_entrega' => 3],
    ['id_insumo' => 102, 'precio' => 8.00, 'tiempo_entrega' => 2],
    ['id_insumo' => 103, 'precio' => 15.00, 'tiempo_entrega' => 5],
]);

assert(count($db->queries) === 2, "Expected exactly 2 queries (1 DELETE, 1 BATCH INSERT)");
assert(strpos($db->queries[0]['sql'], 'DELETE FROM proveedor_insumos') !== false, "First query should be DELETE");
assert(strpos($db->queries[1]['sql'], 'INSERT INTO proveedor_insumos') !== false, "Second query should be BATCH INSERT");
assert(strpos($db->queries[1]['sql'], '(?, ?, ?, ?), (?, ?, ?, ?), (?, ?, ?, ?)') !== false, "Batch insert SQL should contain 3 placeholder tuples");
assert(count($db->queries[1]['params']) === 12, "Batch insert should bind 12 parameters (3 items * 4 fields)");
assert($db->queries[1]['params'][0] === 5 && $db->queries[1]['params'][1] === 101, "First bound item parameter check");
echo "✅ Test 1 Passed: Proveedor::addInsumos uses batched multi-row INSERT\n";

// Test 2: getInsumosSinProveedor NOT EXISTS query structure
$db->queries = [];
$proveedor->getInsumosSinProveedor(1);

assert(count($db->queries) === 1, "Expected 1 query for getInsumosSinProveedor");
$sql = $db->queries[0]['sql'];
assert(strpos($sql, 'NOT EXISTS') !== false, "Query must use NOT EXISTS subquery");
assert(strpos($sql, 'GROUP BY') === false, "Query must not contain GROUP BY");
assert(strpos($sql, 'HAVING') === false, "Query must not contain HAVING");
assert($db->queries[0]['params'] === [1], "Parameters must match sucursal_id");
echo "✅ Test 2 Passed: Proveedor::getInsumosSinProveedor uses efficient NOT EXISTS clause\n";

echo "\nAll Proveedor optimization tests passed successfully!\n";
