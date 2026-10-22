<?php

declare(strict_types=1);

namespace App\Core {
    use PDO;

    class Database
    {
        private static ?Database $instance = null;
        public ?PDO $connection = null;
        public array $executedQueries = [];

        public static function getInstance(): self
        {
            if (self::$instance === null) {
                self::$instance = (new \ReflectionClass(self::class))->newInstanceWithoutConstructor();
            }
            return self::$instance;
        }

        public function beginTransaction(): bool
        {
            $this->executedQueries[] = ['sql' => 'BEGIN TRANSACTION', 'params' => []];
            return true;
        }

        public function commit(): bool
        {
            $this->executedQueries[] = ['sql' => 'COMMIT', 'params' => []];
            return true;
        }

        public function rollback(): bool
        {
            $this->executedQueries[] = ['sql' => 'ROLLBACK', 'params' => []];
            return true;
        }

        public function fetchAll(string $sql, array $params = []): array
        {
            $this->executedQueries[] = ['sql' => $sql, 'params' => $params];

            if (str_contains($sql, 'FROM insumos')) {
                return [
                    ['id' => 10, 'stock_actual' => 50.0, 'precio_unitario' => 5.0],
                    ['id' => 20, 'stock_actual' => 100.0, 'precio_unitario' => 2.5],
                ];
            }

            if (str_contains($sql, 'FROM productos')) {
                return [
                    ['id' => 100, 'stock_actual' => 30.0],
                    ['id' => 200, 'stock_actual' => 40.0],
                ];
            }

            return [];
        }

        public function fetchOne(string $sql, array $params = []): array|false
        {
            $this->executedQueries[] = ['sql' => $sql, 'params' => $params];
            return false;
        }

        public function execute(string $sql, array $params = []): int
        {
            $this->executedQueries[] = ['sql' => $sql, 'params' => $params];
            return 1;
        }

        public function lastInsertId(): string
        {
            return "999";
        }
    }
}

namespace App\Models {
    require_once __DIR__ . '/../app/Models/BaseModel.php';
    require_once __DIR__ . '/../app/Models/Compra.php';

    class MockLote
    {
        public array $batchRegistered = [];

        public function registrarBatch(array $lotes): bool
        {
            $this->batchRegistered = $lotes;
            return true;
        }
    }
}

namespace {
    use App\Core\Database;
    use App\Models\Compra;
    use App\Models\MockLote;

    $db = Database::getInstance();
    $mockLote = new MockLote();

    $refClass = new \ReflectionClass(Compra::class);
    $compraModel = $refClass->newInstanceWithoutConstructor();

    $dbProp = $refClass->getProperty('db');
    $dbProp->setAccessible(true);
    $dbProp->setValue($compraModel, $db);

    $compraModel->loteModel = $mockLote;

    $compraData = [
        'id_sucursal' => 1,
        'id_usuario' => 2,
        'id_proveedor' => 3,
        'fecha_compra' => '2025-01-24',
        'total' => 150.0
    ];

    $detalles = [
        [
            'tipo_item' => 'insumo',
            'id_item' => 10,
            'cantidad' => 10,
            'costo' => 6.0,
            'subtotal' => 60.0,
            'lote_codigo' => 'L10',
            'fecha_vencimiento' => '2025-12-31'
        ],
        [
            'tipo_item' => 'insumo',
            'id_item' => 20,
            'cantidad' => 20,
            'costo' => 3.0,
            'subtotal' => 60.0,
            'lote_codigo' => 'L20',
            'fecha_vencimiento' => '2025-12-31'
        ],
        [
            'tipo_item' => 'producto',
            'id_item' => 100,
            'cantidad' => 5,
            'costo' => 4.0,
            'subtotal' => 20.0,
            'lote_codigo' => null,
            'fecha_vencimiento' => null
        ],
        [
            'tipo_item' => 'producto',
            'id_item' => 200,
            'cantidad' => 2,
            'costo' => 5.0,
            'subtotal' => 10.0,
            'lote_codigo' => null,
            'fecha_vencimiento' => null
        ]
    ];

    $compraId = $compraModel->createWithDetails($compraData, $detalles);

    assert((string)$compraId === "999", "Expected compraId to be '999'");

    $queries = $db->executedQueries;

    $insumoUpdateQuery = null;
    $productoUpdateQuery = null;

    foreach ($queries as $q) {
        if (str_contains($q['sql'], 'UPDATE insumos SET')) {
            $insumoUpdateQuery = $q;
        }
        if (str_contains($q['sql'], 'UPDATE productos SET')) {
            $productoUpdateQuery = $q;
        }
    }

    assert($insumoUpdateQuery !== null, "Insumo batch update query was not executed");
    assert($productoUpdateQuery !== null, "Producto batch update query was not executed");

    // Verify CASE WHEN in insumos query
    assert(str_contains($insumoUpdateQuery['sql'], 'WHEN id = ? THEN ?'), "Insumos stock CASE WHEN query invalid");
    assert(str_contains($insumoUpdateQuery['sql'], 'WHERE id IN (?,?)'), "Insumos WHERE IN query invalid");

    // Insumo parameters check
    // stock: id=10 => new stock = 60.0, id=20 => new stock = 120.0
    // precio: id=10 => (50*5 + 10*6)/60 = 5.166666..., id=20 => (100*2.5 + 20*3)/120 = 2.58333...
    // ids: 10, 20
    $insumoParams = $insumoUpdateQuery['params'];
    assert(count($insumoParams) === 10, "Insumos parameters count mismatch: got " . count($insumoParams));
    assert($insumoParams[0] === 10 && $insumoParams[1] == 60.0, "Insumo 10 stock param mismatch");
    assert($insumoParams[2] === 20 && $insumoParams[3] == 120.0, "Insumo 20 stock param mismatch");

    // Verify CASE WHEN in productos query
    assert(str_contains($productoUpdateQuery['sql'], 'WHEN id = ? THEN ?'), "Productos stock CASE WHEN query invalid");
    assert(str_contains($productoUpdateQuery['sql'], 'WHERE id IN (?,?)'), "Productos WHERE IN query invalid");

    $productoParams = $productoUpdateQuery['params'];
    assert(count($productoParams) === 6, "Productos parameters count mismatch: got " . count($productoParams));
    assert($productoParams[0] === 100 && $productoParams[1] == 35.0, "Producto 100 stock param mismatch");
    assert($productoParams[2] === 200 && $productoParams[3] == 42.0, "Producto 200 stock param mismatch");

    echo "✅ Compra batch UPDATE optimization test passed successfully!\n";
}
