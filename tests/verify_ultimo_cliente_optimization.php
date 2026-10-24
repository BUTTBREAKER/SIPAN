<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Models\Receta;
use App\Models\Producto;

function runVerification() {
    echo "=== Running Ultimo Cliente Batch Optimization Verification ===\n";

    $pdo = new \PDO('sqlite::memory:');
    $pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
    $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

    // Create schema
    $pdo->exec("
        CREATE TABLE productos (
            id INTEGER PRIMARY KEY,
            nombre TEXT,
            precio_costo REAL
        );
        CREATE TABLE recetas (
            id INTEGER PRIMARY KEY,
            id_producto INTEGER,
            rendimiento REAL
        );
        CREATE TABLE receta_insumos (
            id INTEGER PRIMARY KEY,
            id_receta INTEGER,
            id_insumo INTEGER,
            cantidad REAL,
            unidad_medida TEXT
        );
        CREATE TABLE insumos (
            id INTEGER PRIMARY KEY,
            nombre TEXT,
            unidad_medida TEXT,
            precio_unitario REAL
        );
    ");

    // Insert sample data
    $pdo->exec("
        INSERT INTO productos (id, nombre, precio_costo) VALUES (10, 'Pan Canilla', 0.40);
        INSERT INTO productos (id, nombre, precio_costo) VALUES (20, 'Croissant', 0.80);
        INSERT INTO productos (id, nombre, precio_costo) VALUES (30, 'Jugo Natural', 0.50);

        INSERT INTO recetas (id, id_producto, rendimiento) VALUES (100, 10, 10.0);
        INSERT INTO recetas (id, id_producto, rendimiento) VALUES (101, 20, 5.0);

        INSERT INTO insumos (id, nombre, unidad_medida, precio_unitario) VALUES (1, 'Harina de Trigo', 'kg', 1.50);
        INSERT INTO insumos (id, nombre, unidad_medida, precio_unitario) VALUES (2, 'Azúcar', 'kg', 2.00);
        INSERT INTO insumos (id, nombre, unidad_medida, precio_unitario) VALUES (3, 'Mantequilla', 'kg', 4.00);

        INSERT INTO receta_insumos (id, id_receta, id_insumo, cantidad, unidad_medida) VALUES (1, 100, 1, 2.5, 'kg');
        INSERT INTO receta_insumos (id, id_receta, id_insumo, cantidad, unidad_medida) VALUES (2, 100, 2, 0.5, 'kg');
        INSERT INTO receta_insumos (id, id_receta, id_insumo, cantidad, unidad_medida) VALUES (3, 101, 3, 1.0, 'kg');
    ");

    // Set mock connection into Database singleton using Reflection
    $dbReflection = new \ReflectionClass(\App\Core\Database::class);
    $dbInstance = $dbReflection->newInstanceWithoutConstructor();

    $connProp = $dbReflection->getProperty('connection');
    $connProp->setAccessible(true);
    $connProp->setValue($dbInstance, $pdo);

    $instProp = $dbReflection->getProperty('instance');
    $instProp->setAccessible(true);
    $instProp->setValue(null, $dbInstance);

    // Test 1: Test Receta::getInsumosPorProductos batch fetch
    $recetaModel = new Receta();
    $insumosPorProductos = $recetaModel->getInsumosPorProductos([10, 20, 30]);

    assert(count($insumosPorProductos) === 2, "Should return recipe data for 2 products with recipes");
    assert(isset($insumosPorProductos[10]), "Product 10 should be present in results");
    assert(count($insumosPorProductos[10]['insumos']) === 2, "Product 10 should have 2 insumos");
    assert(isset($insumosPorProductos[20]), "Product 20 should be present in results");
    assert(count($insumosPorProductos[20]['insumos']) === 1, "Product 20 should have 1 insumo");
    echo "✓ Test 1 Passed: Receta::getInsumosPorProductos returns properly structured batch data\n";

    // Test 2: Test Producto::getByIds batch fetch
    $productoModel = new Producto();
    $productosMap = $productoModel->getByIds([10, 20, 30]);

    assert(count($productosMap) === 3, "Should return 3 products");
    assert((float)$productosMap[10]['precio_costo'] === 0.40, "Product 10 cost should be 0.40");
    assert((float)$productosMap[30]['precio_costo'] === 0.50, "Product 30 cost should be 0.50");
    echo "✓ Test 2 Passed: Producto::getByIds returns array indexed by product ID\n";

    // Test 3: Simulate logic of PedidosController::ultimoCliente with batching
    $productosPedido = [
        ['id_producto' => 10, 'cantidad' => 20, 'producto_nombre' => 'Pan Canilla'],
        ['id_producto' => 20, 'cantidad' => 10, 'producto_nombre' => 'Croissant'],
        ['id_producto' => 30, 'cantidad' => 5,  'producto_nombre' => 'Jugo Natural']
    ];

    $productIds = array_unique(array_filter(array_map('intval', array_column($productosPedido, 'id_producto'))));
    $recetasMap = $recetaModel->getInsumosPorProductos($productIds);
    $productosMap = $productoModel->getByIds($productIds);

    $gasto_insumos = 0;
    $detallesInsumos = [];

    foreach ($productosPedido as $prod) {
        $pId = (int)$prod['id_producto'];
        $pQty = floatval($prod['cantidad']);

        if (isset($recetasMap[$pId])) {
            $recetaInfo = $recetasMap[$pId];
            $rendimiento = floatval($recetaInfo['receta']['rendimiento'] ?? 1) ?: 1;
            $insumosReceta = $recetaInfo['insumos'];
            $costoBatch = 0;

            foreach ($insumosReceta as $ir) {
                $cant = floatval($ir['cantidad']);
                $costoUnitInsumo = floatval($ir['precio_unitario'] ?? $ir['costo_unitario'] ?? 0);
                $sub = $cant * $costoUnitInsumo;
                $costoBatch += $sub;

                $nomInsumo = $ir['nombre'];
                if (!isset($detallesInsumos[$nomInsumo])) {
                    $detallesInsumos[$nomInsumo] = [
                        'nombre' => $nomInsumo,
                        'cantidad' => 0,
                        'unidad' => $ir['unidad_insumo'] ?? ($ir['unidad_medida'] ?? 'kg'),
                        'costo' => 0
                    ];
                }
                $cantInvertida = ($cant / $rendimiento) * $pQty;
                $detallesInsumos[$nomInsumo]['cantidad'] += $cantInvertida;
                $detallesInsumos[$nomInsumo]['costo'] += $cantInvertida * $costoUnitInsumo;
            }
            $costoInsumosProducto = ($costoBatch / $rendimiento) * $pQty;
        } else {
            $pInfo = $productosMap[$pId] ?? [];
            $costoInsumosProducto = floatval($pInfo['precio_costo'] ?? 0) * $pQty;
        }

        $gasto_insumos += $costoInsumosProducto;
    }

    // Expected calculations:
    // Product 10 (20 units): Recipe yield 10. Batch cost = 2.5*1.5 + 0.5*2.0 = 3.75 + 1.0 = 4.75. Cost per unit = 0.475. For 20 units = 9.50.
    // Product 20 (10 units): Recipe yield 5. Batch cost = 1.0*4.0 = 4.00. Cost per unit = 0.80. For 10 units = 8.00.
    // Product 30 (5 units): No recipe. Fallback precio_costo = 0.50 * 5 = 2.50.
    // Total gasto_insumos = 9.50 + 8.00 + 2.50 = 20.00.
    assert(abs($gasto_insumos - 20.00) < 0.001, "Expected total ingredient cost 20.00, got: " . $gasto_insumos);
    assert(isset($detallesInsumos['Harina de Trigo']), "Harina de Trigo should be in detallesInsumos");
    assert(abs($detallesInsumos['Harina de Trigo']['cantidad'] - 5.0) < 0.001, "Harina de trigo quantity should be 5.0 kg");
    echo "✓ Test 3 Passed: Batch calculations match expected values ($20.00 total cost)\n";

    echo "\nALL VERIFICATION TESTS PASSED SUCCESSFULLY!\n";
}

runVerification();
