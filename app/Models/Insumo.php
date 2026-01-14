<?php

namespace App\Models;

class Insumo extends BaseModel
{
    protected $table = 'insumos';

    // ✅ Nuevo método compatible con el controlador
    public function getAllBySucursal($sucursal_id)
    {
        $sql = "SELECT i.*, p.nombre as proveedor_nombre 
                FROM {$this->table} i
                LEFT JOIN providers p ON i.id_proveedor = p.id
                WHERE i.id_sucursal = ? 
                ORDER BY i.nombre";
        // Corregir nombre de tabla si es 'proveedores' en lugar de 'providers'
        // Asumiendo 'proveedores' por el SQL anterior
        $sql = "SELECT i.*, p.nombre as proveedor_nombre 
                FROM {$this->table} i
                LEFT JOIN proveedores p ON i.id_proveedor = p.id
                WHERE i.id_sucursal = ? 
                ORDER BY i.nombre";
        return $this->db->fetchAll($sql, [$sucursal_id]);
    }

    // ✅ Método general (sin sucursal)
    public function all($sucursal_id = null)
    {
        if ($sucursal_id) {
            return $this->getAllBySucursal($sucursal_id);
        }

        $sql = "SELECT i.*, p.nombre as proveedor_nombre 
                FROM {$this->table} i
                LEFT JOIN proveedores p ON i.id_proveedor = p.id
                ORDER BY i.nombre";
        return $this->db->fetchAll($sql);
    }

    public function getWithStockBajo($sucursal_id)
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE id_sucursal = ? 
                AND stock_actual <= stock_minimo 
                AND stock_minimo > 0
                ORDER BY stock_actual ASC";
        return $this->db->fetchAll($sql, [$sucursal_id]);
    }

    public function search($search, $sucursal_id)
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE id_sucursal = ? 
                AND (nombre LIKE ? OR descripcion LIKE ? OR codigo LIKE ?)
                ORDER BY nombre";
        $searchTerm = "%{$search}%";
        return $this->db->fetchAll($sql, [$sucursal_id, $searchTerm, $searchTerm, $searchTerm]);
    }

    public function updateStock($id, $cantidad, $operacion = 'add')
    {
        if ($operacion === 'add') {
            $sql = "UPDATE {$this->table} SET stock_actual = stock_actual + ? WHERE id = ?";
        } else {
            $sql = "UPDATE {$this->table} SET stock_actual = stock_actual - ? WHERE id = ?";
        }
        return $this->db->execute($sql, [$cantidad, $id]);
    }

    public function getStockActual($id)
    {
        $sql = "SELECT stock_actual FROM {$this->table} WHERE id = ?";
        $result = $this->db->fetchOne($sql, [$id]);
        return $result['stock_actual'] ?? 0;
    }

    public function findByNombre($nombre)
    {
        $sql = "SELECT * FROM {$this->table} WHERE nombre = ? LIMIT 1";
        return $this->db->fetchOne($sql, [$nombre]);
    }

    public function getByProveedor($proveedor_id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE id_proveedor = ? ORDER BY nombre";
        return $this->db->fetchAll($sql, [$proveedor_id]);
    }
}
