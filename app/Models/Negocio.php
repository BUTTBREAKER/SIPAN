<?php

namespace App\Models;

class Negocio extends BaseModel
{
    protected string $table = 'negocios';

    public function getBySucursal($sucursal_id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE id_sucursal = ? LIMIT 1";
        return $this->db->fetchOne($sql, [$sucursal_id]);
    }
}
