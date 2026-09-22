<?php

require_once __DIR__ . '/../vendor/autoload.php';

function runTest()
{
    echo "--- Test de Optimizacion Notificacion::getNoLeidas ---\n";

    // Create PDO SQLite in-memory DB
    $pdo = new PDO('sqlite::memory:');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // Create table schema
    $pdo->exec("
        CREATE TABLE notificaciones (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            id_sucursal INTEGER,
            id_usuario INTEGER,
            leida INTEGER DEFAULT 0,
            tipo TEXT,
            titulo TEXT,
            mensaje TEXT,
            fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP
        );
    ");

    // Insert 60 unread notifications for sucursal 1
    $stmt = $pdo->prepare("INSERT INTO notificaciones (id_sucursal, id_usuario, leida, mensaje) VALUES (1, NULL, 0, ?)");
    for ($i = 1; $i <= 60; $i++) {
        $stmt->execute(["Notification $i"]);
    }

    // Instantiate Database without constructor and set connection
    $dbRef = new ReflectionClass(\App\Core\Database::class);
    $dbInstance = $dbRef->newInstanceWithoutConstructor();

    $connProp = new ReflectionProperty(\App\Core\Database::class, 'connection');
    $connProp->setAccessible(true);
    $connProp->setValue($dbInstance, $pdo);

    // Instantiate Notificacion model
    $notifRef = new ReflectionClass(\App\Models\Notificacion::class);
    $notifModel = $notifRef->newInstanceWithoutConstructor();

    $dbProp = new ReflectionProperty(\App\Models\BaseModel::class, 'db');
    $dbProp->setAccessible(true);
    $dbProp->setValue($notifModel, $dbInstance);

    $tableProp = new ReflectionProperty(\App\Models\Notificacion::class, 'table');
    $tableProp->setAccessible(true);
    $tableProp->setValue($notifModel, 'notificaciones');

    // Test 1: Límite por defecto (50)
    echo "Test 1: Obtener notificaciones no leidas con limite por defecto (50)...\n";
    $notifs1 = $notifModel->getNoLeidas(1);
    assert(count($notifs1) === 50, "Debe retornar exactamente 50 registros acotados por el limite por defecto");
    echo "OK: Retorno exactamente 50 registros de los 60 creados.\n";

    // Test 2: Límite personalizado (10)
    echo "Test 2: Obtener notificaciones con limite personalizado de 10...\n";
    $notifs2 = $notifModel->getNoLeidas(1, null, 10);
    assert(count($notifs2) === 10, "Debe retornar exactamente 10 registros");
    echo "OK: Retorno exactamente 10 registros.\n";

    // Test 3: Filtro por usuario
    echo "Test 3: Insertar notificacion especifica de usuario y filtrar...\n";
    $pdo->exec("INSERT INTO notificaciones (id_sucursal, id_usuario, leida, mensaje) VALUES (1, 99, 0, 'User specific')");
    $notifsUser = $notifModel->getNoLeidas(1, 99, 5);
    assert(count($notifsUser) === 5, "Debe retornar 5 notificaciones");
    echo "OK: Filtro de usuario funciona con limite.\n";

    echo "--- Todos los tests pasaron exitosamente ---\n";
}

try {
    runTest();
} catch (Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
    exit(1);
}
