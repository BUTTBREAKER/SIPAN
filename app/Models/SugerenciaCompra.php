<?php

namespace App\Models;

class SugerenciaCompra extends BaseModel {
    protected $table = 'sugerencias_compra';
    
    public function generar($sucursal_id) {
        $sql = "CALL sp_generar_sugerencias_compra(?)";
        return $this->db->fetchOne($sql, [$sucursal_id]);
    }
    
    public function getWithDetails($sucursal_id, $estado = null) {
        $sql = "SELECT 
                    sc.*,
                    COALESCE(i.nombre, p.nombre) as item_nombre,
                    COALESCE(i.stock_actual, p.stock_actual) as stock_actual,
                    COALESCE(i.stock_minimo, p.stock_minimo) as stock_minimo,
                    CASE 
                        WHEN sc.id_insumo IS NOT NULL THEN 'insumo'
                        WHEN sc.id_producto IS NOT NULL THEN 'producto'
                        ELSE 'desconocido'
                    END as tipo,
                    COALESCE(sc.id_insumo, sc.id_producto) as id_item,
                    i.unidad_medida,
                    COALESCE(i.precio_unitario, p.precio_actual) as precio_unitario,
                    p.imagen as producto_imagen
                FROM {$this->table} sc
                LEFT JOIN insumos i ON sc.id_insumo = i.id
                LEFT JOIN productos p ON sc.id_producto = p.id
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
    
    public function cambiarEstado($id, $estado) {
        $sql = "UPDATE {$this->table} SET estado = ?, fecha_actualizacion = NOW() WHERE id = ?";
        return $this->db->execute($sql, [$estado, $id]);
    }
    
    public function aprobar($id) {
        return $this->cambiarEstado($id, 'aprobada');
    }
    
    public function rechazar($id) {
        return $this->cambiarEstado($id, 'rechazada');
    }
    
    public function completar($id) {
        return $this->cambiarEstado($id, 'completada');
    }
    
    public function getPendientes($sucursal_id) {
        return $this->getWithDetails($sucursal_id, 'pendiente');
    }
}