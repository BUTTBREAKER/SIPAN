<?php

namespace SIPAN\Models;

class Sucursal extends BaseModel
{
  protected $table = 'sucursales';

  public function getActivas()
  {
    $sql = "SELECT * FROM {$this->table} WHERE estado = 'activa' ORDER BY nombre";
    return $this->db->fetchAll($sql);
  }

  public function getNombre($id)
  {
    $sql = "SELECT nombre FROM {$this->table} WHERE id = ?";
    $result = $this->db->fetchOne($sql, [$id]);
    return $result['nombre'] ?? '';
  }

  public function findByClave($clave)
  {
    $sql = "SELECT * FROM {$this->table} WHERE clave_sucursal = ? AND estado = 'activa' LIMIT 1";
    return $this->db->fetchOne($sql, [$clave]);
  }
}
