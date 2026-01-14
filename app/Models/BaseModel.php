<?php

namespace App\Models;

use App\Core\Database;

class BaseModel
{
    protected $db;
    protected $table;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function all($sucursal_id = null)
    {
        $sql = "SELECT * FROM {$this->table}";
        $params = [];

        if ($sucursal_id !== null && $this->hasColumn('id_sucursal')) {
            $sql .= " WHERE id_sucursal = ?";
            $params[] = $sucursal_id;
        }

        $sql .= " ORDER BY id DESC";

        return $this->db->fetchAll($sql, $params);
    }

    public function find($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = ?";
        return $this->db->fetchOne($sql, [$id]);
    }

    public function create($data)
    {
        $columns = array_keys($data);
        $placeholders = array_fill(0, count($columns), '?');

        $sql = "INSERT INTO {$this->table} (" . implode(', ', $columns) . ") 
                VALUES (" . implode(', ', $placeholders) . ")";

        $this->db->execute($sql, array_values($data));
        return $this->db->lastInsertId();
    }

    public function update($id, $data)
    {
        $columns = array_keys($data);
        $set = implode(' = ?, ', $columns) . ' = ?';

        $sql = "UPDATE {$this->table} SET {$set} WHERE id = ?";

        $params = array_values($data);
        $params[] = $id;

        return $this->db->execute($sql, $params);
    }

    public function delete($id)
    {
        $sql = "DELETE FROM {$this->table} WHERE id = ?";
        return $this->db->execute($sql, [$id]);
    }

    public function paginate($page = 1, $perPage = 20, $sucursal_id = null)
    {
        $offset = ($page - 1) * $perPage;

        $sql = "SELECT * FROM {$this->table}";
        $params = [];

        if ($sucursal_id !== null && $this->hasColumn('id_sucursal')) {
            $sql .= " WHERE id_sucursal = ?";
            $params[] = $sucursal_id;
        }

        $sql .= " ORDER BY id DESC LIMIT " . (int)$perPage . " OFFSET " . (int)$offset;

        return $this->db->fetchAll($sql, $params);
    }

    public function count($sucursal_id = null)
    {
        $sql = "SELECT COUNT(*) as total FROM {$this->table}";
        $params = [];

        if ($sucursal_id !== null && $this->hasColumn('id_sucursal')) {
            $sql .= " WHERE id_sucursal = ?";
            $params[] = $sucursal_id;
        }

        $result = $this->db->fetchOne($sql, $params);
        return $result['total'] ?? 0;
    }

    protected function hasColumn($column)
    {
    // Escapa y cita el nombre de la columna de forma segura
        $quoted_column = $this->db->getConnection()->quote($column);

        $sql = "SHOW COLUMNS FROM {$this->table} LIKE $quoted_column";

    // Ejecuta sin parámetros (no necesita prepare con ?)
        $result = $this->db->fetchOne($sql);

        return $result !== false;
    }
}
