<?php

namespace App\Models;

class Produccion extends BaseModel
{
    protected $table = 'producciones';

    public function createWithInsumos($produccion_data, $insumos)
    {
        if (empty($insumos)) {
            throw new \Exception("Debe agregar al menos un insumo");
        }

        try {
            $this->db->beginTransaction();

            // 1. Crear producción
            $produccion_id = $this->create($produccion_data);

            // Instanciar modelos para descontar stock
            $loteModel = new Lote();

            $batch_values = [];
            $batch_params = [];

            // 2. Procesar insumos
            // Bolt Optimization: Multi-row INSERT for production insumos (O(1) round-trip)
            $insumo_values = [];
            $insumo_params = [];

            foreach ($insumos as $insumo) {
                if (!isset($insumo['id_insumo']) || !isset($insumo['cantidad_utilizada'])) {
                    throw new \Exception('Estructura de insumo inválida: ' . print_r($insumo, true));
                }

                $id_insumo = $insumo['id_insumo'];
                $cantidad = $insumo['cantidad_utilizada'];

                // A. Registrar consumo en tabla produccion_insumos
                $insumo_values[] = "(?, ?, ?)";
                array_push($insumo_params, $produccion_id, $id_insumo, $cantidad);

                // B. Descontar de Lotes (FIFO)
                // Note: This must remain in the loop as it handles complex FIFO logic across multiple rows
                $loteModel->descontarStock('insumo', $id_insumo, $cantidad, $produccion_data['id_sucursal']);

                // Optimización Bolt: Eliminada actualización manual del stock total del insumo.
                // El trigger 'tr_descontar_insumos_produccion' en la DB ya realiza este descuento automáticamente
                // al insertar en la tabla 'produccion_insumos'.
            }

            // Since we already have a guard clause at the top, $insumo_values won't be empty here
            $sql = "INSERT INTO produccion_insumos (id_produccion, id_insumo, cantidad_utilizada) VALUES " . implode(',', $insumo_values);
            $this->db->execute($sql, $insumo_params);

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
