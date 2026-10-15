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

            // Optimización Bolt: Batch multi-row INSERT (Reduce N database round-trips to 1)
            if (!empty($insumos)) {
                $placeholders = [];
                $params = [];
                foreach ($insumos as $insumo) {
                    $placeholders[] = "(?, ?, ?, ?)";
                    $params[] = $proveedor_id;
                    $params[] = $insumo['id_insumo'];
                    $params[] = $insumo['precio'] ?? 0;
                    $params[] = $insumo['tiempo_entrega'] ?? null;
                }
                $sql = "INSERT INTO proveedor_insumos (id_proveedor, id_insumo, precio, tiempo_entrega) VALUES " . implode(', ', $placeholders);
                $this->db->execute($sql, $params);
            }

            $this->db->commit();
        } catch (\Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    public function getInsumosSinProveedor($sucursal_id)
    {
        // Optimización Bolt: Reemplazado LEFT JOIN + GROUP BY + HAVING por NOT EXISTS.
        // Esto elimina la agregación pesada sobre toda la tabla de insumos y permite usar índices.
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
