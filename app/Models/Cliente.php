<?php

namespace App\Models;

class Cliente extends BaseModel
{
    protected $table = 'clientes';

    public function search($search, $sucursal_id = null)
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE (nombre LIKE ? OR apellido LIKE ? OR documento_numero LIKE ? OR telefono LIKE ?)";

        $params = ["%{$search}%", "%{$search}%", "%{$search}%", "%{$search}%"];

        if ($sucursal_id) {
            $sql .= " AND id_sucursal = ?";
            $params[] = $sucursal_id;
        }

        $sql .= " ORDER BY nombre";

        return $this->db->fetchAll($sql, $params);
    }

    public function findByDocumento($documento_numero)
    {
        $sql = "SELECT * FROM {$this->table} WHERE documento_numero = ? LIMIT 1";
        return $this->db->fetchOne($sql, [$documento_numero]);
    }

    /**
     * Obtiene los clientes con su resumen de pedidos (total pedidos, monto comprado, pagado y deuda).
     * Optimización Bolt: Consulta unificada con COALESCE para evitar nulos y eliminar dependencia de vista inexistente.
     */
    public function getWithResumen($sucursal_id = null)
    {
        $sql = "SELECT c.*,
                       COUNT(p.id) as total_pedidos,
                       COALESCE(SUM(p.total), 0) as total_comprado,
                       COALESCE(SUM(p.monto_pagado), 0) as total_pagado,
                       COALESCE(SUM(p.monto_deuda), 0) as total_deuda
                FROM {$this->table} c
                LEFT JOIN pedidos p ON c.id = p.id_cliente";

        $params = [];
        if ($sucursal_id) {
            $sql .= " WHERE c.id_sucursal = ?";
            $params[] = $sucursal_id;
        }

        $sql .= " GROUP BY c.id ORDER BY c.nombre";

        return $this->db->fetchAll($sql, $params);
    }


    public function getBySucursal($sucursal_id)
    {
        $sql = "SELECT * FROM clientes WHERE id_sucursal = ?";
        return $this->db->fetchAll($sql, [$sucursal_id]);
    }

    /**
     * Obtiene los clientes con sus estadísticas de compra (total de ventas y monto total)
     * Optimización Bolt: Evita N+1 queries al traer estadísticas en una sola consulta
     */
    public function getBySucursalWithStats($sucursal_id)
    {
        $sql = "SELECT c.*,
                       COUNT(v.id) as total_compras,
                       COALESCE(SUM(v.total), 0) as monto_total
                FROM {$this->table} c
                LEFT JOIN ventas v ON c.id = v.id_cliente
                WHERE c.id_sucursal = ?
                GROUP BY c.id
                ORDER BY c.nombre ASC";

        return $this->db->fetchAll($sql, [$sucursal_id]);
    }
}
