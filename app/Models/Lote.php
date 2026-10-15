<?php

namespace App\Models;

class Lote extends BaseModel
{
    protected string $table = 'lotes';

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
     * Registrar múltiples lotes en una sola operación
     */
    public function registrarBatch($data_batch)
    {
        if (empty($data_batch)) {
            return true;
        }

        $placeholders = [];
        $values = [];

        foreach ($data_batch as $data) {
            $placeholders[] = "(?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $values[] = $data['id_sucursal'];
            $values[] = $data['tipo'];
            $values[] = $data['id_item'];
            $values[] = $data['codigo_lote'];
            $values[] = $data['fecha_entrada'];
            $values[] = $data['fecha_vencimiento'] ?: null;
            $values[] = $data['cantidad_inicial'];
            $values[] = $data['cantidad_inicial']; // Al inicio actual = inicial
            $values[] = $data['costo_unitario'];
        }

        $sql = "INSERT INTO {$this->table}
                (id_sucursal, tipo, id_item, codigo_lote, fecha_entrada, fecha_vencimiento, cantidad_inicial, cantidad_actual, costo_unitario)
                VALUES " . implode(', ', $placeholders);

        return $this->db->execute($sql, $values);
    }

    /**
     * Obtener lotes por vencer en X días (o ya vencidos si $incluir_vencidos es true)
     */
    public function getPorVencer($sucursal_id, $dias = 30, $incluir_vencidos = true)
    {
        $fecha_limite = date('Y-m-d', strtotime("+{$dias} days"));
        $fecha_hoy = date('Y-m-d');

        $whereFecha = $incluir_vencidos
            ? "AND l.fecha_vencimiento <= ?"
            : "AND l.fecha_vencimiento BETWEEN ? AND ?";
        $params = $incluir_vencidos
            ? [$sucursal_id, $fecha_limite]
            : [$sucursal_id, $fecha_hoy, $fecha_limite];

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
                {$whereFecha}
                ORDER BY l.fecha_vencimiento ASC";

        return $this->db->fetchAll($sql, $params);
    }

    /**
     * Actualizar stock de un lote (consumo)
     * Retorna la cantidad que NO se pudo descontar (si stock insuficiente)
     */
    public function descontarStock($tipo, $id_item, $cantidad, $sucursal_id)
    {
        // Buscar lotes activos ordenados por vencimiento (FIFO / FEFO)
        // Bolt Optimization: Select only required columns (id, cantidad_actual) to reduce memory overhead
        $sql = "SELECT id, cantidad_actual FROM {$this->table}
                WHERE tipo = ? AND id_item = ? AND id_sucursal = ? 
                AND estado = 'activo' AND cantidad_actual > 0
                ORDER BY fecha_vencimiento ASC, created_at ASC";

        $lotes = $this->db->fetchAll($sql, [$tipo, $id_item, $sucursal_id]);

        $pendiente = (float)$cantidad;
        $updates = [];

        foreach ($lotes as $lote) {
            if ($pendiente <= 0) {
                break;
            }

            $cantActual = (float)$lote['cantidad_actual'];
            $descontar = min($pendiente, $cantActual);

            // Actualizar lote
            $nuevo_stock = $cantActual - $descontar;
            $estado = ($nuevo_stock <= 0) ? 'agotado' : 'activo';

            $updates[] = [
                'id' => $lote['id'],
                'cantidad_actual' => $nuevo_stock,
                'estado' => $estado
            ];

            $pendiente -= $descontar;
        }

        if (!empty($updates)) {
            if (count($updates) === 1) {
                // Bolt Optimization: Single UPDATE statement for single-lot deduction
                $this->db->execute(
                    "UPDATE {$this->table} SET cantidad_actual = ?, estado = ? WHERE id = ?",
                    [$updates[0]['cantidad_actual'], $updates[0]['estado'], $updates[0]['id']]
                );
            } else {
                // Bolt Optimization: Consolidate multi-lot updates into 1 batched UPDATE query using CASE statements (O(1) DB round-trips)
                $caseStock = "CASE id";
                $caseEstado = "CASE id";
                $stockParams = [];
                $estadoParams = [];
                $ids = [];

                foreach ($updates as $upd) {
                    $caseStock .= " WHEN ? THEN ?";
                    $stockParams[] = $upd['id'];
                    $stockParams[] = $upd['cantidad_actual'];

                    $caseEstado .= " WHEN ? THEN ?";
                    $estadoParams[] = $upd['id'];
                    $estadoParams[] = $upd['estado'];

                    $ids[] = $upd['id'];
                }

                $caseStock .= " END";
                $caseEstado .= " END";

                $placeholders = implode(',', array_fill(0, count($ids), '?'));
                $sqlBatch = "UPDATE {$this->table} SET cantidad_actual = $caseStock, estado = $caseEstado WHERE id IN ($placeholders)";
                $params = array_merge($stockParams, $estadoParams, $ids);

                $this->db->execute($sqlBatch, $params);
            }
        }

        return $pendiente; // Si es 0, se descontó todo correctamente
    }
}
