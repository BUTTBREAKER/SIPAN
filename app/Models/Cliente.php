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
        // Optimización Bolt: Consulta unificada con LEFT JOIN y COALESCE para evitar dependencia de vistas inexistentes y retornos NULL
        $sql = "SELECT c.*,
                       COUNT(p.id) as total_pedidos,
                       COALESCE(SUM(p.total), 0) as total_comprado,
                       COALESCE(SUM(p.monto_pagado), 0) as total_pagado,
                       COALESCE(SUM(p.monto_deuda), 0) as total_deuda
                FROM {$this->table} c
                LEFT JOIN pedidos p ON c.id = p.id_cliente";

        $params = [];
        if ($sucursal_id !== null) {
            $sql .= " WHERE c.id_sucursal = ?";
            $params[] = $sucursal_id;
        }

        $sql .= " GROUP BY c.id ORDER BY c.nombre";

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
}
