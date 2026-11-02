<?php

// phpcs:disable PSR1.Files.SideEffects
// phpcs:disable PSR1.Classes.ClassDeclaration.MultipleClasses

declare(strict_types=1);

namespace Tests;

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Database;
use App\Models\ChatMensaje;
use PDO;
use PDOStatement;
use ReflectionClass;

echo "--- Verifying ChatMensaje Optimizations ---\n";

// Set up in-memory SQLite database
$pdo = new PDO('sqlite::memory:');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Create required schema
$pdo->exec("
    CREATE TABLE chat_conversaciones (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        tipo TEXT NOT NULL DEFAULT 'directa',
        nombre TEXT NULL,
        id_sucursal INTEGER NULL,
        created_at TEXT DEFAULT CURRENT_TIMESTAMP,
        updated_at TEXT DEFAULT CURRENT_TIMESTAMP
    );

    CREATE TABLE chat_participantes (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        id_conversacion INTEGER NOT NULL,
        id_usuario INTEGER NOT NULL,
        ultimo_leido TEXT NULL,
        joined_at TEXT DEFAULT CURRENT_TIMESTAMP
    );

    CREATE TABLE chat_mensajes (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        id_conversacion INTEGER NOT NULL,
        id_usuario INTEGER NOT NULL,
        mensaje TEXT NOT NULL,
        created_at TEXT DEFAULT CURRENT_TIMESTAMP
    );

    CREATE TABLE usuarios (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        primer_nombre TEXT NOT NULL,
        apellido_paterno TEXT NOT NULL,
        rol TEXT NOT NULL,
        id_sucursal INTEGER NULL,
        estado TEXT NOT NULL DEFAULT 'activo'
    );

    CREATE TABLE sucursales (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        nombre TEXT NOT NULL
    );
");

// Insert seed data
$pdo->exec("
    INSERT INTO sucursales (id, nombre) VALUES (1, 'Sucursal Central');
    INSERT INTO usuarios (id, primer_nombre, apellido_paterno, rol, id_sucursal, estado)
    VALUES (1, 'Alice', 'Smith', 'administrador', 1, 'activo'),
           (2, 'Bob', 'Jones', 'empleado', 1, 'activo');

    INSERT INTO chat_conversaciones (id, tipo, nombre, id_sucursal)
    VALUES (1, 'directa', NULL, 1);

    INSERT INTO chat_participantes (id_conversacion, id_usuario, ultimo_leido)
    VALUES (1, 1, '2026-01-01 00:00:00'),
           (1, 2, '2026-01-01 00:00:00');

    INSERT INTO chat_mensajes (id_conversacion, id_usuario, mensaje, created_at)
    VALUES (1, 2, 'Hola Alice!', '2026-01-02 10:00:00');
");

// Custom PDO subclass to log queries for inspection
class QueryLoggingPDO extends PDO
{
    /** @var list<array{sql: string, params: array<mixed>}> */
    public array $loggedQueries = [];

    public function __construct(private PDO $wrapped)
    {
    }

    #[\ReturnTypeWillChange]
    public function prepare(string $query, array $options = []): PDOStatement|false
    {
        $stmt = $this->wrapped->prepare($query, $options);
        return new LoggingStatement($stmt, $this, $query);
    }
}

class LoggingStatement extends PDOStatement
{
    public function __construct(
        private PDOStatement $wrappedStmt,
        private QueryLoggingPDO $parent,
        private string $sql
    ) {
    }

    #[\ReturnTypeWillChange]
    public function execute(?array $params = null): bool
    {
        $this->parent->loggedQueries[] = [
            'sql' => $this->sql,
            'params' => $params ?? [],
        ];
        return $this->wrappedStmt->execute($params);
    }

    #[\ReturnTypeWillChange]
    public function fetchAll(int $mode = PDO::FETCH_DEFAULT, mixed ...$args): array
    {
        return $this->wrappedStmt->fetchAll($mode, ...$args);
    }

    #[\ReturnTypeWillChange]
    public function fetch(
        int $mode = PDO::FETCH_DEFAULT,
        int $cursorOrientation = PDO::FETCH_ORI_NEXT,
        int $cursorOffset = 0
    ): mixed {
        return $this->wrappedStmt->fetch($mode, $cursorOrientation, $cursorOffset);
    }

    #[\ReturnTypeWillChange]
    public function rowCount(): int
    {
        return $this->wrappedStmt->rowCount();
    }
}

$loggingPdo = new QueryLoggingPDO($pdo);

// Inject SQLite logging PDO into Database singleton
$dbRef = new ReflectionClass(Database::class);
$dbInstance = $dbRef->newInstanceWithoutConstructor();

$connProp = $dbRef->getProperty('connection');
$connProp->setAccessible(true);
$connProp->setValue($dbInstance, $loggingPdo);

$instProp = $dbRef->getProperty('instance');
$instProp->setAccessible(true);
$instProp->setValue(null, $dbInstance);

$model = new ChatMensaje();

// 1. Verify getConversaciones SQL and execution
echo "Testing getConversaciones...\n";
$convs = $model->getConversaciones(1);
$lastQueryEntry = end($loggingPdo->loggedQueries);
$lastQuery = $lastQueryEntry['sql'];

// Check absence of correlated subqueries in SELECT list
if (preg_match('/SELECT\s+.*?\(\s*SELECT\s+COUNT/is', $lastQuery)) {
    echo "❌ FAILURE: Correlated scalar subquery detected in getConversaciones SELECT list.\n";
    echo "Query: $lastQuery\n";
    exit(1);
}

// Check absence of correlated subquery in JOIN
if (preg_match('/LEFT\s+JOIN.*?ON.*?=\s*\(\s*SELECT/is', $lastQuery)) {
    echo "❌ FAILURE: Correlated subquery in JOIN detected in getConversaciones.\n";
    echo "Query: $lastQuery\n";
    exit(1);
}

// Check presence of derived table joins
if (stripos($lastQuery, 'LEFT JOIN (') === false) {
    echo "❌ FAILURE: Derived table JOIN not found in getConversaciones.\n";
    echo "Query: $lastQuery\n";
    exit(1);
}

// Verify results
if (count($convs) !== 1 || (int) $convs[0]['no_leidos'] !== 1) {
    echo "❌ FAILURE: Unexpected results from getConversaciones. Got: " . json_encode($convs) . "\n";
    exit(1);
}
echo "✅ getConversaciones SQL and results verified!\n";

// 2. Verify contarNoLeidos SQL and execution
echo "Testing contarNoLeidos...\n";
$unreadCount = $model->contarNoLeidos(1);
$lastQueryEntry = end($loggingPdo->loggedQueries);
$lastQuery = $lastQueryEntry['sql'];

// Check absence of nested subquery pattern
if (stripos($lastQuery, 'FROM (') !== false || preg_match('/\(\s*SELECT/i', $lastQuery)) {
    echo "❌ FAILURE: Nested or scalar subquery detected in contarNoLeidos.\n";
    echo "Query: $lastQuery\n";
    exit(1);
}

// Check presence of direct INNER JOIN
$hasJoin = stripos($lastQuery, 'INNER JOIN chat_participantes') !== false
    || stripos($lastQuery, 'JOIN chat_participantes') !== false;
if (!$hasJoin) {
    echo "❌ FAILURE: Expected direct JOIN in contarNoLeidos.\n";
    echo "Query: $lastQuery\n";
    exit(1);
}

// Verify result
if ($unreadCount !== 1) {
    echo "❌ FAILURE: Expected 1 unread message, got $unreadCount\n";
    exit(1);
}
echo "✅ contarNoLeidos SQL and results verified!\n";

echo "🎉 All chat optimizations verified successfully!\n";
