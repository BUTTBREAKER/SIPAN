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

            error_log('Producción creada con ID: ' . $produccion_id); // LOG

            // Instanciar modelos para descontar stock
            $loteModel = new Lote();
            $insumoModel = new Insumo();

            // 2. Procesar insumos
            foreach ($insumos as $insumo) {
                error_log('Procesando insumo: ' . print_r($insumo, true)); // LOG

                if (!isset($insumo['id_insumo']) || !isset($insumo['cantidad_utilizada'])) {
                    throw new \Exception('Estructura de insumo inválida: ' . print_r($insumo, true));
                }

                $id_insumo = $insumo['id_insumo'];
                $cantidad = $insumo['cantidad_utilizada'];

                // A. Registrar consumo en tabla produccion_insumos
                $sql = "INSERT INTO produccion_insumos (id_produccion, id_insumo, cantidad_utilizada)
                    VALUES (?, ?, ?)";
                $this->db->execute($sql, [
                    $produccion_id,
                    $id_insumo,
                    $cantidad
                ]);

                // B. Descontar de Lotes (FIFO)
                // Esto busca lotes activos y los consume en orden de vencimiento
                $loteModel->descontarStock('insumo', $id_insumo, $cantidad, $produccion_data['id_sucursal']);

                // C. Descontar Stock Total del Insumo
                // Se asume que Insumo::updateStock maneja la resta
                $insumoModel->updateStock($id_insumo, $cantidad, 'subtract');
            }

            $this->db->commit();
            return $produccion_id;
        } catch (\Exception $e) {
            $this->db->rollback();
            error_log('Error en createWithInsumos: ' . $e->getMessage());
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
