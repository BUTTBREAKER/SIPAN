<?php

namespace App\Models;

class Producto extends BaseModel
{
    protected string $table = 'productos';

    public function getAllBySucursal($sucursal_id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE id_sucursal = ? ORDER BY nombre";
        return $this->db->fetchAll($sql, [$sucursal_id]);
    }

    /**
     * Obtiene todos los productos, opcionalmente filtrados por sucursal.
     * Bolt Optimization: Se corrigió la firma para que coincida con BaseModel::all($sucursal_id)
     * y se implementó el filtrado por sucursal a nivel de base de datos.
     * Esto evita cargar el catálogo global de productos en cada sucursal (O(N) -> O(N/S)).
     */
    public function all(?int $sucursal_id = null): array
    {
        $sql = "SELECT * FROM {$this->table}";
        $params = [];

        if ($sucursal_id !== null) {
            $sql .= " WHERE id_sucursal = ?";
            $params[] = $sucursal_id;
        }

        $sql .= " ORDER BY nombre";

        return $this->db->fetchAll($sql, $params);
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
                AND (nombre LIKE ? OR descripcion LIKE ?)
                ORDER BY nombre";
        $searchTerm = "%{$search}%";
        return $this->db->fetchAll($sql, [$sucursal_id, $searchTerm, $searchTerm]);
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

    public function getConReceta($sucursal_id)
    {
        $sql = "SELECT DISTINCT p.* 
                FROM {$this->table} p
                INNER JOIN recetas r ON p.id = r.id_producto
                WHERE p.id_sucursal = ?
                ORDER BY p.nombre";
        return $this->db->fetchAll($sql, [$sucursal_id]);
    }

    /**
     * Obtiene los productos de una sucursal con el valor del stock calculado.
     * Optimización Bolt: Calcula valor_stock directamente en SQL para mayor eficiencia.
     */
    public function getBySucursal($sucursal_id)
    {
        // Optimización Bolt: El valor_stock se calcula en la DB para evitar bucles O(N) en PHP.
        $sql = "SELECT *, (stock_actual * precio_actual) as valor_stock
                FROM productos
                WHERE id_sucursal = ?";
        return $this->db->fetchAll($sql, [$sucursal_id]);
    }

    public function getStatsByDateRange($sucursal_id, $fecha_inicio, $fecha_fin)
    {
        $sql = "SELECT p.*, (p.stock_actual * p.precio_actual) as valor_stock,
                       COALESCE(SUM(vp.cantidad), 0) as total_vendido,
                       COALESCE(SUM(vp.subtotal), 0) as total_generado
                FROM {$this->table} p
                LEFT JOIN venta_productos vp ON p.id = vp.id_producto
                LEFT JOIN ventas v ON vp.id_venta = v.id AND v.estado = 'completada' 
                     AND v.fecha_venta >= ? AND v.fecha_venta <= ?
                WHERE p.id_sucursal = ?
                GROUP BY p.id
                ORDER BY total_vendido DESC, p.nombre ASC";
        
        return $this->db->fetchAll($sql, [$fecha_inicio . ' 00:00:00', $fecha_fin . ' 23:59:59', $sucursal_id]);
    }
}
