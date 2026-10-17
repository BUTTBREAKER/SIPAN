<?php

namespace App\Models;

class Proveedor extends BaseModel
{
    protected string $table = 'proveedores';

    public function getAllBySucursal($sucursal_id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE id_sucursal = ? ORDER BY nombre ASC";
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

            // Bolt Optimization: Multi-row batch insert (O(1) database round-trips instead of O(N))
            if (!empty($insumos)) {
                $placeholders = [];
                $values = [];
                foreach ($insumos as $insumo) {
                    $placeholders[] = "(?, ?, ?, ?)";
                    $values[] = $proveedor_id;
                    $values[] = $insumo['id_insumo'];
                    $values[] = $insumo['precio'] ?? 0;
                    $values[] = $insumo['tiempo_entrega'] ?? null;
                }

                $sql = "INSERT INTO proveedor_insumos (id_proveedor, id_insumo, precio, tiempo_entrega)
                        VALUES " . implode(', ', $placeholders);
                $this->db->execute($sql, $values);
            }

            $this->db->commit();
        } catch (\Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    public function getInsumosSinProveedor($sucursal_id)
    {
        // Bolt Optimization: Replace LEFT JOIN ... GROUP BY ... HAVING COUNT = 0 with SARGable NOT EXISTS subquery.
        // Avoids costly grouping, full table joins, and temporary table creation on unassigned insumo lookup.
        $sql = "SELECT i.id, i.nombre, i.unidad_medida, i.stock_actual, i.stock_minimo
                FROM insumos i
                WHERE i.id_sucursal = ?
                  AND NOT EXISTS (
                      SELECT 1 FROM proveedor_insumos pi WHERE pi.id_insumo = i.id
                  )
                ORDER BY i.nombre";
        return $this->db->fetchAll($sql, [$sucursal_id]);
    }
}
