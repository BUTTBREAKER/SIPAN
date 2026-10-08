<?php

namespace App\Models;

class Insumo extends BaseModel
{
    protected string $table = 'insumos';

    // ✅ Nuevo método compatible con el controlador
    // Optimización Bolt: Se reemplazó la subconsulta correlacionada escalar por un LEFT JOIN
    // con una tabla derivada (ultimo_costo) que precalcula el último ID de compra por insumo.
    // Esto reduce la complejidad de O(N * M) a O(N + M).
    public function getAllBySucursal($sucursal_id)
    {
        $sql = "SELECT i.*, 
                       GROUP_CONCAT(p.nombre SEPARATOR ', ') as proveedor_nombre,
                       MAX(ultimo_costo.costo_unitario) as costo_ultimo
                FROM {$this->table} i
                LEFT JOIN proveedor_insumos pi ON i.id = pi.id_insumo
                LEFT JOIN proveedores p ON pi.id_proveedor = p.id
                LEFT JOIN (
                    SELECT cd.id_item, cd.costo_unitario
                    FROM compra_detalles cd
                    INNER JOIN (
                        SELECT cd_sub.id_item, MAX(cd_sub.id) AS max_cd_id
                        FROM compra_detalles cd_sub
                        INNER JOIN compras c_sub ON cd_sub.id_compra = c_sub.id
                        WHERE cd_sub.tipo_item = 'insumo' AND c_sub.id_sucursal = ?
                        GROUP BY cd_sub.id_item
                    ) max_cd ON cd.id = max_cd.max_cd_id
                ) ultimo_costo ON i.id = ultimo_costo.id_item
                WHERE i.id_sucursal = ? 
                GROUP BY i.id
                ORDER BY i.nombre";
        return $this->db->fetchAll($sql, [$sucursal_id, $sucursal_id]);
    }

    // ✅ Método general (sin sucursal)
    // Optimización Bolt: Se utiliza LEFT JOIN con tabla derivada para evitar subconsultas correlacionadas escalares.
    public function all(?int $sucursal_id = null): array
    {
        if ($sucursal_id) {
            return $this->getAllBySucursal($sucursal_id);
        }

        $sql = "SELECT i.*, 
                       GROUP_CONCAT(p.nombre SEPARATOR ', ') as proveedor_nombre,
                       MAX(ultimo_costo.costo_unitario) as costo_ultimo
                FROM {$this->table} i
                LEFT JOIN proveedor_insumos pi ON i.id = pi.id_insumo
                LEFT JOIN proveedores p ON pi.id_proveedor = p.id
                LEFT JOIN (
                    SELECT cd.id_item, cd.costo_unitario
                    FROM compra_detalles cd
                    INNER JOIN (
                        SELECT cd_sub.id_item, MAX(cd_sub.id) AS max_cd_id
                        FROM compra_detalles cd_sub
                        INNER JOIN compras c_sub ON cd_sub.id_compra = c_sub.id
                        WHERE cd_sub.tipo_item = 'insumo'
                        GROUP BY cd_sub.id_item
                    ) max_cd ON cd.id = max_cd.max_cd_id
                ) ultimo_costo ON i.id = ultimo_costo.id_item
                GROUP BY i.id
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
        $sql = "SELECT i.* 
                FROM {$this->table} i
                INNER JOIN proveedor_insumos pi ON i.id = pi.id_insumo
                WHERE pi.id_proveedor = ? 
                ORDER BY i.nombre";
        return $this->db->fetchAll($sql, [$proveedor_id]);
    }

    public function updateProveedor($insumo_id, $proveedor_id, $precio = 0)
    {
        // Primero eliminar cualquier relación existente (para mantener una relación 1 a 1 lógica desde la UI,
        // aunque la DB permita muchos a muchos)
        $sqlDelete = "DELETE FROM proveedor_insumos WHERE id_insumo = ?";
        $this->db->execute($sqlDelete, [$insumo_id]);

        if (!empty($proveedor_id)) {
            $sqlInsert = "INSERT INTO proveedor_insumos (id_proveedor, id_insumo, precio) VALUES (?, ?, ?)";
            $this->db->execute($sqlInsert, [$proveedor_id, $insumo_id, $precio]);
        }
    }

    public function getStatsByDateRange($sucursal_id, $fecha_inicio, $fecha_fin)
    {
        $sql = "SELECT i.*, 
                       COALESCE(SUM(pi.cantidad_utilizada), 0) as cantidad_usada,
                       COALESCE(SUM(pi.cantidad_utilizada * i.precio_unitario), 0) as gasto_total
                FROM {$this->table} i
                LEFT JOIN produccion_insumos pi ON i.id = pi.id_insumo
                LEFT JOIN producciones pr ON pi.id_produccion = pr.id 
                     AND pr.fecha_produccion >= ? AND pr.fecha_produccion <= ?
                WHERE i.id_sucursal = ?
                GROUP BY i.id
                ORDER BY cantidad_usada DESC, i.nombre ASC";

        return $this->db->fetchAll($sql, [$fecha_inicio . ' 00:00:00', $fecha_fin . ' 23:59:59', $sucursal_id]);
    }
}
