<?php

namespace App\Models;

class Lote extends BaseModel
{
    protected $table = 'lotes';

    /**
     * Registrar un nuevo lote
     */
    public function registrar($data)
    {
        $sql = "INSERT INTO {$this->table} 
                (id_sucursal, tipo, id_item, codigo_lote, fecha_entrada, fecha_vencimiento, cantidad_inicial, cantidad_actual, costo_unitario)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $this->db->execute($sql, [
            $data['id_sucursal'],
            $data['tipo'],
            $data['id_item'],
            $data['codigo_lote'],
            $data['fecha_entrada'],
            $data['fecha_vencimiento'] ?: null,
            $data['cantidad_inicial'],
            $data['cantidad_inicial'], // Al inicio actual = inicial
            $data['costo_unitario']
        ]);

        return $this->db->lastInsertId();
    }

    /**
     * Obtener lotes por vencer en X días
     */
    public function getPorVencer($sucursal_id, $dias = 30)
    {
        $fecha_limite = date('Y-m-d', strtotime("+{$dias} days"));
        $fecha_hoy = date('Y-m-d');

        // Consulta unificada para productos e insumos
        $sql = "SELECT l.*, 
                       CASE 
                           WHEN l.tipo = 'producto' THEN p.nombre 
                           WHEN l.tipo = 'insumo' THEN i.nombre 
                       END as nombre_item
                FROM {$this->table} l
                LEFT JOIN productos p ON l.id_item = p.id AND l.tipo = 'producto'
                LEFT JOIN insumos i ON l.id_item = i.id AND l.tipo = 'insumo'
                WHERE l.id_sucursal = ? 
                AND l.estado = 'activo'
                AND l.cantidad_actual > 0
                AND l.fecha_vencimiento IS NOT NULL
                AND l.fecha_vencimiento BETWEEN ? AND ?
                ORDER BY l.fecha_vencimiento ASC";

        return $this->db->fetchAll($sql, [$sucursal_id, $fecha_hoy, $fecha_limite]);
    }

    /**
     * Actualizar stock de un lote (consumo)
     * Retorna la cantidad que NO se pudo descontar (si stock insuficiente)
     */
    public function descontarStock($tipo, $id_item, $cantidad, $sucursal_id)
    {
        // Buscar lotes activos ordenados por vencimiento (FIFO / FEFO)
        $sql = "SELECT * FROM {$this->table} 
                WHERE tipo = ? AND id_item = ? AND id_sucursal = ? 
                AND estado = 'activo' AND cantidad_actual > 0
                ORDER BY fecha_vencimiento ASC, created_at ASC";

        $lotes = $this->db->fetchAll($sql, [$tipo, $id_item, $sucursal_id]);

        $pendiente = $cantidad;

        foreach ($lotes as $lote) {
            if ($pendiente <= 0) {
                break;
            }

            $descontar = min($pendiente, $lote['cantidad_actual']);

            // Actualizar lote
            $nuevo_stock = $lote['cantidad_actual'] - $descontar;
            $estado = ($nuevo_stock <= 0) ? 'agotado' : 'activo';

            $this->db->execute(
                "UPDATE {$this->table} SET cantidad_actual = ?, estado = ? WHERE id = ?",
                [$nuevo_stock, $estado, $lote['id']]
            );

            $pendiente -= $descontar;
        }

        return $pendiente; // Si es 0, se descontó todo correctamente
    }
}
