<?php

declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOException;
use PDOStatement;

use function App\getenv;

final class Database
{
    private static ?self $instance = null;
    private PDO $connection;

    /** @throws PDOException */
    public function __construct()
    {
        // Use Environment helper to get config
        $driver = getenv('db_driver');
        $host = getenv('db_host');
        $name = getenv('db_name');
        $user = (string) getenv('db_user');
        $pass = (string) getenv('db_pass');

        $dsn = $driver === 'mysql'
            ? "mysql:host=$host;dbname=$name;charset=utf8mb4"
            : "sqlite:$name";

        $this->connection = new PDO($dsn, $user, $pass, [
            PDO::ATTR_CASE => PDO::CASE_LOWER,
            PDO::ATTR_ORACLE_NULLS => PDO::NULL_EMPTY_STRING,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    }

    /** @throws PDOException */
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function getConnection(): PDO
    {
        return $this->connection;
    }

    /**
     * @param array<null|scalar> $params
     * @throws PDOException
     */
    private function query(string $sql, array $params = []): PDOStatement
    {
        $stmt = $this->connection->prepare($sql);
        $stmt->execute($params);

        return $stmt;
    }

    /**
     * @param array<null|scalar> $params
     * @return list<array<string, null|scalar|resource>>
     * @throws PDOException
     */
    public function fetchAll(string $sql, array $params = []): array
    {
        return $this->query($sql, $params)->fetchAll();
    }

    /**
     * @param array<null|scalar> $params
     * @return false|array<string, null|scalar|resource>
     * @throws PDOException
     */
    public function fetchOne(string $sql, array $params = []): false|array
    {
        return $this->query($sql, $params)->fetch();
    }

    /**
     * @param array<null|scalar> $params
     * @throws PDOException
     */
    public function execute(string $sql, array $params = []): int
    {
        return $this->query($sql, $params)->rowCount();
    }

    /** @throws PDOException */
    public function lastInsertId(): string|false
    {
        return $this->connection->lastInsertId();
    }

    public function beginTransaction(): bool
    {
        return $this->connection->beginTransaction();
    }

    public function commit(): bool
    {
        return $this->connection->commit();
    }

    public function rollback(): bool
    {
        return $this->connection->rollBack();
    }
}
