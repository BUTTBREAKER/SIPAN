<?php

namespace App\Models;

class Usuario extends BaseModel
{
    protected $table = 'usuarios';

    public function findByEmail($correo)
    {
        $sql = "SELECT * FROM {$this->table} WHERE correo = ? LIMIT 1";
        return $this->db->fetchOne($sql, [$correo]);
    }

    public function findByDNI($dni)
    {
        $sql = "SELECT * FROM {$this->table} WHERE dni = ? LIMIT 1";
        return $this->db->fetchOne($sql, [$dni]);
    }

    public function findByTelefono($telefono)
    {
        $sql = "SELECT * FROM {$this->table} WHERE telefono = ? LIMIT 1";
        return $this->db->fetchOne($sql, [$telefono]);
    }

    public function authenticate($correo, $clave)
    {
        $user = $this->findByEmail($correo);

        if ($user && password_verify($clave, $user['clave'])) {
            return $user;
        }

        return false;
    }

    // ★ Registro desde la vista pública de registro (con hashing automático)
    public function register($data)
    {
        $sql = "INSERT INTO {$this->table}
        (primer_nombre, segundo_nombre, apellido_paterno, apellido_materno,
         dni, telefono, correo, clave, rol, estado, id_sucursal)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $this->db->execute($sql, [
            $data['primer_nombre'],
            $data['segundo_nombre'],
            $data['apellido_paterno'],
            $data['apellido_materno'],
            $data['dni'],
            $data['telefono'],
            $data['correo'],
            password_hash($data['clave'], PASSWORD_DEFAULT),
            $data['rol'],
            $data['estado'],
            $data['id_sucursal']
        ]);

        return $this->db->lastInsertId();
    }


    // Administración / Panel — tu versión actual
    public function createUser($data)
    {
        if (isset($data['clave'])) {
            $data['clave'] = password_hash($data['clave'], PASSWORD_DEFAULT);
        }

        return $this->create($data);
    }

    public function updateUser($id, $data)
    {
        if (isset($data['clave']) && !empty($data['clave'])) {
            $data['clave'] = password_hash($data['clave'], PASSWORD_DEFAULT);
        } else {
            unset($data['clave']);
        }

        return $this->update($id, $data);
    }

    public function find($id)
    {
        $sql = "SELECT *,
            CONCAT_WS(' ', primer_nombre, segundo_nombre, apellido_paterno, apellido_materno) as nombre
            FROM {$this->table}
            WHERE id = ?
            LIMIT 1";

        return $this->db->fetchOne($sql, [$id]);
    }

    public function getBySucursal($sucursal_id, $solo_activos = false)
    {
        $sql = "SELECT *,
            CONCAT_WS(' ', primer_nombre, segundo_nombre, apellido_paterno, apellido_materno) as nombre
            FROM {$this->table}
            WHERE id_sucursal = ?";

        if ($solo_activos) {
            $sql .= " AND estado = 'activo'";
        }

        $sql .= " ORDER BY primer_nombre";

        return $this->db->fetchAll($sql, [$sucursal_id]);
    }
}
