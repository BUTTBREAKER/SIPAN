<?php

namespace App\Core;

class Database
{
    private static ?Database $instance = null;
    public array $executedQueries = [];
    public bool $inTransaction = false;

    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function beginTransaction()
    {
        $this->inTransaction = true;
    }

    public function commit()
    {
        $this->inTransaction = false;
    }

    public function rollback()
    {
        $this->inTransaction = false;
    }

    public function fetchAll(string $sql, array $params = []): array
    {
        $this->executedQueries[] = ['sql' => $sql, 'params' => $params];

        if (str_contains($sql, 'FROM insumos')) {
            return [
                ['id' => 1, 'stock_actual' => 10.0, 'precio_unitario' => 2.0],
                ['id' => 2, 'stock_actual' => 20.0, 'precio_unitario' => 5.0]
            ];
        }

        if (str_contains($sql, 'FROM productos')) {
            return [
                ['id' => 10, 'stock_actual' => 50.0],
                ['id' => 20, 'stock_actual' => 100.0]
            ];
        }

        return [];
    }

    public function execute(string $sql, array $params = []): bool
    {
        $this->executedQueries[] = ['sql' => $sql, 'params' => $params];
        return true;
    }

    public function lastInsertId(): string
    {
        return '101';
    }
}

namespace Test;

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Database;
use App\Models\Compra;

echo "=== Testing Compra::createWithDetails Batch Updates ===\n\n";

$db = Database::getInstance();
$compra = new Compra();

// Mock loteModel property
$ref = new \ReflectionClass($compra);
if ($ref->hasProperty('loteModel')) {
    $prop = $ref->getProperty('loteModel');
    $prop->setAccessible(true);
    $prop->setValue($compra, new class {
        public function registrarBatch($batch)
        {
            return true;
        }
    });
} else {
    $compra->loteModel = new class {
        public function registrarBatch($batch)
        {
            return true;
        }
    };
}

$compraData = [
    'id_sucursal' => 1,
    'id_usuario' => 2,
    'id_proveedor' => 3,
    'fecha_compra' => '2025-01-24 10:00:00',
    'numero_comprobante' => 'INV-001',
    'total' => 150.00,
    'estado' => 'completada'
];

$detallesMulti = [
    [
        'tipo_item' => 'insumo',
        'id_item' => 1,
        'cantidad' => 5,
        'costo_unitario' => 3.0,
        'subtotal' => 15.0,
        'lote_codigo' => 'LOTE-1',
        'fecha_vencimiento' => '2026-01-01'
    ],
    [
        'tipo_item' => 'insumo',
        'id_item' => 2,
        'cantidad' => 10,
        'costo_unitario' => 6.0,
        'subtotal' => 60.0,
        'lote_codigo' => 'LOTE-2',
        'fecha_vencimiento' => '2026-01-01'
    ],
    [
        'tipo_item' => 'producto',
        'id_item' => 10,
        'cantidad' => 15,
        'costo_unitario' => 2.0,
        'subtotal' => 30.0
    ],
    [
        'tipo_item' => 'producto',
        'id_item' => 20,
        'cantidad' => 25,
        'costo_unitario' => 1.8,
        'subtotal' => 45.0
    ]
];

$purchaseId = $compra->createWithDetails($compraData, $detallesMulti);

echo "Purchase ID returned: $purchaseId\n";
assert($purchaseId === '101', "Expected purchase ID 101");

// Inspect executed queries for multi-item
$updateInsumosQuery = null;
$updateProductosQuery = null;

foreach ($db->executedQueries as $q) {
    if (str_contains($q['sql'], 'UPDATE insumos')) {
        $updateInsumosQuery = $q;
    }
    if (str_contains($q['sql'], 'UPDATE productos')) {
        $updateProductosQuery = $q;
    }
}

echo "\n--- Multi-item Insumos Update Query ---\n";
echo "SQL: " . $updateInsumosQuery['sql'] . "\n";
echo "Params: " . json_encode($updateInsumosQuery['params']) . "\n";

echo "\n--- Multi-item Productos Update Query ---\n";
echo "SQL: " . $updateProductosQuery['sql'] . "\n";
echo "Params: " . json_encode($updateProductosQuery['params']) . "\n";

assert(str_contains($updateInsumosQuery['sql'], 'CASE id WHEN ? THEN ? WHEN ? THEN ? END'), "Insumos update should use CASE WHEN expressions");
assert(str_contains($updateInsumosQuery['sql'], 'WHERE id IN (?,?)'), "Insumos update should use WHERE id IN (?,?)");
assert(count($updateInsumosQuery['params']) === 10, "Expected 10 params for insumos batch update");

assert(str_contains($updateProductosQuery['sql'], 'CASE id WHEN ? THEN ? WHEN ? THEN ? END'), "Productos update should use CASE WHEN expressions");
assert(str_contains($updateProductosQuery['sql'], 'WHERE id IN (?,?)'), "Productos update should use WHERE id IN (?,?)");
assert(count($updateProductosQuery['params']) === 6, "Expected 6 params for productos batch update");

// Reset executed queries for single item test
$db->executedQueries = [];

$detallesSingle = [
    [
        'tipo_item' => 'insumo',
        'id_item' => 1,
        'cantidad' => 5,
        'costo_unitario' => 3.0,
        'subtotal' => 15.0
    ],
    [
        'tipo_item' => 'producto',
        'id_item' => 10,
        'cantidad' => 15,
        'costo_unitario' => 2.0,
        'subtotal' => 30.0
    ]
];

$compra->createWithDetails($compraData, $detallesSingle);

$updateInsumosQuerySingle = null;
$updateProductosQuerySingle = null;

foreach ($db->executedQueries as $q) {
    if (str_contains($q['sql'], 'UPDATE insumos')) {
        $updateInsumosQuerySingle = $q;
    }
    if (str_contains($q['sql'], 'UPDATE productos')) {
        $updateProductosQuerySingle = $q;
    }
}

echo "\n--- Single-item Insumos Update Query ---\n";
echo "SQL: " . $updateInsumosQuerySingle['sql'] . "\n";
echo "Params: " . json_encode($updateInsumosQuerySingle['params']) . "\n";

echo "\n--- Single-item Productos Update Query ---\n";
echo "SQL: " . $updateProductosQuerySingle['sql'] . "\n";
echo "Params: " . json_encode($updateProductosQuerySingle['params']) . "\n";

assert($updateInsumosQuerySingle['sql'] === 'UPDATE insumos SET stock_actual = ?, precio_unitario = ? WHERE id = ?');
assert($updateProductosQuerySingle['sql'] === 'UPDATE productos SET stock_actual = ? WHERE id = ?');

echo "\n✅ All verification checks (multi & single item) passed successfully!\n";