<?php

namespace App\Models;

use App\Models\BaseModel;
use PDO;

class Caja extends BaseModel
{
    protected $table = 'cajas';

    /**
     * Caching en memoria a nivel de request (Optimización Bolt)
     * @var array<int, array|null>
     */
    private static $activeCajaCache = [];

    /**
     * Obtiene la caja activa para una sucursal
     * Optimización Bolt: Implementa caché a nivel de request para evitar consultas redundantes.
     */
    public function getActiva($id_sucursal)
    {
        if (array_key_exists($id_sucursal, self::$activeCajaCache)) {
            return self::$activeCajaCache[$id_sucursal];
        }

        $sql = "SELECT * FROM {$this->table} WHERE id_sucursal = ? AND estado = 'abierta' LIMIT 1";
        $result = $this->db->fetchOne($sql, [$id_sucursal]);

        self::$activeCajaCache[$id_sucursal] = $result ?: null;
        return $result;
    }

    /**
     * Abre una nueva caja con soporte multimoneda
     */
    public function abrir($id_sucursal, $id_usuario, $monto_usd, $monto_bs, $tasa)
    {
        $total_usd = $monto_usd + ($monto_bs / $tasa);

        $result = $this->create([
            'id_sucursal' => $id_sucursal,
            'id_usuario_apertura' => $id_usuario,
            'monto_apertura' => $total_usd,
            'monto_apertura_usd' => $monto_usd,
            'monto_apertura_bs' => $monto_bs,
            'estado' => 'abierta',
            'fecha_apertura' => date('Y-m-d H:i:s')
        ]);

        if ($result) {
            unset(self::$activeCajaCache[$id_sucursal]);
        }

        return $result;
    }

    /**
     * Obtiene el resumen de una caja (totales de ingresos y egresos)
     * Optimización Bolt: Consolida la obtención del monto de apertura y los totales de movimientos en una sola consulta.
     */
    public function getResumen($id_caja)
    {
        $sql = "SELECT 
                    c.monto_apertura,
                    SUM(CASE WHEN m.tipo = 'ingreso' THEN m.monto ELSE 0 END) as ingresos,
                    SUM(CASE WHEN m.tipo = 'egreso' THEN m.monto ELSE 0 END) as egresos
                FROM {$this->table} c
                LEFT JOIN caja_movimientos m ON c.id = m.id_caja
                WHERE c.id = ?
                GROUP BY c.id";

        $res = $this->db->fetchOne($sql, [$id_caja]);

        $monto_apertura = $res['monto_apertura'] ?? 0;
        $ingresos = $res['ingresos'] ?? 0;
        $egresos = $res['egresos'] ?? 0;

        $resumen = [
            'apertura' => $monto_apertura,
            'ingresos' => $ingresos,
            'egresos' => $egresos,
            'esperado' => $monto_apertura + $ingresos - $egresos
        ];

        return $resumen;
    }

    /**
     * Cierra una caja con soporte multimoneda
     */
    public function cerrar($id_caja, $id_usuario, $monto_usd, $monto_bs, $tasa, $observaciones = '')
    {
        $caja = $this->find($id_caja);
        $resumen = $this->getResumen($id_caja);
        $total_cierre_usd = $monto_usd + ($monto_bs / $tasa);

        $result = $this->update($id_caja, [
            'id_usuario_cierre' => $id_usuario,
            'monto_cierre' => $total_cierre_usd,
            'monto_cierre_usd' => $monto_usd,
            'monto_cierre_bs' => $monto_bs,
            'monto_esperado' => $resumen['esperado'],
            'estado' => 'cerrada',
            'fecha_cierre' => date('Y-m-d H:i:s'),
            'observaciones' => $observaciones
        ]);

        if ($result && $caja) {
            unset(self::$activeCajaCache[$caja['id_sucursal']]);
        }

        return $result;
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
