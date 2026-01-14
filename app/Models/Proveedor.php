<?php

namespace App\Models;

class Proveedor extends BaseModel
{
    protected $table = 'proveedores';

    public function getAllBySucursal($sucursal_id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE sucursal_id = ? ORDER BY nombre ASC";
        return $this->db->fetchAll($sql, [$sucursal_id]);
    }

    public function getWithInsumos($id)
    {
        $sql = "SELECT p.*, pi.id_insumo, i.nombre AS insumo_nombre, pi.precio, pi.tiempo_entrega
                FROM proveedores p
                LEFT JOIN proveedor_insumos pi ON p.id = pi.id_proveedor
                LEFT JOIN insumos i ON pi.id_insumo = i.id
                WHERE p.id = ?";
        return $this->db->fetchAll($sql, [$id]);
    }

    public function addInsumos($proveedor_id, $insumos)
    {
        $this->db->beginTransaction();
        try {
            $this->db->execute("DELETE FROM proveedor_insumos WHERE id_proveedor = ?", [$proveedor_id]);
            foreach ($insumos as $insumo) {
                $sql = "INSERT INTO proveedor_insumos (id_proveedor, id_insumo, precio, tiempo_entrega)
                        VALUES (?, ?, ?, ?)";
                $this->db->execute($sql, [
                    $proveedor_id,
                    $insumo['id_insumo'],
                    $insumo['precio'] ?? 0,
                    $insumo['tiempo_entrega'] ?? null
                ]);
            }
            $this->db->commit();
        } catch (\Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    public function getInsumosSinProveedor($sucursal_id)
    {
        $sql = "SELECT i.id, i.nombre, i.unidad_medida, i.stock_actual, i.stock_minimo
            FROM insumos i
            LEFT JOIN proveedor_insumos pi ON i.id = pi.id_insumo
            WHERE i.id_sucursal = ?
            GROUP BY i.id
            HAVING COUNT(pi.id) = 0
            ORDER BY i.nombre";
        return $this->db->fetchAll($sql, [$sucursal_id]);
    }
}
