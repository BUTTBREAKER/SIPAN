<?php

namespace Tests;

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Database;
use App\Models\Proveedor;
use PDO;

class VerifyProveedorOptimization
{
    public function run()
    {
        echo "=== Verifying Proveedor Optimizations ===\n\n";

        $this->verifyStaticCode();
        $this->verifyRuntimeExecution();

        echo "\n✅ All Proveedor optimizations successfully verified!\n";
    }

    private function verifyStaticCode()
    {
        echo "--- 1. Static Code Analysis ---\n";

        $filePath = __DIR__ . '/../app/Models/Proveedor.php';
        $content = file_get_contents($filePath);

        // Check addInsumos
        if (strpos($content, 'INSERT INTO proveedor_insumos (id_proveedor, id_insumo, precio, tiempo_entrega) VALUES " . implode(\', \', $placeholders)') !== false) {
            echo "✅ SUCCESS: addInsumos uses batched multi-row INSERT query.\n";
        } else {
            echo "❌ FAILURE: addInsumos batched INSERT query not found.\n";
            exit(1);
        }

        // Check getInsumosSinProveedor
        if (strpos($content, 'NOT EXISTS') !== false && strpos($content, 'SELECT 1 FROM proveedor_insumos pi WHERE pi.id_insumo = i.id') !== false) {
            echo "✅ SUCCESS: getInsumosSinProveedor uses SARGable NOT EXISTS subquery.\n";
        } else {
            echo "❌ FAILURE: NOT EXISTS subquery not found in getInsumosSinProveedor.\n";
            exit(1);
        }

        if (strpos($content, 'HAVING COUNT(pi.id) = 0') === false) {
            echo "✅ SUCCESS: Legacy HAVING clause successfully removed.\n";
        } else {
            echo "❌ FAILURE: HAVING clause still exists in getInsumosSinProveedor.\n";
            exit(1);
        }
    }

    private function verifyRuntimeExecution()
    {
        echo "\n--- 2. Mock SQLite Runtime Execution Analysis ---\n";

        // Create SQLite PDO connection
        $pdo = new PDO('sqlite::memory:');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        // Set up tables in memory
        $pdo->exec("CREATE TABLE proveedor_insumos (id_proveedor INTEGER, id_insumo INTEGER, precio REAL, tiempo_entrega INTEGER)");
        $pdo->exec("CREATE TABLE insumos (id INTEGER PRIMARY KEY, id_sucursal INTEGER, nombre TEXT, unidad_medida TEXT, stock_actual REAL, stock_minimo REAL)");

        // Insert mock insumos
        $pdo->exec("INSERT INTO insumos (id, id_sucursal, nombre, unidad_medida, stock_actual, stock_minimo) VALUES (1, 10, 'Harina', 'kg', 100, 10)");
        $pdo->exec("INSERT INTO insumos (id, id_sucursal, nombre, unidad_medida, stock_actual, stock_minimo) VALUES (2, 10, 'Azúcar', 'kg', 50, 5)");
        $pdo->exec("INSERT INTO insumos (id, id_sucursal, nombre, unidad_medida, stock_actual, stock_minimo) VALUES (3, 10, 'Sal', 'kg', 20, 2)");

        // Create Database instance via reflection
        $refDb = new \ReflectionClass(Database::class);
        $dbInstance = $refDb->newInstanceWithoutConstructor();

        $connProp = $refDb->getProperty('connection');
        $connProp->setAccessible(true);
        $connProp->setValue($dbInstance, $pdo);

        // Inject singleton
        $instProp = $refDb->getProperty('instance');
        $instProp->setAccessible(true);
        $instProp->setValue(null, $dbInstance);

        // Instantiate Proveedor model
        $proveedor = new Proveedor();

        // Test addInsumos
        $insumos = [
            ['id_insumo' => 1, 'precio' => 15.5, 'tiempo_entrega' => 3],
            ['id_insumo' => 2, 'precio' => 20.0, 'tiempo_entrega' => 5],
        ];

        $proveedor->addInsumos(100, $insumos);

        $stmt = $pdo->query("SELECT * FROM proveedor_insumos WHERE id_proveedor = 100");
        $insertedRows = $stmt->fetchAll();

        if (count($insertedRows) === 2) {
            echo "✅ SUCCESS: addInsumos inserted 2 rows correctly via batched multi-row INSERT.\n";
            echo "   Row 1: id_insumo={$insertedRows[0]['id_insumo']}, precio={$insertedRows[0]['precio']}\n";
            echo "   Row 2: id_insumo={$insertedRows[1]['id_insumo']}, precio={$insertedRows[1]['precio']}\n";
        } else {
            echo "❌ FAILURE: Expected 2 inserted rows in SQLite, found " . count($insertedRows) . "\n";
            exit(1);
        }

        // Test getInsumosSinProveedor
        // Currently insumo 3 (Sal) has no provider in proveedor_insumos table
        $sinProveedor = $proveedor->getInsumosSinProveedor(10);

        if (count($sinProveedor) === 1 && $sinProveedor[0]['nombre'] === 'Sal') {
            echo "✅ SUCCESS: getInsumosSinProveedor correctly identified 1 insumo without provider using NOT EXISTS.\n";
        } else {
            echo "❌ FAILURE: Expected 1 insumo ('Sal'), got: " . json_encode($sinProveedor) . "\n";
            exit(1);
        }
    }
}

$test = new VerifyProveedorOptimization();
$test->run();
