<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Controllers\PedidosController;
use App\Core\Database;
use App\Models\Pedido;
use App\Models\Producto;
use App\Models\Receta;

class MockPDOStatement extends PDOStatement
{
    private array $data;
    private array $params = [];

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    #[\ReturnTypeWillChange]
    public function execute($params = null): bool
    {
        if ($params !== null) {
            $this->params = $params;
        }
        return true;
    }

    #[\ReturnTypeWillChange]
    public function fetchAll($mode = PDO::FETCH_DEFAULT, ...$args): array
    {
        return $this->data;
    }

    #[\ReturnTypeWillChange]
    public function fetch($mode = PDO::FETCH_DEFAULT, ...$args): mixed
    {
        return $this->data[0] ?? false;
    }
}

class QueryTrackerPDO extends PDO
{
    public array $queries = [];

    public function __construct()
    {
        parent::__construct('sqlite::memory:');
    }

    #[\ReturnTypeWillChange]
    public function prepare($query, $options = []): PDOStatement|false
    {
        $this->queries[] = $query;
        $data = [];

        if (str_contains($query, 'FROM cajas WHERE id_sucursal = ? AND estado = \'abierta\'')) {
            $data = [['id' => 1, 'id_sucursal' => 1, 'estado' => 'abierta']];
        } elseif (str_contains($query, 'WHERE id_cliente = ? AND id_sucursal = ?')) {
            $data = [['id' => 10, 'id_cliente' => 1, 'id_sucursal' => 1, 'total' => 100]];
        } elseif (str_contains($query, 'WHERE pp.id_pedido = ?')) {
            $data = [
                ['id_pedido' => 10, 'id_producto' => 101, 'cantidad' => 2, 'producto_nombre' => 'Pan Baguette'],
                ['id_pedido' => 10, 'id_producto' => 102, 'cantidad' => 3, 'producto_nombre' => 'Croissant']
            ];
        } elseif (str_contains($query, 'WHERE id IN (?,?)') || str_contains($query, 'WHERE id IN (?, ?)')) {
            $data = [
                ['id' => 101, 'nombre' => 'Pan Baguette', 'precio_costo' => 1.5],
                ['id' => 102, 'nombre' => 'Croissant', 'precio_costo' => 2.0]
            ];
        } elseif (str_contains($query, 'WHERE id_producto IN (?,?)') || str_contains($query, 'WHERE id_producto IN (?, ?)')) {
            $data = [
                ['id' => 50, 'id_producto' => 101, 'rendimiento' => 10],
                ['id' => 51, 'id_producto' => 102, 'rendimiento' => 5]
            ];
        } elseif (str_contains($query, 'WHERE ri.id_receta IN (?,?)') || str_contains($query, 'WHERE ri.id_receta IN (?, ?)')) {
            $data = [
                ['id_receta' => 50, 'id_insumo' => 1, 'nombre' => 'Harina', 'cantidad' => 5, 'unidad_medida' => 'kg', 'precio_unitario' => 1.0],
                ['id_receta' => 51, 'id_insumo' => 2, 'nombre' => 'Mantequilla', 'cantidad' => 2, 'unidad_medida' => 'kg', 'precio_unitario' => 3.0]
            ];
        }

        return new MockPDOStatement($data);
    }
}

// Configurar base de datos mock via Reflection
$trackerPdo = new QueryTrackerPDO();
$db = (new ReflectionClass(Database::class))->newInstanceWithoutConstructor();

$refConnection = new ReflectionProperty(Database::class, 'connection');
$refConnection->setAccessible(true);
$refConnection->setValue($db, $trackerPdo);

$refInstance = new ReflectionProperty(Database::class, 'instance');
$refInstance->setAccessible(true);
$refInstance->setValue(null, $db);

// Mock Auth session via Leaf\Http\Session
\Leaf\Http\Session::set('user_id', 1);
\Leaf\Http\Session::set('user_nombre', 'Test User');
\Leaf\Http\Session::set('user_correo', 'test@example.com');
\Leaf\Http\Session::set('user_rol', 'administrador');
\Leaf\Http\Session::set('sucursal_id', 1);

register_shutdown_function(function() use ($trackerPdo) {
    $output = ob_get_clean();
    $response = json_decode($output, true);

    if (!$response) {
        fwrite(STDERR, "Failed to parse JSON response. Output was:\n" . $output . "\n");
        exit(1);
    }

    fwrite(STDOUT, "Response status: " . ($response['success'] ? "SUCCESS" : "FAILED") . "\n");
    fwrite(STDOUT, "Executed Queries (" . count($trackerPdo->queries) . "):\n");
    foreach ($trackerPdo->queries as $i => $q) {
        fwrite(STDOUT, "  " . ($i + 1) . ". " . preg_replace('/\s+/', ' ', $q) . "\n");
    }

    $hasIndividualFind = false;
    foreach ($trackerPdo->queries as $q) {
        if (preg_replace('/\s+/', ' ', $q) === 'SELECT * FROM productos WHERE id = ?') {
            $hasIndividualFind = true;
        }
    }

    if ($response['success'] !== true) {
        fwrite(STDERR, "Error: Response success is not true\n");
        exit(1);
    }
    if ($response['gasto_insumos'] != 4.6) {
        fwrite(STDERR, "Error: Calculated cost is not 4.6, got " . $response['gasto_insumos'] . "\n");
        exit(1);
    }
    if ($hasIndividualFind) {
        fwrite(STDERR, "Error: N+1 individual product find query detected\n");
        exit(1);
    }

    fwrite(STDOUT, "✓ Verification passed: PedidosController::ultimoCliente optimized with zero N+1 queries!\n");
});

ob_start();
$controller = new PedidosController();
$controller->ultimoCliente(1);
