<?php

namespace SIPAN\Models;

class Produccion extends BaseModel
{
  protected $table = 'producciones';

  public function createWithInsumos($produccion_data, $insumos)
  {
    try {
      $this->db->beginTransaction();

      // Crear producción
      $produccion_id = $this->create($produccion_data);

      // Agregar insumos utilizados
      foreach ($insumos as $insumo) {
        $sql = "INSERT INTO produccion_insumos (id_produccion, id_insumo, cantidad_utilizada)
                        VALUES (?, ?, ?)";
        $this->db->execute($sql, [
          $produccion_id,
          $insumo['id_insumo'],
          $insumo['cantidad_utilizada']
        ]);
      }

      $this->db->commit();
      return $produccion_id;
    } catch (\Exception $e) {
      $this->db->rollback();
      throw $e;
    }
  }

  public function getInsumos($produccion_id)
  {
    $sql = "SELECT pi.*, i.nombre, i.unidad_medida
                FROM produccion_insumos pi
                INNER JOIN insumos i ON pi.id_insumo = i.id
                WHERE pi.id_produccion = ?";
    return $this->db->fetchAll($sql, [$produccion_id]);
  }

  public function getWithDetails($sucursal_id, $fecha_inicio = null, $fecha_fin = null)
  {
    $sql = "SELECT pr.*, p.nombre as producto_nombre,
                       u.primer_nombre, u.apellido_paterno
                FROM {$this->table} pr
                INNER JOIN productos p ON pr.id_producto = p.id
                INNER JOIN usuarios u ON pr.id_usuario = u.id
                WHERE pr.id_sucursal = ?";

    $params = [$sucursal_id];

    if ($fecha_inicio) {
      $sql .= " AND DATE(pr.fecha_produccion) >= ?";
      $params[] = $fecha_inicio;
    }

    if ($fecha_fin) {
      $sql .= " AND DATE(pr.fecha_produccion) <= ?";
      $params[] = $fecha_fin;
    }

    $sql .= " ORDER BY pr.fecha_produccion DESC";

    return $this->db->fetchAll($sql, $params);
  }
}
