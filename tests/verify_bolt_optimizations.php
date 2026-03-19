<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Models\Compra;
use App\Models\Pedido;
use App\Models\Insumo;
use App\Models\Producto;
use App\Models\Lote;

// Mock session
$_SESSION['id_usuario'] = 1;
$_SESSION['sucursal_id'] = 1;
$_SESSION['id_negocio'] = 1;

// Mock Database to avoid real connection
class MockDatabase {
    public $queries = [];
    public $lastInsertId = 999;

    public function beginTransaction() { $this->queries[] = "BEGIN"; }
    public function commit() { $this->queries[] = "COMMIT"; }
    public function rollback() { $this->queries[] = "ROLLBACK"; }

    public function execute($sql, $params = []) {
        $this->queries[] = ['sql' => $sql, 'params' => $params];
        return 1;
    }

    public function fetchAll($sql, $params = []) {
        $this->queries[] = ['sql' => $sql, 'params' => $params];
        if (strpos($sql, 'FROM insumos') !== false) {
            return [['id' => 1, 'stock_actual' => 10, 'precio_unitario' => 100]];
        }
        if (strpos($sql, 'FROM productos') !== false) {
            return [['id' => 1, 'stock_actual' => 20]];
        }
        return [];
    }

    public function fetch($sql, $params = []) {
        return $this->fetchOne($sql, $params);
    }

    public function fetchOne($sql, $params = []) {
        $this->queries[] = ['sql' => $sql, 'params' => $params];
        if (strpos($sql, 'FROM insumos') !== false) {
            return ['id' => 1, 'stock_actual' => 10, 'precio_unitario' => 100];
        }
        if (strpos($sql, 'FROM pedidos') !== false) {
             return ['total' => 0];
        }
        return [];
    }

    public function lastInsertId() { return $this->lastInsertId++; }
}

// Subclass to avoid constructor calling Database::getInstance()
class MockCompra extends Compra {
    public $loteModel;
    public function __construct($db) { $this->db = $db; $this->table = 'compras'; }
    protected function hasColumn($column) { return true; }
    public function create($data) {
        $this->db->execute("INSERT INTO {$this->table} ...", array_values($data));
        return $this->db->lastInsertId();
    }
}

class MockPedido extends Pedido {
    public function __construct($db) { $this->db = $db; $this->table = 'pedidos'; }
    protected function hasColumn($column) { return true; }
    public function create($data) {
        $this->db->execute("INSERT INTO {$this->table} ...", array_values($data));
        return $this->db->lastInsertId();
    }
    public function query($sql, $params = []) { return $this->db->execute($sql, $params); }
    public function fetchOne($sql, $params = []) { return $this->db->fetchOne($sql, $params); }
}

class MockLote extends Lote {
    public function __construct($db) { $this->db = $db; $this->table = 'lotes'; }
}

function testOptimizations() {
    $mockDb = new MockDatabase();

    $compraModel = new MockCompra($mockDb);
    $loteModel = new MockLote($mockDb);
    $compraModel->loteModel = $loteModel;
    $pedidoModel = new MockPedido($mockDb);

    echo "--- Testing Compra::createWithDetails Optimization (Mocked) ---\n";

    $compraData = [
        'id_sucursal' => 1,
        'id_usuario' => 1,
        'id_proveedor' => 1,
        'fecha_compra' => date('Y-m-d'),
        'total' => 100,
        'estado' => 'completada'
    ];

    $detalles = [
        [
            'tipo_item' => 'insumo',
            'id_item' => 1,
            'cantidad' => 10,
            'costo_unitario' => 50.0,
            'subtotal' => 500.0,
            'lote_codigo' => 'LOTE-TEST-1',
            'fecha_vencimiento' => date('Y-m-d', strtotime('+30 days'))
        ]
    ];

    $compraModel->createWithDetails($compraData, $detalles);

    $batchInsertFound = false;
    $batchUpdateFound = false;
    foreach ($mockDb->queries as $q) {
        if (is_array($q)) {
            if (strpos($q['sql'], 'INSERT INTO compra_detalles') !== false && strpos($q['sql'], 'VALUES (?, ?, ?, ?, ?, ?, ?, ?)') !== false) {
                $batchInsertFound = true;
                echo "Found batch insert for details\n";
            }
            if (strpos($q['sql'], 'UPDATE insumos SET stock_actual = ?, precio_unitario = ?') !== false) {
                $batchUpdateFound = true;
                echo "Found batch update for insumos\n";
                // Verify calculation: initial 10@100 + new 10@50 = 20@75
                if ($q['params'][0] == 20 && $q['params'][1] == 75) {
                    echo "✅ Weighted average calculation correct: 75\n";
                } else {
                    echo "❌ Weighted average calculation incorrect: Got {$q['params'][1]}\n";
                }
            }
        }
    }

    if ($batchInsertFound && $batchUpdateFound) {
        echo "✅ Compra optimization verified (Mocked)!\n";
    } else {
        echo "❌ Compra optimization verification failed!\n";
    }

    echo "\n--- Testing Pedido::createWithProducts Optimization (Mocked) ---\n";
    $mockDb->queries = [];

    $pedidoData = [
        'id_sucursal' => 1,
        'id_usuario' => 1,
        'id_cliente' => 1,
        'fecha_pedido' => date('Y-m-d'),
        'monto_total' => 200,
        'estado_pedido' => 'pendiente',
        'estado_pago' => 'pendiente'
    ];

    $productos = [
        ['id' => 1, 'cantidad' => 2, 'precio' => 100],
        ['id' => 2, 'cantidad' => 1, 'precio' => 50]
    ];

    $pedidoModel->createWithProducts($pedidoData, $productos);

    $batchInsertFound = false;
    foreach ($mockDb->queries as $q) {
        if (is_array($q) && strpos($q['sql'], 'INSERT INTO pedido_productos') !== false) {
            if (strpos($q['sql'], 'VALUES (?, ?, ?, ?, ?), (?, ?, ?, ?, ?)') !== false) {
                $batchInsertFound = true;
                echo "Found batch insert for pedido products (multiple rows)\n";
            }
        }
    }

    if ($batchInsertFound) {
        echo "✅ Pedido optimization verified (Mocked)!\n";
    } else {
        echo "❌ Pedido optimization verification failed!\n";
    }
}

try {
    testOptimizations();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
