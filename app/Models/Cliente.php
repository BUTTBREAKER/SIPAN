<?php

declare(strict_types=1);

namespace App\Models;

use PDOException;

final class Cliente extends BaseModel
{
    protected string $table = 'clientes';

    /**
     * @return list<array{
     *   id: int,
     *   id_sucursal: ?int,
     *   nombre: string,
     *   apellido: string,
     *   documento_tipo: 'DNI'|'RUC'|'CE'|'Pasaporte',
     *   documento_number: ?string,
     *   telefono: ?string,
     *   correo: ?string,
     *   direccion: ?string,
     *   estado: 'activo'|'inactivo',
     *   fecha_registro: string,
     * }>
     * @throws PDOException
     */
    public function search(string $search, ?int $sucursal_id = null): array
    {
        $sql = "SELECT * FROM $this->table
                WHERE (nombre LIKE ? OR apellido LIKE ? OR documento_numero LIKE ? OR telefono LIKE ?)";

        $params = ["%{$search}%", "%{$search}%", "%{$search}%", "%{$search}%"];

        if ($sucursal_id) {
            $sql .= " AND id_sucursal = ?";
            $params[] = $sucursal_id;
        }

        $sql .= " ORDER BY nombre";

        return $this->db->fetchAll($sql, $params);
    }

    /**
     * @return list<array{
     *   id: int,
     *   id_sucursal: ?int,
     *   nombre: string,
     *   apellido: string,
     *   documento_tipo: 'DNI'|'RUC'|'CE'|'Pasaporte',
     *   documento_numero: ?string,
     *   telefono: ?string,
     *   correo: ?string,
     *   direccion: ?string,
     *   estado: 'activo'|'inactivo',
     *   fecha_registro: string,
     *   total_pedidos: int,
     *   total_comprado: float,
     *   total_pagado: float,
     *   total_deuda: float,
     * }>
     * @throws PDOException
     */
    public function getWithResumen(?int $sucursal_id = null): array
    {
        $sql = "SELECT * FROM v_resumen_pedidos_cliente";

        if ($sucursal_id) {
            // Filtrar por sucursal si es necesario
            $sql = "SELECT c.*, 
                           COUNT(p.id) total_pedidos,
                           SUM(p.total) total_comprado,
                           SUM(p.monto_pagado) total_pagado,
                           SUM(p.monto_deuda) total_deuda
                    FROM {$this->table} c
                    LEFT JOIN pedidos p ON c.id = p.id_cliente
                    WHERE c.id_sucursal = ?
                    GROUP BY c.id
                    ORDER BY c.nombre";

            return $this->db->fetchAll($sql, [$sucursal_id]);
        }

        return $this->db->fetchAll($sql);
    }

    /**
     * @return list<array{
     *   id: int,
     *   id_sucursal: ?int,
     *   nombre: string,
     *   apellido: string,
     *   documento_tipo: 'DNI'|'RUC'|'CE'|'Pasaporte',
     *   documento_numero: ?string,
     *   telefono: ?string,
     *   correo: ?string,
     *   direccion: ?string,
     *   estado: 'activo'|'inactivo',
     *   fecha_registro: string,
     * }>
     * @throws PDOException
     */
    public function getBySucursal(int $sucursal_id): array
    {
        $sql = "SELECT * FROM clientes WHERE id_sucursal = ?";

        return $this->db->fetchAll($sql, [$sucursal_id]);
    }

    /**
     * Obtiene los clientes con sus estadísticas de compra (total de ventas y monto total)
     * Optimización Bolt: Evita N+1 queries al traer estadísticas en una sola consulta
     * @return list<array{
     *   id: int,
     *   id_sucursal: ?int,
     *   nombre: string,
     *   apellido: string,
     *   documento_tipo: 'DNI'|'RUC'|'CE'|'Pasaporte',
     *   documento_numero: ?string,
     *   telefono: ?string,
     *   correo: ?string,
     *   direccion: ?string,
     *   estado: 'activo'|'inactivo',
     *   fecha_registro: string,
     *   total_compras: int,
     *   monto_total: float,
     * }>
     * @throws PDOException
     */
    public function getBySucursalWithStats(?int $sucursal_id): array
    {
        $sql = "SELECT c.*,
                       COUNT(v.id) as total_compras,
                       COALESCE(SUM(v.total), 0) as monto_total
                FROM {$this->table} c
                LEFT JOIN ventas v ON c.id = v.id_cliente
                WHERE c.id_sucursal = ?
                GROUP BY c.id
                ORDER BY c.nombre ASC";

        return $this->db->fetchAll($sql, [$sucursal_id]);
    }

    public function getStatsByDateRange(int $sucursal_id, string $fecha_inicio, string $fecha_fin): array
    {
        $sql = "SELECT c.*,
                       COUNT(DISTINCT v.id) as total_compras,
                       COALESCE(SUM(v.total), 0) as monto_periodo,
                       COUNT(DISTINCT pe.id) as total_pedidos,
                       COALESCE(SUM(pe.total), 0) as monto_pedidos,
                       COALESCE(SUM(pe.monto_deuda), 0) as total_deuda,
                       MAX(GREATEST(
                           COALESCE(v.fecha_venta, '2000-01-01'),
                           COALESCE(pe.fecha_pedido, '2000-01-01')
                       )) as ultima_actividad
                FROM {$this->table} c
                LEFT JOIN ventas v ON c.id = v.id_cliente 
                     AND v.fecha_venta >= ? AND v.fecha_venta <= ?
                     AND v.estado = 'completada'
                LEFT JOIN pedidos pe ON c.id = pe.id_cliente 
                     AND pe.fecha_pedido >= ? AND pe.fecha_pedido <= ?
                WHERE c.id_sucursal = ?
                GROUP BY c.id
                ORDER BY monto_periodo DESC, monto_pedidos DESC, c.nombre ASC";

        return $this->db->fetchAll($sql, [
            $fecha_inicio . ' 00:00:00', $fecha_fin . ' 23:59:59',
            $fecha_inicio . ' 00:00:00', $fecha_fin . ' 23:59:59',
            $sucursal_id
        ]);
    }
}
