<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDOException;

abstract class BaseModel
{
    protected Database $db;
    protected string $table;

    /** @var array<string, list<string>> */
    protected static array $columnCache = [];

    final public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * @return list<array<string, null|scalar|resource>>
     * @throws PDOException
     */
    final public function all(?int $sucursal_id = null): array
    {
        $sql = "SELECT * FROM $this->table";
        $params = [];

        if ($sucursal_id !== null && $this->hasColumn('id_sucursal')) {
            $sql .= ' WHERE id_sucursal = ?';
            $params[] = $sucursal_id;
        }

        $sql .= ' ORDER BY id DESC';

        return $this->db->fetchAll($sql, $params);
    }

    /**
     * @return false|array<string, null|scalar|resource>
     * @throws PDOException
     */
    final public function find(int $id): false|array
    {
        $sql = "SELECT * FROM $this->table WHERE id = ?";

        return $this->db->fetchOne($sql, [$id]);
    }

    /**
     * @param array<string, null|scalar> $data
     * @throws PDOException
     */
    final public function create(array $data): string|false
    {
        $columns = array_keys($data);
        $placeholders = array_fill(0, count($columns), '?');

        $sql = "
            INSERT INTO $this->table (" . implode(', ', $columns) . ")
            VALUES (" . implode(', ', $placeholders) . ")
        ";

        $this->db->execute($sql, array_values($data));

        return $this->db->lastInsertId();
    }

    /**
     * @param array<string, null|scalar> $data
     * @throws PDOException
     */
    final public function update(int $id, array $data): int
    {
        $columns = array_keys($data);
        $set = implode(' = ?, ', $columns) . ' = ?';
        $sql = "UPDATE $this->table SET $set WHERE id = ?";
        $params = array_values($data);
        $params[] = $id;

        return $this->db->execute($sql, $params);
    }

    /** @throws PDOException */
    final public function delete(int $id): int
    {
        $sql = "DELETE FROM $this->table WHERE id = ?";

        return $this->db->execute($sql, [$id]);
    }

    /**
     * Verifica si una columna existe en la tabla del modelo.
     * Optimización Bolt: Cachea las columnas de la tabla para evitar consultas redundantes.
     * @throws PDOException
     */
    private function hasColumn(string $column): bool
    {
        if (!isset(self::$columnCache[$this->table])) {
            $sql = "SHOW COLUMNS FROM $this->table";
            $result = $this->db->fetchAll($sql);

            // Normalizar a minúsculas para coincidencia insensible a mayúsculas/minúsculas
            self::$columnCache[$this->table] = array_map('strtolower', array_column($result, 'Field'));
        }

        return in_array(strtolower($column), self::$columnCache[$this->table]);
    }
}
