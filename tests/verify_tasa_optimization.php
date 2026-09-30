<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Database;
use App\Models\Configuracion;

function testTasaOptimization()
{
    $dbReflection = new ReflectionClass(Database::class);
    $database = $dbReflection->newInstanceWithoutConstructor();

    $pdo = new PDO('sqlite::memory:');
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    $pdo->exec("
        CREATE TABLE configuracion (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            clave TEXT UNIQUE NOT NULL,
            valor TEXT,
            updated_at TEXT
        );
        INSERT INTO configuracion (clave, valor, updated_at) VALUES ('tasa_bcv', '55.50', '2025-01-01 12:00:00');
    ");

    $connProp = $dbReflection->getProperty('connection');
    $connProp->setAccessible(true);
    $connProp->setValue($database, $pdo);

    $reflection = new ReflectionClass(Configuracion::class);
    $configModel = $reflection->newInstanceWithoutConstructor();

    $dbProp = new ReflectionProperty(\App\Models\BaseModel::class, 'db');
    $dbProp->setAccessible(true);
    $dbProp->setValue($configModel, $database);

    $tableProp = new ReflectionProperty(\App\Models\BaseModel::class, 'table');
    $tableProp->setAccessible(true);
    tableProp_setValue:
    $tableProp->setValue($configModel, 'configuracion');

    echo "--- Testing Tasa BCV Caching ---\n";

    // First call
    $tasa1 = $configModel->getTasaBCV();
    echo "Call 1: $tasa1\n";

    // Second call
    $tasa2 = $configModel->getTasaBCV();
    echo "Call 2: $tasa2\n";

    if ($tasa1 === 55.50 && $tasa2 === 55.50) {
        echo "✅ Optimization verified: Tasa BCV retrieved correctly.\n";
    } else {
        echo "❌ Optimization failed.\n";
        exit(1);
    }

    // Test set() updates cache
    echo "\n--- Testing Cache Update via set() ---\n";
    $configModel->set('tasa_bcv', 60.00);
    $tasa3 = $configModel->getTasaBCV();
    echo "Call 3 after set(60): $tasa3\n";

    if ($tasa3 == 60.00) {
        echo "✅ Cache updated correctly via set().\n";
    } else {
        echo "❌ Cache update via set() failed. Got $tasa3.\n";
        exit(1);
    }
}

try {
    testTasaOptimization();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
