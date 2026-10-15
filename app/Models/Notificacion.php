<?php

namespace App\Models;

class Notificacion extends BaseModel
{
    protected string $table = 'notificaciones';

    public function crear($sucursal_id, $tipo, $titulo, $mensaje, $referencia_tipo = null, $referencia_id = null, $usuario_id = null)
    {
        $sql = "INSERT INTO {$this->table} 
                (id_usuario, id_sucursal, tipo, titulo, mensaje, referencia_tipo, referencia_id)
                VALUES (?, ?, ?, ?, ?, ?, ?)";

        return $this->db->execute($sql, [
            $usuario_id,
            $sucursal_id,
            $tipo,
            $titulo,
            $mensaje,
            $referencia_tipo,
            $referencia_id
        ]);
    }

    /**
     * Bolt Optimization: Selects only necessary fields instead of SELECT *
     * and applies a LIMIT to optimize payload size and performance for 30s AJAX polling.
     */
    public function getNoLeidas($sucursal_id, $usuario_id = null, int $limit = 50)
    {
        $limit = max(1, $limit);

        $sql = "SELECT id, tipo, titulo, mensaje, referencia_tipo, referencia_id, fecha_creacion
                FROM {$this->table}
                WHERE id_sucursal = ? 
                AND leida = 0";

        $params = [$sucursal_id];

        if ($usuario_id) {
            $sql .= " AND (id_usuario IS NULL OR id_usuario = ?)";
            $params[] = $usuario_id;
        }

        $sql .= " ORDER BY fecha_creacion DESC LIMIT " . (int)$limit;

        return $this->db->fetchAll($sql, $params);
    }

    public function marcarComoLeida($id)
    {
        $sql = "UPDATE {$this->table} SET leida = 1 WHERE id = ?";
        return $this->db->execute($sql, [$id]);
    }

    public function marcarTodasComoLeidas($sucursal_id, $usuario_id = null)
    {
        $sql = "UPDATE {$this->table} SET leida = 1 WHERE id_sucursal = ?";
        $params = [$sucursal_id];

        if ($usuario_id) {
            $sql .= " AND (id_usuario IS NULL OR id_usuario = ?)";
            $params[] = $usuario_id;
        }

        return $this->db->execute($sql, $params);
    }

    public function verificarStockBajo()
    {
        $sql = "CALL sp_verificar_stock_bajo()";
        return $this->db->fetchOne($sql);
    }

    public function countNoLeidas($sucursal_id, $usuario_id = null)
    {
        $sql = "SELECT COUNT(*) as total FROM {$this->table} 
                WHERE id_sucursal = ? AND leida = 0";

        $params = [$sucursal_id];

        if ($usuario_id) {
            $sql .= " AND (id_usuario IS NULL OR id_usuario = ?)";
            $params[] = $usuario_id;
        }

        $result = $this->db->fetchOne($sql, $params);
        return $result['total'] ?? 0;
    }
}
