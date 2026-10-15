<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Database;
use App\Models\SugerenciaCompra;

function verifyOptimization()
{
    $dbReflection = new ReflectionClass(Database::class);
    $database = $dbReflection->newInstanceWithoutConstructor();

    $pdo = new PDO('sqlite::memory:');
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->sqliteCreateFunction('FIELD', function ($val, ...$fields) {
        $idx = array_search($val, $fields);
        return $idx === false ? 9999 : $idx + 1;
    });

    $pdo->exec("
        CREATE TABLE insumos (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            nombre TEXT NOT NULL,
            stock_actual REAL DEFAULT 0,
            stock_minimo REAL DEFAULT 0,
            unidad_medida TEXT,
            precio_unitario REAL DEFAULT 0
        );
        CREATE TABLE sugerencias_compra (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            id_sucursal INTEGER NOT NULL,
            id_insumo INTEGER NOT NULL,
            prioridad TEXT,
            fecha_sugerencia TEXT,
            estado TEXT,
            razon TEXT
        );
        INSERT INTO insumos (id, nombre, stock_actual, stock_minimo, unidad_medida, precio_unitario)
        VALUES (1, 'Harina', 10, 20, 'kg', 1.5);
        INSERT INTO sugerencias_compra (id, id_sucursal, id_insumo, prioridad, fecha_sugerencia, estado, razon)
        VALUES (1, 1, 1, 'alta', '2023-01-01 10:00:00', 'pendiente', 'Stock bajo');
    ");

    $connProp = $dbReflection->getProperty('connection');
    $connProp->setAccessible(true);
    $connProp->setValue($database, $pdo);

    $reflection = new ReflectionClass(SugerenciaCompra::class);
    $model = $reflection->newInstanceWithoutConstructor();

    $dbProp = new ReflectionProperty(\App\Models\BaseModel::class, 'db');
    $dbProp->setAccessible(true);
    $dbProp->setValue($model, $database);

    $tableProp = new ReflectionProperty(\App\Models\BaseModel::class, 'table');
    $tableProp->setAccessible(true);
    $tableProp->setValue($model, 'sugerencias_compra');

    echo "Running SugerenciaCompra::getWithDetails()...\n";
    $results = $model->getWithDetails(1);

    // Verify method SQL does not contain LEFT JOIN productos
    $methodRef = new ReflectionMethod(SugerenciaCompra::class, 'getWithDetails');
    $filename = $methodRef->getFileName();
    $startLine = $methodRef->getStartLine();
    $endLine = $methodRef->getEndLine();
    $source = implode('', array_slice(file($filename), $startLine - 1, $endLine - $startLine + 1));

    if (strpos($source, 'LEFT JOIN productos') !== false) {
        throw new Exception("Optimization failed: LEFT JOIN productos still present in query.");
    }

    if (strpos($source, 'INNER JOIN insumos') === false) {
        throw new Exception("Optimization failed: INNER JOIN insumos missing from query.");
    }

    $requiredFields = ['item_nombre', 'tipo', 'stock_actual', 'stock_minimo', 'id_item', 'unidad_medida', 'precio_unitario', 'prioridad', 'fecha_sugerencia', 'estado'];
    foreach ($requiredFields as $field) {
        if (!isset($results[0][$field])) {
            throw new Exception("Functional regression: $field missing from results.");
        }
    }

    echo "✅ SugerenciaCompra::getWithDetails optimization verified successfully!\n";
}

try {
    verifyOptimization();
} catch (Exception $e) {
    echo "❌ Verification failed: " . $e->getMessage() . "\n";
    exit(1);
}
