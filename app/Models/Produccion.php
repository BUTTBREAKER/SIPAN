<?php

namespace App\Models;

class Produccion extends BaseModel
{
    protected $table = 'producciones';

    public function createWithInsumos($produccion_data, $insumos)
    {
        try {
            $this->db->beginTransaction();

            // 1. Crear producción
            $produccion_id = $this->create($produccion_data);

            // Instanciar modelos para descontar stock
            $loteModel = new Lote();

            $batch_values = [];
            $batch_params = [];

            // 2. Procesar insumos
            foreach ($insumos as $insumo) {
                if (!isset($insumo['id_insumo']) || !isset($insumo['cantidad_utilizada'])) {
                    throw new \Exception('Estructura de insumo inválida: ' . print_r($insumo, true));
                }

                $id_insumo = $insumo['id_insumo'];
                $cantidad = $insumo['cantidad_utilizada'];

                // A. Preparar para inserción batch en produccion_insumos
                // Bolt Optimization: Reduce database round-trips from O(N) to O(1)
                $batch_values[] = "(?, ?, ?)";
                array_push($batch_params, $produccion_id, $id_insumo, $cantidad);

                // B. Descontar de Lotes (FIFO) - Mantenemos en bucle por complejidad lógica de selección de lotes
                $loteModel->descontarStock('insumo', $id_insumo, $cantidad, $produccion_data['id_sucursal']);

                // C. Redundancia Eliminada: El stock total de insumos se descuenta automáticamente
                // mediante el trigger 'tr_descontar_insumos_produccion' (see 001.sql:547)
                // upon insertion into produccion_insumos.
            }

            // Ejecutar inserción batch de insumos
            if (!empty($batch_values)) {
                $sql_batch = "INSERT INTO produccion_insumos (id_produccion, id_insumo, cantidad_utilizada) VALUES " . implode(', ', $batch_values);
                $this->db->execute($sql_batch, $batch_params);
            }

            $this->db->commit();
            return $produccion_id;
        } catch (\Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    public function getInsumos($produccion_id)
    {
        $sql = "SELECT pi.*, i.nombre, i.unidad_medida, i.precio_unitario
                FROM produccion_insumos pi
                INNER JOIN insumos i ON pi.id_insumo = i.id
                WHERE pi.id_produccion = ?";
        return $this->db->fetchAll($sql, [$produccion_id]);
    }

    public function getWithDetails($sucursal_id, $fecha_inicio = null, $fecha_fin = null)
    {
        $sql = "SELECT pr.*, p.nombre as producto_nombre, 
                       u.primer_nombre, u.apellido_paterno
                FROM {$this->table} pr
                INNER JOIN productos p ON pr.id_producto = p.id
                INNER JOIN usuarios u ON pr.id_usuario = u.id
                WHERE pr.id_sucursal = ?";

        $params = [$sucursal_id];

        if ($fecha_inicio) {
            $sql .= " AND DATE(pr.fecha_produccion) >= ?";
            $params[] = $fecha_inicio;
        }

        if ($fecha_fin) {
            $sql .= " AND DATE(pr.fecha_produccion) <= ?";
            $params[] = $fecha_fin;
        }

        $sql .= " ORDER BY pr.fecha_produccion DESC";

        return $this->db->fetchAll($sql, $params);
    }
}
