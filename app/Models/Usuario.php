<?php

namespace SIPAN\Models;

class Usuario extends BaseModel
{
  protected $table = 'usuarios';

  public function findByEmail(string $correo)
  {
    $sql = "SELECT * FROM {$this->table} WHERE correo = ? LIMIT 1";

    return $this->db->fetchOne($sql, [$correo]);
  }

  public function authenticate($correo, $clave)
  {
    $user = $this->findByEmail($correo);

    if ($user && password_verify($clave, $user['clave'])) {
      return $user;
    }

    return false;
  }

  public function createUser($data)
  {
    // Hashear la contraseña
    if (isset($data['clave'])) {
      $data['clave'] = password_hash($data['clave'], PASSWORD_DEFAULT);
    }

    return $this->create($data);
  }

  public function updateUser($id, $data)
  {
    // Hashear la contraseña si se está actualizando
    if (isset($data['clave']) && !empty($data['clave'])) {
      $data['clave'] = password_hash($data['clave'], PASSWORD_DEFAULT);
    } else {
      unset($data['clave']);
    }

    return $this->update($id, $data);
  }

  public function getBySucursal($sucursal_id)
  {
    $sql = "SELECT * FROM {$this->table} WHERE id_sucursal = ? AND estado = 'activo' ORDER BY primer_nombre";
    return $this->db->fetchAll($sql, [$sucursal_id]);
  }

  public function findByDNI($dni)
  {
    $sql = "SELECT * FROM {$this->table} WHERE dni = ? LIMIT 1";
    return $this->db->fetchOne($sql, [$dni]);
  }
}
