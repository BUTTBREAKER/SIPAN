<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\BaseModel;
use PDOException;

final class Caja extends BaseModel
{
    protected string $table = 'cajas';

    /**
     * Obtiene la caja activa para una sucursal
     * @return false|array{
     *   id: int,
     *   id_sucursal: int,
     *   id_usuario_apertura: int,
     *   id_usuario_cierre: ?int,
     *   monto_apertura: float,
     *   monto_apertura_usd: float,
     *   monto_apertura_bs: float,
     *   monto_cierre: ?float,
     *   monto_cierre_usd: ?float,
     *   monto_cierre_bs: ?float,
     *   monto_esperado: ?float,
     *   monto_esperado_usd: ?float,
     *   monto_esperado_bs: ?float,
     *   estado: 'abierta'|'cerrada',
     *   fecha_apertura: string,
     *   fecha_cierre: ?string,
     *   observaciones: ?string,
     *   created_at: string,
     *   updated_at: string,
     * }
     * @throws PDOException
     */
    public function getActiva(int $id_sucursal): false|array
    {
        $sql = "SELECT * FROM $this->table WHERE id_sucursal = ? AND estado = 'abierta' LIMIT 1";

        return $this->db->fetchOne($sql, [$id_sucursal]);
    }

    /**
     * Abre una nueva caja con soporte multimoneda
     * @throws PDOException
     */
    public function abrir(
        int $id_sucursal,
        int $id_usuario,
        float $monto_usd,
        float $monto_bs,
        float $tasa,
    ): string|false {
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
     * @return array{
     *   apertura: float,
     *   ingresos: float,
     *   egresos: float,
     *   esperado: float,
     * }
     * @throws PDOException
     */
    public function getResumen(int $id_caja): array
    {
        $sql = "
            SELECT
            SUM(CASE WHEN tipo = 'ingreso' THEN monto ELSE 0 END) ingresos,
            SUM(CASE WHEN tipo = 'egreso' THEN monto ELSE 0 END) egresos
            FROM caja_movimientos
            WHERE id_caja = ?
        ";

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
     * @throws PDOException
     */
    public function cerrar(
        int $id_caja,
        int $id_usuario,
        float $monto_usd,
        float $monto_bs,
        float $tasa,
        string $observaciones = '',
    ): int {
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
            'observaciones' => $observaciones,
        ]);
    }

    /**
     * Registra un movimiento en la caja
     * @param 'ingreso'|'egreso' $tipo
     * @throws PDOException
     */
    public function addMovimiento(
        int $id_caja,
        string $tipo,
        float $monto,
        string $descripcion,
        string $metodo_pago = 'efectivo',
        ?int $id_venta = null,
    ): int {
        $sql = '
            INSERT INTO caja_movimientos (id_caja, tipo, monto, descripcion, metodo_pago, id_venta, fecha)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ';

        return $this->db->execute($sql, [
            $id_caja,
            $tipo,
            $monto,
            $descripcion,
            $metodo_pago,
            $id_venta,
            date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Obtiene todos los movimientos de una caja
     * @return list<array{
     *   id: int,
     *   id_caja: int,
     *   tipo: 'ingreso'|'egreso',
     *   monto: float,
     *   descripcion: string,
     *   metodo_pago: ?string,
     *   id_venta: ?int,
     *   fecha: string,
     * }>
     * @throws PDOException
     */
    public function getMovimientos(int $id_caja): array
    {
        $sql = 'SELECT * FROM caja_movimientos WHERE id_caja = ? ORDER BY fecha DESC';

        return $this->db->fetchAll($sql, [$id_caja]);
    }

    /**
     * Obtiene el historial de cajas de una sucursal
     * @return list<array{
     *   id: int,
     *   id_sucursal: int,
     *   id_usuario_apertura: int,
     *   id_usuario_cierre: ?int,
     *   monto_apertura: float,
     *   monto_apertura_usd: float,
     *   monto_apertura_bs: float,
     *   monto_cierre: ?float,
     *   monto_cierre_usd: ?float,
     *   monto_cierre_bs: ?float,
     *   monto_esperado: ?float,
     *   monto_esperado_usd: ?float,
     *   monto_esperado_bs: ?float,
     *   estado: 'abierta'|'cerrada',
     *   fecha_apertura: string,
     *   fecha_cierre: ?string,
     *   observaciones: ?string,
     *   created_at: string,
     *   updated_at: string,
     *   usuario_apertura: string,
     *   usuario_cierre: string,
     * }>
     * @throws PDOException
     */
    public function getHistorial(int $id_sucursal, int $limit = 10): array
    {
        $sql = "
            SELECT c.*,
            u1.primer_nombre usuario_apertura,
            u2.primer_nombre usuario_cierre
            FROM $this->table c
            JOIN usuarios u1 ON c.id_usuario_apertura = u1.id
            LEFT JOIN usuarios u2 ON c.id_usuario_cierre = u2.id
            WHERE c.id_sucursal = ?
            ORDER BY c.fecha_apertura DESC
            LIMIT ?
        ";

        return $this->db->fetchAll($sql, [$id_sucursal, $limit]);
    }
}
