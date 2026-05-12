<?php

namespace App\Models;

class SugerenciaCompra extends BaseModel
{
    protected $table = 'sugerencias_compra';

    public function generar($sucursal_id)
    {
        $sql = "CALL sp_generar_sugerencias_compra(?)";
        return $this->db->fetchOne($sql, [$sucursal_id]);
    }

    public function getWithDetails($sucursal_id, $estado = null)
    {
        // Bolt Optimization: Removed redundant LEFT JOIN on productos.
        // The current schema and 'sp_generar_sugerencias_compra' procedure only support insumos.
        // Using INNER JOIN on insumos since id_insumo is mandatory and simplified the SELECT list.
        $sql = "SELECT 
                    sc.*,
                    i.nombre as item_nombre,
                    i.stock_actual,
                    i.stock_minimo,
                    'insumo' as tipo,
                    sc.id_insumo as id_item,
                    i.unidad_medida,
                    i.precio_unitario
                FROM {$this->table} sc
                INNER JOIN insumos i ON sc.id_insumo = i.id
                WHERE sc.id_sucursal = ?";

        $params = [$sucursal_id];

        if ($estado) {
            $sql .= " AND sc.estado = ?";
            $params[] = $estado;
        }

        $sql .= " ORDER BY 
                    FIELD(sc.prioridad, 'alta', 'media', 'baja'),
                    sc.fecha_sugerencia DESC";

        return $this->db->fetchAll($sql, $params);
    }

    public function cambiarEstado($id, $estado)
    {
        $sql = "UPDATE {$this->table} SET estado = ?, fecha_actualizacion = NOW() WHERE id = ?";
        return $this->db->execute($sql, [$estado, $id]);
    }

    public function aprobar($id)
    {
        return $this->cambiarEstado($id, 'aprobada');
    }

    public function rechazar($id)
    {
        return $this->cambiarEstado($id, 'rechazada');
    }

    public function completar($id)
    {
        return $this->cambiarEstado($id, 'completada');
    }

    public function getPendientes($sucursal_id)
    {
        return $this->getWithDetails($sucursal_id, 'pendiente');
    }
}
