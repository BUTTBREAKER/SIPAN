<?php

namespace SIPAN\Models;

class Receta extends BaseModel {
    protected $table = 'recetas';

    public function createWithInsumos($id_producto, $rendimiento, $instrucciones, $sucursal_id, $insumos) {
    // Crear la receta
    $sql = "INSERT INTO recetas (id_producto, rendimiento, instrucciones, id_sucursal) VALUES (?, ?, ?, ?)";
    $this->db->execute($sql, [$id_producto, $rendimiento, $instrucciones, $sucursal_id]);
    $id_receta = $this->db->lastInsertId();

    // Agregar los insumos
    $sqlDetalle = "INSERT INTO receta_insumos (id_receta, id_insumo, cantidad) VALUES (?, ?, ?)";
    foreach ($insumos as $insumo) {
        $this->db->execute($sqlDetalle, [$id_receta, $insumo['id'], $insumo['cantidad']]);
    }

    return $id_receta;
}

    
    public function getByProducto($producto_id) {
        $sql = "SELECT * FROM {$this->table} WHERE id_producto = ?";
        return $this->db->fetchOne($sql, [$producto_id]);
    }
    
    public function getInsumos($receta_id) {
        $sql = "SELECT ri.*, i.nombre, i.unidad_medida as unidad_insumo, i.stock_actual, i.precio_unitario
                FROM receta_insumos ri
                INNER JOIN insumos i ON ri.id_insumo = i.id
                WHERE ri.id_receta = ?";
        return $this->db->fetchAll($sql, [$receta_id]);
    }
    
    public function addInsumo($receta_id, $insumo_id, $cantidad, $unidad_medida) {
        $sql = "INSERT INTO receta_insumos (id_receta, id_insumo, cantidad, unidad_medida) 
                VALUES (?, ?, ?, ?)";
        return $this->db->execute($sql, [$receta_id, $insumo_id, $cantidad, $unidad_medida]);
    }
    
    public function removeInsumo($receta_id, $insumo_id) {
        $sql = "DELETE FROM receta_insumos WHERE id_receta = ? AND id_insumo = ?";
        return $this->db->execute($sql, [$receta_id, $insumo_id]);
    }
    
    public function updateInsumo($receta_id, $insumo_id, $cantidad) {
        $sql = "UPDATE receta_insumos SET cantidad = ? WHERE id_receta = ? AND id_insumo = ?";
        return $this->db->execute($sql, [$cantidad, $receta_id, $insumo_id]);
    }
    
    public function calcularInsumos($producto_id, $cantidad) {
        $sql = "CALL sp_calcular_insumos_produccion(?, ?)";
        return $this->db->fetchAll($sql, [$producto_id, $cantidad]);
    }
    
    public function getWithDetails($sucursal_id) {
        $sql = "SELECT r.*, p.nombre as producto_nombre, 
                       COUNT(ri.id) as total_insumos
                FROM {$this->table} r
                INNER JOIN productos p ON r.id_producto = p.id
                LEFT JOIN receta_insumos ri ON r.id = ri.id_receta
                WHERE r.id_sucursal = ?
                GROUP BY r.id
                ORDER BY r.nombre";
        return $this->db->fetchAll($sql, [$sucursal_id]);
    }
    
    public function all($sucursal_id = null) {
        if ($sucursal_id === null) {
            $sql = "SELECT r.*, p.nombre as producto_nombre
                    FROM {$this->table} r
                    INNER JOIN productos p ON r.id_producto = p.id
                    ORDER BY r.nombre";
            return $this->db->fetchAll($sql);
        }
        
        $sql = "SELECT r.*, p.nombre as producto_nombre
                FROM {$this->table} r
                INNER JOIN productos p ON r.id_producto = p.id
                WHERE r.id_sucursal = ?
                ORDER BY r.nombre";
        return $this->db->fetchAll($sql, [$sucursal_id]);
    }
    
    public function getInsumosByReceta($receta_id) {
        $sql = "SELECT ri.*, i.nombre, i.unidad_medida, i.stock_actual, i.precio_unitario
                FROM receta_insumos ri
                INNER JOIN insumos i ON ri.id_insumo = i.id
                WHERE ri.id_receta = ?";
        return $this->db->fetchAll($sql, [$receta_id]);
    }
    
    public function findByProducto($producto_id) {
        $sql = "SELECT * FROM {$this->table} WHERE id_producto = ? LIMIT 1";
        return $this->db->fetchOne($sql, [$producto_id]);
    }
}

