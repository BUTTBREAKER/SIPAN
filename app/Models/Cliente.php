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

    public function getWithResumen($sucursal_id = null)
    {
        $sql = "SELECT * FROM v_resumen_pedidos_cliente";

        if ($sucursal_id) {
            // Filtrar por sucursal si es necesario
            $sql = "SELECT c.*, 
                           COUNT(p.id) as total_pedidos,
                           SUM(p.total) as total_comprado,
                           SUM(p.monto_pagado) as total_pagado,
                           SUM(p.monto_deuda) as total_deuda
                    FROM {$this->table} c
                    LEFT JOIN pedidos p ON c.id = p.id_cliente
                    WHERE c.id_sucursal = ?
                    GROUP BY c.id
                    ORDER BY c.nombre";
            return $this->db->fetchAll($sql, [$sucursal_id]);
        }

        return $this->db->fetchAll($sql);
    }


    public function getBySucursal($sucursal_id)
    {
        $sql = "SELECT * FROM clientes WHERE id_sucursal = ?";
        return $this->db->fetchAll($sql, [$sucursal_id]);
    }
}
