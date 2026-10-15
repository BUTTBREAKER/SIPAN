<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Models\Proveedor;
use App\Core\Database;

class QueryLoggerPDO extends PDO
{
    public array $queries = [];
    public array $params = [];

    public function __construct()
    {
        parent::__construct('sqlite::memory:');
        $this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->exec("CREATE TABLE proveedor_insumos (id_proveedor INT, id_insumo INT, precio REAL, tiempo_entrega INT)");
        $this->exec("CREATE TABLE insumos (id INT, nombre TEXT, unidad_medida TEXT, stock_actual REAL, stock_minimo REAL, id_sucursal INT)");
    }

    public function prepare(string $query, array $options = []): PDOStatement|false
    {
        $this->queries[] = $query;
        return parent::prepare($query, $options);
    }
}

$pdo = new QueryLoggerPDO();

// Setup Database singleton instance with Reflection
$dbReflector = new ReflectionClass(Database::class);
$databaseInstance = $dbReflector->newInstanceWithoutConstructor();

$connProp = $dbReflector->getProperty('connection');
$connProp->setAccessible(true);
$connProp->setValue($databaseInstance, $pdo);

$instProp = $dbReflector->getProperty('instance');
$instProp->setAccessible(true);
$instProp->setValue(null, $databaseInstance);

$proveedorModel = new Proveedor();

echo "Testing Proveedor::addInsumos (Batched Multi-row INSERT)...\n";
$insumos = [
    ['id_insumo' => 10, 'precio' => 12.5, 'tiempo_entrega' => 3],
    ['id_insumo' => 11, 'precio' => 8.0, 'tiempo_entrega' => 5],
    ['id_insumo' => 12, 'precio' => 15.0, 'tiempo_entrega' => 2],
];

$proveedorModel->addInsumos(5, $insumos);

// Assert queries
assert(count($pdo->queries) === 2, "Expected 2 prepared statements for addInsumos");
assert(str_contains($pdo->queries[0], "DELETE FROM proveedor_insumos WHERE id_proveedor = ?"), "Query 1 should be DELETE");
assert(str_contains($pdo->queries[1], "INSERT INTO proveedor_insumos"), "Query 2 should be INSERT");
assert(str_contains($pdo->queries[1], "VALUES (?, ?, ?, ?), (?, ?, ?, ?), (?, ?, ?, ?)"), "Query 2 should contain 3 batched value placeholders");

echo "✓ Proveedor::addInsumos batching verified successfully!\n\n";

// Reset queries
$pdo->queries = [];

echo "Testing Proveedor::getInsumosSinProveedor (NOT EXISTS optimization)...\n";
$proveedorModel->getInsumosSinProveedor(1);

assert(count($pdo->queries) === 1, "Expected 1 prepared statement for getInsumosSinProveedor");
assert(str_contains($pdo->queries[0], "NOT EXISTS"), "Query should use NOT EXISTS clause");
assert(str_contains($pdo->queries[0], "SELECT 1 FROM proveedor_insumos pi WHERE pi.id_insumo = i.id"), "Query should correlate subquery properly");
assert(!str_contains($pdo->queries[0], "GROUP BY"), "Query should not use GROUP BY");
assert(!str_contains($pdo->queries[0], "HAVING"), "Query should not use HAVING");

echo "✓ Proveedor::getInsumosSinProveedor NOT EXISTS verified successfully!\n";
echo "All Proveedor optimizations verified successfully!\n";
