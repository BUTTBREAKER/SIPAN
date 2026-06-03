<?php

namespace App\Models;

use App\Models\BaseModel;
use PDO;

class Caja extends BaseModel
{
    protected $table = 'cajas';

    /**
     * Cache de la caja activa por sucursal (Optimización Bolt: Request-level caching)
     * @var array
     */
    private static $activeCajaCache = [];

    /**
     * Obtiene la caja activa para una sucursal
     */
    public function getActiva($id_sucursal)
    {
        if (array_key_exists($id_sucursal, self::$activeCajaCache)) {
            return self::$activeCajaCache[$id_sucursal];
        }

        $sql = "SELECT * FROM {$this->table} WHERE id_sucursal = ? AND estado = 'abierta' LIMIT 1";
        $result = $this->db->fetchOne($sql, [$id_sucursal]);

        self::$activeCajaCache[$id_sucursal] = $result;
        return $result;
    }

    /**
     * Abre una nueva caja con soporte multimoneda
     */
    public function abrir($id_sucursal, $id_usuario, $monto_usd, $monto_bs, $tasa)
    {
        $total_usd = $monto_usd + ($monto_bs / $tasa);

        $id = $this->create([
            'id_sucursal' => $id_sucursal,
            'id_usuario_apertura' => $id_usuario,
            'monto_apertura' => $total_usd,
            'monto_apertura_usd' => $monto_usd,
            'monto_apertura_bs' => $monto_bs,
            'estado' => 'abierta',
            'fecha_apertura' => date('Y-m-d H:i:s')
        ]);

        if ($id) {
            unset(self::$activeCajaCache[$id_sucursal]);
        }

        return $id;
    }

    /**
     * Obtiene el resumen de una caja (totales de ingresos y egresos)
     * Optimización Bolt: Consolidación de consultas (JOIN) para reducir round-trips de 2 a 1.
     */
    public function getResumen($id_caja)
    {
        $sql = "SELECT 
                    c.monto_apertura,
                    SUM(CASE WHEN cm.tipo = 'ingreso' THEN cm.monto ELSE 0 END) as ingresos,
                    SUM(CASE WHEN cm.tipo = 'egreso' THEN cm.monto ELSE 0 END) as egresos
                FROM {$this->table} c
                LEFT JOIN caja_movimientos cm ON c.id = cm.id_caja
                WHERE c.id = ?
                GROUP BY c.id";

        $res = $this->db->fetchOne($sql, [$id_caja]);

        if (!$res) {
            return [
                'apertura' => 0.0,
                'ingresos' => 0.0,
                'egresos' => 0.0,
                'esperado' => 0.0
            ];
        }

        $monto_apertura = (float)$res['monto_apertura'];
        $ingresos = (float)$res['ingresos'];
        $egresos = (float)$res['egresos'];

        return [
            'apertura' => $monto_apertura,
            'ingresos' => $ingresos,
            'egresos' => $egresos,
            'esperado' => $monto_apertura + $ingresos - $egresos
        ];
    }

    /**
     * Cierra una caja con soporte multimoneda
     */
    public function cerrar($id_caja, $id_usuario, $monto_usd, $monto_bs, $tasa, $observaciones = '')
    {
        $resumen = $this->getResumen($id_caja);
        $total_cierre_usd = $monto_usd + ($monto_bs / $tasa);

        $caja = $this->find($id_caja);
        $id_sucursal = $caja['id_sucursal'] ?? null;

        $success = $this->update($id_caja, [
            'id_usuario_cierre' => $id_usuario,
            'monto_cierre' => $total_cierre_usd,
            'monto_cierre_usd' => $monto_usd,
            'monto_cierre_bs' => $monto_bs,
            'monto_esperado' => $resumen['esperado'],
            'estado' => 'cerrada',
            'fecha_cierre' => date('Y-m-d H:i:s'),
            'observaciones' => $observaciones
        ]);

        if ($success && $id_sucursal) {
            unset(self::$activeCajaCache[$id_sucursal]);
        }

        return $success;
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
