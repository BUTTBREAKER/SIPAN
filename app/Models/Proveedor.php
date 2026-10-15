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

    /**
     * Asserts and inserts supplier insumos in a single batched query (Bolt Optimization: O(1) DB round-trip)
     */
    public function addInsumos($proveedor_id, $insumos)
    {
        $this->db->beginTransaction();
        try {
            $this->db->execute("DELETE FROM proveedor_insumos WHERE id_proveedor = ?", [$proveedor_id]);

            if (!empty($insumos)) {
                $placeholders = [];
                $params = [];
                foreach ($insumos as $insumo) {
                    if (!isset($insumo['id_insumo'])) {
                        continue;
                    }
                    $placeholders[] = "(?, ?, ?, ?)";
                    array_push(
                        $params,
                        $proveedor_id,
                        $insumo['id_insumo'],
                        $insumo['precio'] ?? 0,
                        $insumo['tiempo_entrega'] ?? null
                    );
                }

                if (!empty($placeholders)) {
                    $sql = "INSERT INTO proveedor_insumos (id_proveedor, id_insumo, precio, tiempo_entrega) VALUES " . implode(', ', $placeholders);
                    $this->db->execute($sql, $params);
                }
            }

            $this->db->commit();
        } catch (\Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    /**
     * Obtiene los insumos que no tienen proveedor asignado.
     * Bolt Optimization: Utiliza NOT EXISTS para evitar JOINs globales y agrupamiento GROUP BY / HAVING.
     */
    public function getInsumosSinProveedor($sucursal_id)
    {
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
