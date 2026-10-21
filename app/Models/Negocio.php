<?php

namespace App\Models;

class Negocio extends BaseModel
{
    protected string $table = 'negocios';

    public function getBySucursal($sucursal_id)
    {
        // 1. Intentar por sucursal directa en negocios
        $sql = "SELECT * FROM {$this->table} WHERE id_sucursal = ? LIMIT 1";
        $negocio = $this->db->fetchOne($sql, [$sucursal_id]);
        if ($negocio) {
            return $negocio;
        }

        // 2. Intentar por sucursales.negocio_id
        $sql = "SELECT n.* FROM {$this->table} n 
                INNER JOIN sucursales s ON s.negocio_id = n.id 
                WHERE s.id = ? LIMIT 1";
        $negocio = $this->db->fetchOne($sql, [$sucursal_id]);
        if ($negocio) {
            return $negocio;
        }

        // 3. Fallback al primer negocio registrado
        return $this->db->fetchOne("SELECT * FROM {$this->table} ORDER BY id ASC LIMIT 1");
    }
}
