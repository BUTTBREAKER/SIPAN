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

            // 2. Insertar Detalles y Actualizar Stock/Lotes
            $sqlDetalle = "INSERT INTO compra_detalles (id_compra, tipo_item, id_item, cantidad, costo_unitario, subtotal, lote_codigo, fecha_vencimiento) 
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            
            $loteModel = new Lote();
            $insumoModel = new Insumo();

            foreach ($detalles as $detalle) {
                // Insertar detalle
                $this->db->execute($sqlDetalle, [
                    $compraId,
                    $detalle['tipo_item'],
                    $detalle['id_item'],
                    $detalle['cantidad'],
                    $detalle['costo_unitario'],
                    $detalle['subtotal'],
                    $detalle['lote_codigo'] ?? null,
                    $detalle['fecha_vencimiento'] ?? null
                ]);

                // Crear Lote si aplica
                if (!empty($detalle['lote_codigo'])) {
                    $loteData = [
                        'id_sucursal' => $compraData['id_sucursal'],
                        'tipo' => $detalle['tipo_item'],
                        'id_item' => $detalle['id_item'],
                        'codigo_lote' => $detalle['lote_codigo'],
                        'fecha_entrada' => date('Y-m-d'),
                        'fecha_vencimiento' => $detalle['fecha_vencimiento'],
                        'cantidad_inicial' => $detalle['cantidad'],
                        'cantidad_actual' => $detalle['cantidad'], // Inicialmente igual
                        'costo_unitario' => $detalle['costo_unitario']
                    ];
                    $loteModel->registrar($loteData);
                }

                // Actualizar Stock con Costo Promedio Ponderado
                if ($detalle['tipo_item'] === 'insumo') {
                    $this->updateStockWithWeightedAverage(
                        $detalle['id_item'],
                        $detalle['cantidad'],
                        $detalle['costo_unitario']
                    );
                } else if ($detalle['tipo_item'] === 'producto') {
                   // Si compramos productos terminados (revender)
                   $prodModel = new Producto();
                   $prodModel->updateStock($detalle['id_item'], $detalle['cantidad'], 'add');
                }
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
    
    public function getWithProveedor($sucursal_id) {
         $sql = "SELECT c.*, p.nombre as proveedor_nombre, CONCAT(u.primer_nombre, ' ', u.apellido_paterno) as usuario_nombre
                FROM {$this->table} c
                LEFT JOIN proveedores p ON c.id_proveedor = p.id
                LEFT JOIN usuarios u ON c.id_usuario = u.id
                WHERE c.id_sucursal = ?
                ORDER BY c.fecha_compra DESC";
        return $this->db->fetchAll($sql, [$sucursal_id]);
    }
    
    public function getById($id) {
        $sql = "SELECT c.*, p.nombre as proveedor_nombre, p.telefono as proveedor_telefono,
                       CONCAT(u.primer_nombre, ' ', u.apellido_paterno) as usuario_nombre
                FROM {$this->table} c
                LEFT JOIN proveedores p ON c.id_proveedor = p.id
                LEFT JOIN usuarios u ON c.id_usuario = u.id
                WHERE c.id = ?";
        return $this->db->fetch($sql, [$id]);
    }
    
    public function getDetalles($id_compra) {
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

    public function getByProveedor($proveedor_id) {
        $sql = "SELECT c.*, CONCAT(u.primer_nombre, ' ', u.apellido_paterno) as usuario_nombre
                FROM {$this->table} c
                LEFT JOIN usuarios u ON c.id_usuario = u.id
                WHERE c.id_proveedor = ?
                ORDER BY c.fecha_compra DESC";
        return $this->db->fetchAll($sql, [$proveedor_id]);
    }
}
