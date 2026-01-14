<?php

namespace App\Models;

use App\Models\BaseModel;
use PDO;

class Caja extends BaseModel
{
    protected $table = 'cajas';

    /**
     * Obtiene la caja activa para una sucursal
     */
    public function getActiva($id_sucursal)
    {
        $sql = "SELECT * FROM {$this->table} WHERE id_sucursal = ? AND estado = 'abierta' LIMIT 1";
        return $this->db->fetchOne($sql, [$id_sucursal]);
    }

    /**
     * Abre una nueva caja con soporte multimoneda
     */
    public function abrir($id_sucursal, $id_usuario, $monto_usd, $monto_bs, $tasa)
    {
        $total_usd = $monto_usd + ($monto_bs / $tasa);

        return $this->create([
            'id_sucursal' => $id_sucursal,
            'id_usuario_apertura' => $id_usuario,
            'monto_apertura' => $total_usd,
            'monto_apertura_usd' => $monto_usd,
            'monto_apertura_bs' => $monto_bs,
            'estado' => 'abierta',
            'fecha_apertura' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Obtiene el resumen de una caja (totales de ingresos y egresos)
     */
    public function getResumen($id_caja)
    {
        $sql = "SELECT 
                    SUM(CASE WHEN tipo = 'ingreso' THEN monto ELSE 0 END) as ingresos,
                    SUM(CASE WHEN tipo = 'egreso' THEN monto ELSE 0 END) as egresos
                FROM caja_movimientos 
                WHERE id_caja = ?";
        $res = $this->db->fetchOne($sql, [$id_caja]);

        $caja = $this->find($id_caja);
        $monto_apertura = $caja['monto_apertura'] ?? 0;

        $resumen = [
            'apertura' => $monto_apertura,
            'ingresos' => $res['ingresos'] ?? 0,
            'egresos' => $res['egresos'] ?? 0,
            'esperado' => $monto_apertura + ($res['ingresos'] ?? 0) - ($res['egresos'] ?? 0)
        ];

        return $resumen;
    }

    /**
     * Cierra una caja con soporte multimoneda
     */
    public function cerrar($id_caja, $id_usuario, $monto_usd, $monto_bs, $tasa, $observaciones = '')
    {
        $resumen = $this->getResumen($id_caja);
        $total_cierre_usd = $monto_usd + ($monto_bs / $tasa);

        return $this->update($id_caja, [
            'id_usuario_cierre' => $id_usuario,
            'monto_cierre' => $total_cierre_usd,
            'monto_cierre_usd' => $monto_usd,
            'monto_cierre_bs' => $monto_bs,
            'monto_esperado' => $resumen['esperado'],
            'estado' => 'cerrada',
            'fecha_cierre' => date('Y-m-d H:i:s'),
            'observaciones' => $observaciones
        ]);
    }

    /**
     * Registra un movimiento en la caja
     */
    public function addMovimiento($id_caja, $tipo, $monto, $descripcion, $metodo_pago = 'efectivo', $id_venta = null)
    {
        $sql = "INSERT INTO caja_movimientos (id_caja, tipo, monto, descripcion, metodo_pago, id_venta, fecha) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        return $this->db->execute($sql, [
            $id_caja, $tipo, $monto, $descripcion, $metodo_pago, $id_venta, date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Obtiene todos los movimientos de una caja
     */
    public function getMovimientos($id_caja)
    {
        $sql = "SELECT * FROM caja_movimientos WHERE id_caja = ? ORDER BY fecha DESC";
        return $this->db->fetchAll($sql, [$id_caja]);
    }

    /**
     * Obtiene el historial de cajas de una sucursal
     */
    public function getHistorial($id_sucursal, $limit = 10)
    {
        $sql = "SELECT c.*, 
                u1.primer_nombre as usuario_apertura, 
                u2.primer_nombre as usuario_cierre 
                FROM {$this->table} c
                JOIN usuarios u1 ON c.id_usuario_apertura = u1.id
                LEFT JOIN usuarios u2 ON c.id_usuario_cierre = u2.id
                WHERE c.id_sucursal = ? 
                ORDER BY c.fecha_apertura DESC 
                LIMIT ?";
        return $this->db->fetchAll($sql, [$id_sucursal, $limit]);
    }
}
