<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Database;
use App\Models\Caja;
use App\Models\Sucursal;

class LoggingPDOStatement extends PDOStatement
{
    public static int $executedCount = 0;

    protected function __construct()
    {
    }

    public function execute(?array $params = null): bool
    {
        self::$executedCount++;
        return parent::execute($params);
    }
}

function setupDatabaseMock()
{
    $db = (new ReflectionClass(Database::class))->newInstanceWithoutConstructor();

    $pdo = new PDO('sqlite::memory:');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->setAttribute(PDO::ATTR_STATEMENT_CLASS, [LoggingPDOStatement::class, []]);

    // Create tables
    $pdo->exec("CREATE TABLE cajas (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        id_sucursal INTEGER,
        estado TEXT,
        monto_apertura REAL,
        id_usuario_apertura INTEGER
    )");

    $pdo->exec("CREATE TABLE sucursales (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        nombre TEXT,
        estado TEXT
    )");

    // Insert dummy data
    $pdo->exec("INSERT INTO cajas (id_sucursal, estado, monto_apertura, id_usuario_apertura) VALUES (1, 'abierta', 100.0, 1)");
    $pdo->exec("INSERT INTO sucursales (nombre, estado) VALUES ('Sucursal Central', 'activa'), ('Sucursal Norte', 'activa')");

    $refConn = new ReflectionProperty(Database::class, 'connection');
    $refConn->setAccessible(true);
    $refConn->setValue($db, $pdo);

    $refInst = new ReflectionProperty(Database::class, 'instance');
    $refInst->setAccessible(true);
    $refInst->setValue(null, $db);

    return $db;
}

setupDatabaseMock();

$cajaModel = new Caja();
$sucursalModel = new Sucursal();

echo "--- Testing Request-Level Caching for Caja::getActiva and Sucursal::getActivas ---\n";

LoggingPDOStatement::$executedCount = 0;

// Call getActiva 3 times
$res1 = $cajaModel->getActiva(1);
$res2 = $cajaModel->getActiva(1);
$res3 = $cajaModel->getActiva(1);

echo "Caja::getActiva called 3 times. PDO Statement Executions: " . LoggingPDOStatement::$executedCount . "\n";

if (LoggingPDOStatement::$executedCount === 1) {
    echo "✅ SUCCESS: Caja::getActiva request-level caching verified!\n";
} else {
    echo "❌ FAILURE: Expected 1 PDO execution for Caja::getActiva, got " . LoggingPDOStatement::$executedCount . "\n";
}

LoggingPDOStatement::$executedCount = 0;

// Call getActivas 3 times
$s1 = $sucursalModel->getActivas();
$s2 = $sucursalModel->getActivas();
$s3 = $sucursalModel->getActivas();

echo "Sucursal::getActivas called 3 times. PDO Statement Executions: " . LoggingPDOStatement::$executedCount . "\n";

if (LoggingPDOStatement::$executedCount === 1) {
    echo "✅ SUCCESS: Sucursal::getActivas request-level caching verified!\n";
} else {
    echo "❌ FAILURE: Expected 1 PDO execution for Sucursal::getActivas, got " . LoggingPDOStatement::$executedCount . "\n";
}
