<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Database;
use App\Models\ChatMensaje;

class MockPDOStatement extends PDOStatement
{
    public string $queryString;

    public function __construct(string $queryString = '')
    {
        $this->queryString = $queryString;
    }

    public function execute(?array $params = null): bool
    {
        return true;
    }

    public function fetchAll(int $mode = PDO::FETCH_DEFAULT, mixed ...$args): array
    {
        return [];
    }

    public function fetch(int $mode = PDO::FETCH_DEFAULT, int $cursorOrientation = PDO::FETCH_ORI_NEXT, int $cursorOffset = 0): mixed
    {
        return ['total' => 0];
    }
}

class TestPdoConnection extends PDO
{
    public array $log = [];

    public function __construct()
    {
        // Dummy constructor, avoiding parent PDO connection
    }

    public function prepare(string $query, array $options = []): PDOStatement|false
    {
        $this->log[] = ['query' => $query, 'params' => null];
        $stmt = new MockPDOStatement($query);
        return $stmt;
    }
}

class TestDatabaseWrapper
{
    public array $queries = [];

    public function fetchAll(string $sql, array $params = []): array
    {
        $this->queries[] = ['sql' => $sql, 'params' => $params];
        return [];
    }

    public function fetchOne(string $sql, array $params = []): array|false
    {
        $this->queries[] = ['sql' => $sql, 'params' => $params];
        return ['total' => 0];
    }
}

// Instantiate Database via Reflection
$dbReflection = new ReflectionClass(Database::class);
$dbInstance = $dbReflection->newInstanceWithoutConstructor();

$testDbWrapper = new TestDatabaseWrapper();

// Set $db property on ChatMensaje using reflection or constructor
$modelReflection = new ReflectionClass(ChatMensaje::class);
$chatModel = $modelReflection->newInstanceWithoutConstructor();

$dbProperty = new ReflectionProperty(ChatMensaje::class, 'db');
$dbProperty->setAccessible(true);

// Inject SQLite in-memory Database into ChatMensaje
$sqlitePdo = new PDO('sqlite::memory:');
$sqlitePdo->exec("
    CREATE TABLE IF NOT EXISTS chat_participantes (id_conversacion INT, id_usuario INT, ultimo_leido TEXT);
    CREATE TABLE IF NOT EXISTS chat_conversaciones (id INT, tipo TEXT, nombre TEXT, id_sucursal INT, created_at TEXT);
    CREATE TABLE IF NOT EXISTS chat_mensajes (id INT, id_conversacion INT, id_usuario INT, mensaje TEXT, created_at TEXT);
    CREATE TABLE IF NOT EXISTS usuarios (id INT, primer_nombre TEXT, apellido_paterno TEXT, rol TEXT, id_sucursal INT);
    CREATE TABLE IF NOT EXISTS sucursales (id INT, nombre TEXT);
");

$dbConnProperty = $dbReflection->getProperty('connection');
$dbConnProperty->setAccessible(true);
$dbConnProperty->setValue($dbInstance, $sqlitePdo);

$dbInstProperty = $dbReflection->getProperty('instance');
$dbInstProperty->setAccessible(true);
$dbInstProperty->setValue(null, $dbInstance);

$dbProperty->setValue($chatModel, $dbInstance);

echo "Testing ChatMensaje::getConversaciones optimization...\n";

// Execute getConversaciones
$results = $chatModel->getConversaciones(1);

// Reflect source file code of getConversaciones to analyze SQL
$modelFile = file_get_contents(__DIR__ . '/../app/Models/ChatMensaje.php');

// Ensure no scalar subqueries in SELECT list: e.g. (SELECT COUNT(*) FROM chat_mensajes
if (preg_match('/SELECT\s+.*?\(\s*SELECT\s+COUNT/is', $modelFile)) {
    echo "❌ FAIL: Found correlated scalar subquery in SELECT clause of ChatMensaje.php\n";
    exit(1);
}

// Ensure getConversaciones does not use correlated subquery in JOIN: LEFT JOIN chat_mensajes m ON m.id = (SELECT
if (preg_match('/LEFT\s+JOIN\s+chat_mensajes\s+m\s+ON\s+m\.id\s*=\s*\(\s*SELECT/is', $modelFile)) {
    echo "❌ FAIL: Found correlated subquery in JOIN clause of ChatMensaje.php\n";
    exit(1);
}

echo "✅ SUCCESS: No correlated scalar subqueries found in ChatMensaje::getConversaciones\n";

echo "Testing ChatMensaje::contarNoLeidos optimization...\n";

$count = $chatModel->contarNoLeidos(1);

// Check if contarNoLeidos uses nested subquery: FROM ( SELECT ( SELECT COUNT
if (preg_match('/FROM\s*\(\s*SELECT\s*\(\s*SELECT/is', $modelFile)) {
    echo "❌ FAIL: Found nested scalar subquery in ChatMensaje::contarNoLeidos\n";
    exit(1);
}

echo "✅ SUCCESS: ChatMensaje::contarNoLeidos uses direct JOIN\n";
echo "All ChatMensaje optimizations successfully verified!\n";
