<?php

namespace App\Models;

class Producto extends BaseModel
{
    protected $table = 'productos';

    public function getAllBySucursal($sucursal_id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE id_sucursal = ? ORDER BY nombre";
        return $this->db->fetchAll($sql, [$sucursal_id]);
    }

    /**
     * Optimización Bolt: Filtra productos por sucursal a nivel de base de datos.
     * Al agregar el parámetro $sucursal_id, evitamos cargar todos los productos de todas las sucursales
     * en memoria cuando solo se necesitan los de una sucursal específica (e.g., Ventas, Recetas).
     *
     * Impacto esperado: Reduce el consumo de memoria de PHP y el tráfico de red de la BD en un 90%
     * para entornos con 10 sucursales, acelerando la carga de formularios de creación de ventas y pedidos.
     *
     * @param int|null $sucursal_id
     * @return array
     */
    public function all($sucursal_id = null)
    {
        if ($sucursal_id !== null) {
            return $this->getAllBySucursal($sucursal_id);
        }

        $sql = "SELECT * FROM {$this->table} ORDER BY nombre";
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

    public function getBySucursal($sucursal_id)
    {
        $sql = "SELECT * FROM productos WHERE id_sucursal = ?";
        return $this->db->fetchAll($sql, [$sucursal_id]);
    }
}
