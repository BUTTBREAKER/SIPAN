<?php

namespace App\Models;

class Compra extends BaseModel
{
    protected $table = 'compras';

    public function createWithDetails($compraData, $detalles)
    {
        try {
            $this->db->beginTransaction();

            // 1. Crear Compra
            $compraId = $this->create($compraData);

            if (empty($detalles)) {
                $this->db->commit();
                return $compraId;
            }

            // 2. Pre-fetch stock and price data for items (O(1) database round-trips)
            $insumoIds = [];
            $productoIds = [];
            foreach ($detalles as $detalle) {
                if ($detalle['tipo_item'] === 'insumo') {
                    $insumoIds[] = $detalle['id_item'];
                } elseif ($detalle['tipo_item'] === 'producto') {
                    $productoIds[] = $detalle['id_item'];
                }
            }

            $insumoData = [];
            if (!empty($insumoIds)) {
                $uniqueIds = array_unique($insumoIds);
                $placeholders = implode(',', array_fill(0, count($uniqueIds), '?'));
                $sql = "SELECT id, stock_actual, precio_unitario FROM insumos WHERE id IN ($placeholders)";
                $rows = $this->db->fetchAll($sql, array_values($uniqueIds));
                foreach ($rows as $row) {
                    $insumoData[$row['id']] = $row;
                }
            }

            $productoData = [];
            if (!empty($productoIds)) {
                $uniqueIds = array_unique($productoIds);
                $placeholders = implode(',', array_fill(0, count($uniqueIds), '?'));
                $sql = "SELECT id, stock_actual FROM productos WHERE id IN ($placeholders)";
                $rows = $this->db->fetchAll($sql, array_values($uniqueIds));
                foreach ($rows as $row) {
                    $productoData[$row['id']] = $row;
                }
            }

            // 3. Preparar Batch Inserts and Updates
            $detallesBatch = [];
            $lotesBatch = [];
            $insumosUpdates = [];
            $productosUpdates = [];

            $loteModel = $this->loteModel ?? new Lote();

            foreach ($detalles as $detalle) {
                $id_item = $detalle['id_item'];
                $tipo = $detalle['tipo_item'];
                $cantidad = floatval($detalle['cantidad']);
                $costo = floatval($detalle['costo_unitario']);

                // Collect details for batch insert
                $detallesBatch[] = [
                    $compraId,
                    $tipo,
                    $id_item,
                    $cantidad,
                    $costo,
                    $detalle['subtotal'],
                    $detalle['lote_codigo'] ?? null,
                    $detalle['fecha_vencimiento'] ?? null
                ];

                // Collect lotes for batch insert
                if (!empty($detalle['lote_codigo'])) {
                    $lotesBatch[] = [
                        'id_sucursal' => $compraData['id_sucursal'],
                        'tipo' => $tipo,
                        'id_item' => $id_item,
                        'codigo_lote' => $detalle['lote_codigo'],
                        'fecha_entrada' => date('Y-m-d'),
                        'fecha_vencimiento' => $detalle['fecha_vencimiento'],
                        'cantidad_inicial' => $cantidad,
                        'costo_unitario' => $costo
                    ];
                }

                // Calculate stock updates in-memory
                if ($tipo === 'insumo' && isset($insumoData[$id_item])) {
                    $stock_actual = floatval($insumoData[$id_item]['stock_actual']);
                    $costo_actual = floatval($insumoData[$id_item]['precio_unitario']);

                    $valor_actual = $stock_actual * $costo_actual;
                    $valor_nuevo = $cantidad * $costo;
                    $stock_total = $stock_actual + $cantidad;

                    $costo_promedio = $stock_total > 0 ? ($valor_actual + $valor_nuevo) / $stock_total : $costo;

                    // Update local state for subsequent items of same id if any
                    $insumoData[$id_item]['stock_actual'] = $stock_total;
                    $insumoData[$id_item]['precio_unitario'] = $costo_promedio;

                    $insumosUpdates[$id_item] = [
                        'stock_actual' => $stock_total,
                        'precio_unitario' => $costo_promedio
                    ];
                } elseif ($tipo === 'producto' && isset($productoData[$id_item])) {
                    $stock_total = floatval($productoData[$id_item]['stock_actual']) + $cantidad;
                    $productoData[$id_item]['stock_actual'] = $stock_total;
                    $productosUpdates[$id_item] = $stock_total;
                }
            }

            // 4. Execute Batch Operations

            // 4a. Batch insert details
            if (!empty($detallesBatch)) {
                $placeholders = implode(', ', array_fill(0, count($detallesBatch), '(?, ?, ?, ?, ?, ?, ?, ?)'));
                $values = [];
                foreach ($detallesBatch as $d) {
                    $values = array_merge($values, $d);
                }
                $sqlDetalle = "INSERT INTO compra_detalles (id_compra, tipo_item, id_item, cantidad, costo_unitario, subtotal, lote_codigo, fecha_vencimiento)
                               VALUES $placeholders";
                $this->db->execute($sqlDetalle, $values);
            }

            // 4b. Batch register lotes
            if (!empty($lotesBatch)) {
                $loteModel->registrarBatch($lotesBatch);
            }

            // 4c. Batch update insumos (O(N_unique_items) database round-trips, still better than O(N_items))
            foreach ($insumosUpdates as $id => $data) {
                $sql = "UPDATE insumos SET stock_actual = ?, precio_unitario = ? WHERE id = ?";
                $this->db->execute($sql, [$data['stock_actual'], $data['precio_unitario'], $id]);
            }

            // 4d. Batch update productos
            foreach ($productosUpdates as $id => $stock) {
                $sql = "UPDATE productos SET stock_actual = ? WHERE id = ?";
                $this->db->execute($sql, [$stock, $id]);
            }

            $this->db->commit();
            return $compraId;
        } catch (\Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    /**
     * Actualizar stock de insumo con cálculo de costo promedio ponderado
     */
    private function updateStockWithWeightedAverage($id_insumo, $cantidad_nueva, $costo_nuevo)
    {
        // Obtener stock y costo actual
        $sql = "SELECT stock_actual, precio_unitario FROM insumos WHERE id = ?";
        $insumo = $this->db->fetch($sql, [$id_insumo]);

        $stock_actual = floatval($insumo['stock_actual']);
        $costo_actual = floatval($insumo['precio_unitario']);

        // Calcular costo promedio ponderado
        $valor_actual = $stock_actual * $costo_actual;
        $valor_nuevo = floatval($cantidad_nueva) * floatval($costo_nuevo);
        $stock_total = $stock_actual + floatval($cantidad_nueva);

        $costo_promedio = $stock_total > 0 ? ($valor_actual + $valor_nuevo) / $stock_total : $costo_nuevo;

        // Actualizar stock y costo promedio
        $sqlUpdate = "UPDATE insumos SET stock_actual = stock_actual + ?, precio_unitario = ? WHERE id = ?";
        $this->db->execute($sqlUpdate, [floatval($cantidad_nueva), $costo_promedio, $id_insumo]);
    }

    public function getWithProveedor($sucursal_id)
    {
         $sql = "SELECT c.*, p.nombre as proveedor_nombre, CONCAT(u.primer_nombre, ' ', u.apellido_paterno) as usuario_nombre
                FROM {$this->table} c
                LEFT JOIN proveedores p ON c.id_proveedor = p.id
                LEFT JOIN usuarios u ON c.id_usuario = u.id
                WHERE c.id_sucursal = ?
                ORDER BY c.fecha_compra DESC";
        return $this->db->fetchAll($sql, [$sucursal_id]);
    }

    public function getById($id)
    {
        $sql = "SELECT c.*, p.nombre as proveedor_nombre, p.telefono as proveedor_telefono,
                       CONCAT(u.primer_nombre, ' ', u.apellido_paterno) as usuario_nombre
                FROM {$this->table} c
                LEFT JOIN proveedores p ON c.id_proveedor = p.id
                LEFT JOIN usuarios u ON c.id_usuario = u.id
                WHERE c.id = ?";
        return $this->db->fetch($sql, [$id]);
    }

    public function getDetalles($id_compra)
    {
        $sql = "SELECT cd.*, 
                       CASE 
                           WHEN cd.tipo_item = 'insumo' THEN i.nombre
                           WHEN cd.tipo_item = 'producto' THEN p.nombre
                       END as item_nombre,
                       CASE 
                           WHEN cd.tipo_item = 'insumo' THEN i.unidad_medida
                           ELSE 'unidad'
                       END as unidad_medida
                FROM compra_detalles cd
                LEFT JOIN insumos i ON cd.tipo_item = 'insumo' AND cd.id_item = i.id
                LEFT JOIN productos p ON cd.tipo_item = 'producto' AND cd.id_item = p.id
                WHERE cd.id_compra = ?
                ORDER BY cd.id";
        return $this->db->fetchAll($sql, [$id_compra]);
    }

    public function getByProveedor($proveedor_id)
    {
        $sql = "SELECT c.*, CONCAT(u.primer_nombre, ' ', u.apellido_paterno) as usuario_nombre
                FROM {$this->table} c
                LEFT JOIN usuarios u ON c.id_usuario = u.id
                WHERE c.id_proveedor = ?
                ORDER BY c.fecha_compra DESC";
        return $this->db->fetchAll($sql, [$proveedor_id]);
    }
}
