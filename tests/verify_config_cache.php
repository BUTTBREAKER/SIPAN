<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../app/Models/BaseModel.php';
require_once __DIR__ . '/../app/Models/Configuracion.php';

use App\Core\Database;

function createMockConfiguracionDatabase(): Database
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
        INSERT INTO configuracion (clave, valor) VALUES ('sitio_nombre', 'SIPAN Test');
    ");

    $connProp = $dbReflection->getProperty('connection');
    $connProp->setAccessible(true);
    $connProp->setValue($database, $pdo);

    $instanceProp = $dbReflection->getProperty('instance');
    $instanceProp->setAccessible(true);
    $instanceProp->setValue(null, $database);

    return $database;
}

function runTest()
{
    echo "--- Iniciando Test de Cache de Configuracion Refacturado ---\n";

    $database = createMockConfiguracionDatabase();
    $config = new \App\Models\Configuracion();

    // Test 1: Primera llamada a get()
    echo "Test 1: Primera llamada a get('sitio_nombre')...\n";
    $val1 = $config->get('sitio_nombre');
    assert($val1 === 'SIPAN Test');
    echo "OK: Valor recuperado.\n";

    // Test 2: Segunda llamada a get() (debe usar cache)
    echo "Test 2: Segunda llamada a get('sitio_nombre')...\n";
    $val2 = $config->get('sitio_nombre');
    assert($val2 === 'SIPAN Test');
    echo "OK: Valor recuperado de cache.\n";

    // Test 3: getTasaBCV() primera llamada
    echo "Test 3: getTasaBCV()...\n";
    $tasa1 = $config->getTasaBCV();
    assert($tasa1 === 55.50);
    echo "OK: getTasaBCV() devolvió tasa correcta.\n";

    // Test 4: Segunda llamada a getTasaBCV() (debe usar cache específico)
    echo "Test 4: Segunda llamada a getTasaBCV()...\n";
    $tasa2 = $config->getTasaBCV();
    assert($tasa2 === 55.50);
    echo "OK: Valor recuperado de cache específico de tasa.\n";

    // Test 5: set() para una clave nueva
    echo "Test 5: set() para una clave nueva...\n";
    $config->set('nueva_clave', 'valor_nuevo');
    assert($config->get('nueva_clave') === 'valor_nuevo');
    echo "OK: Clave nueva insertada correctamente.\n";

    // Test 6: set() para una clave existente
    echo "Test 6: set() para una clave existente...\n";
    $config->set('sitio_nombre', 'Nuevo SIPAN');
    assert($config->get('sitio_nombre') === 'Nuevo SIPAN');
    echo "OK: Clave existente actualizada correctamente.\n";

    echo "--- Todos los tests pasaron exitosamente ---\n";
}

try {
    runTest();
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
    exit(1);
}
