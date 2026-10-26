<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Models\Producto;
use App\Models\Receta;
use App\Core\Database;

function verifyUltimoClienteBatchMethods()
{
    echo "--- Testing Batch Methods for Producto and Receta with In-Memory SQLite ---\n";

    // Setup SQLite In-Memory Database
    $pdo = new PDO('sqlite::memory:');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    $pdo->exec("CREATE TABLE productos (id INTEGER PRIMARY KEY, nombre TEXT, precio_costo REAL)");
    $pdo->exec("CREATE TABLE recetas (id INTEGER PRIMARY KEY, id_producto INTEGER, rendimiento REAL)");
    $pdo->exec("CREATE TABLE insumos (id INTEGER PRIMARY KEY, nombre TEXT, unidad_medida TEXT, precio_unitario REAL, stock_actual REAL)");
    $pdo->exec("CREATE TABLE receta_insumos (id INTEGER PRIMARY KEY, id_receta INTEGER, id_insumo INTEGER, cantidad REAL, unidad_medida TEXT)");

    $pdo->exec("INSERT INTO productos VALUES (10, 'Pan Canilla', 0.50), (20, 'Pan Sobao', 0.80)");
    $pdo->exec("INSERT INTO recetas VALUES (101, 10, 10), (102, 20, 5)");
    $pdo->exec("INSERT INTO insumos VALUES (1, 'Harina', 'kg', 1.50, 100.0), (2, 'Azúcar', 'kg', 2.00, 50.0)");
    $pdo->exec("INSERT INTO receta_insumos VALUES (1, 101, 1, 2.0, 'kg'), (2, 102, 2, 0.5, 'kg')");

    // Instantiate Database instance via Reflection
    $refDb = new ReflectionClass(Database::class);
    $databaseInstance = $refDb->newInstanceWithoutConstructor();
    $connProp = $refDb->getProperty('connection');
    $connProp->setAccessible(true);
    $connProp->setValue($databaseInstance, $pdo);

    // Instantiate models via Reflection
    $refBase = new ReflectionClass(\App\Models\BaseModel::class);
    $dbProp = $refBase->getProperty('db');
    $dbProp->setAccessible(true);

    $productoModel = (new ReflectionClass(Producto::class))->newInstanceWithoutConstructor();
    $dbProp->setValue($productoModel, $databaseInstance);

    $recetaModel = (new ReflectionClass(Receta::class))->newInstanceWithoutConstructor();
    $dbProp->setValue($recetaModel, $databaseInstance);

    $pTable = (new ReflectionClass(Producto::class))->getProperty('table');
    $pTable->setAccessible(true);
    $pTable->setValue($productoModel, 'productos');

    $rTable = (new ReflectionClass(Receta::class))->getProperty('table');
    $rTable->setAccessible(true);
    $rTable->setValue($recetaModel, 'recetas');

    // 1. Test Producto::getByIds
    $pMap = $productoModel->getByIds([10, 20]);
    if (!isset($pMap[10]) || $pMap[10]['nombre'] !== 'Pan Canilla') {
        throw new \Exception("Producto::getByIds failed to index product 10 correctly");
    }
    if (!isset($pMap[20]) || $pMap[20]['nombre'] !== 'Pan Sobao') {
        throw new \Exception("Producto::getByIds failed to index product 20 correctly");
    }
    echo "✅ Producto::getByIds verified!\n";

    // 2. Test Receta::getByProductoIds
    $rMap = $recetaModel->getByProductoIds([10, 20]);
    if (!isset($rMap[10]) || (int)$rMap[10]['id'] !== 101) {
        throw new \Exception("Receta::getByProductoIds failed to index recipe for product 10");
    }
    if (!isset($rMap[20]) || (int)$rMap[20]['id'] !== 102) {
        throw new \Exception("Receta::getByProductoIds failed to index recipe for product 20");
    }
    echo "✅ Receta::getByProductoIds verified!\n";

    // 3. Test Receta::getInsumosPorRecetaIds
    $iMap = $recetaModel->getInsumosPorRecetaIds([101, 102]);
    if (!isset($iMap[101]) || count($iMap[101]) !== 1 || $iMap[101][0]['nombre'] !== 'Harina') {
        throw new \Exception("Receta::getInsumosPorRecetaIds failed to index insumos for recipe 101");
    }
    if (!isset($iMap[102]) || count($iMap[102]) !== 1 || $iMap[102][0]['nombre'] !== 'Azúcar') {
        throw new \Exception("Receta::getInsumosPorRecetaIds failed to index insumos for recipe 102");
    }
    echo "✅ Receta::getInsumosPorRecetaIds verified!\n";

    // Verify empty array handling
    if ($productoModel->getByIds([]) !== [] || $recetaModel->getByProductoIds([]) !== [] || $recetaModel->getInsumosPorRecetaIds([]) !== []) {
        throw new \Exception("Batch methods should return empty array when given empty input array");
    }
    echo "✅ Empty input edge case verified!\n";

    echo "\n🎉 All batch method tests passed successfully!\n";
}

try {
    verifyUltimoClienteBatchMethods();
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
