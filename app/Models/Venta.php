<?php

namespace App\Models;

class Venta extends BaseModel
{
    protected $table = 'ventas';

    public function createWithProducts($venta_data, $productos, $pagos = [])
    {
        if (empty($productos)) {
            throw new \Exception("Debe agregar al menos un producto");
        }

        try {
            $this->db->beginTransaction();

            // Validar stock antes de crear la venta (Optimización Bolt: Batch query para reducir round-trips)
            $cantidadesPorProducto = [];
            foreach ($productos as $producto) {
                $id = $producto['id_producto'];
                $cantidadesPorProducto[$id] = ($cantidadesPorProducto[$id] ?? 0) + $producto['cantidad'];
            }

            $productIds = array_keys($cantidadesPorProducto);
            if (!empty($productIds)) {
                $placeholders = implode(',', array_fill(0, count($productIds), '?'));
                $sql_stock = "SELECT id, nombre, stock_actual FROM productos WHERE id IN ($placeholders)";
                $productos_db = $this->db->fetchAll($sql_stock, $productIds);
                $stockMap = array_column($productos_db, null, 'id');

                foreach ($cantidadesPorProducto as $id => $cantidad_solicitada) {
                    if (!isset($stockMap[$id])) {
                        throw new \Exception("Producto con ID {$id} no encontrado");
                    }

                    if ($stockMap[$id]['stock_actual'] < $cantidad_solicitada) {
                        throw new \Exception("Stock insuficiente para el producto: " . $stockMap[$id]['nombre'] . ". Solicitado: $cantidad_solicitada, Disponible: " . $stockMap[$id]['stock_actual']);
                    }
                }
            }

            // Crear venta maestra
            $venta_id = $this->create($venta_data);

            if (!$venta_id) {
                throw new \Exception("Error al crear la venta");
            }

            // Optimización Bolt: Batch Payment Insertion (1 query en lugar de N)
            if (!empty($pagos)) {
                $pago_values = [];
                $pago_params = [];
                foreach ($pagos as $pago) {
                    $pago_values[] = "(?, ?, ?, ?)";
                    $pago_params[] = $venta_id;
                    $pago_params[] = $pago['metodo'];
                    $pago_params[] = $pago['monto'];
                    $pago_params[] = $pago['referencia'] ?? null;
                }
                $sql_pago = "INSERT INTO venta_pagos (id_venta, metodo_pago, monto, referencia) VALUES " . implode(', ', $pago_values);
                $this->db->execute($sql_pago, $pago_params);
            } else {
                $sql_pago = "INSERT INTO venta_pagos (id_venta, metodo_pago, monto, referencia) VALUES (?, ?, ?, ?)";
                $this->db->execute($sql_pago, [
                    $venta_id,
                    $venta_data['metodo_pago'],
                    $venta_data['total'],
                    null
                ]);
            }

            // Bolt: Registro de Detalles por Lotes (Batch Insert)
            // Se elimina la actualización manual de stock ya que el disparador tr_actualizar_stock_venta se encarga de ello automáticamente.
            $productPlaceholders = [];
            $productValues = [];
            foreach ($productos as $producto) {
                $productPlaceholders[] = "(?, ?, ?, ?, ?)";
                array_push($productValues, $venta_id, $producto['id_producto'], $producto['cantidad'], $producto['precio_unitario'], $producto['subtotal']);
            }

            if (!empty($productPlaceholders)) {
                $sql_productos = "INSERT INTO venta_productos (id_venta, id_producto, cantidad, precio_unitario, subtotal) VALUES " . implode(', ', $productPlaceholders);
                $this->db->execute($sql_productos, $productValues);
            }

            $this->db->commit();
            return $venta_id;
        } catch (\Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }
    public function getProductos($venta_id)
    {
        $sql = "SELECT vp.*, p.nombre as producto_nombre
                FROM venta_productos vp
                INNER JOIN productos p ON vp.id_producto = p.id
                WHERE vp.id_venta = ?";
        return $this->db->fetchAll($sql, [$venta_id]);
    }

    public function getPagos($venta_id)
    {
        $sql = "SELECT * FROM venta_pagos WHERE id_venta = ?";
        return $this->db->fetchAll($sql, [$venta_id]);
    }

    /**
     * Obtiene los pagos de múltiples ventas en una sola consulta (Optimización Bolt)
     *
     * @param array $venta_ids
     * @return array
     */
    public function getPagosPorVentas(array $venta_ids)
    {
        if (empty($venta_ids)) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($venta_ids), '?'));
        $sql = "SELECT * FROM venta_pagos WHERE id_venta IN ($placeholders)";

        return $this->db->fetchAll($sql, $venta_ids);
    }

    public function getWithDetails($sucursal_id, $fecha_inicio = null, $fecha_fin = null)
    {
        $sql = "SELECT v.*, 
                       CONCAT(COALESCE(c.nombre, ''), ' ', COALESCE(c.apellido, '')) as cliente_nombre,
                       CONCAT(u.primer_nombre, ' ', u.apellido_paterno) as usuario_nombre,
                       COUNT(vp.id) as total_productos
                FROM {$this->table} v
                INNER JOIN usuarios u ON v.id_usuario = u.id
                LEFT JOIN clientes c ON v.id_cliente = c.id
                LEFT JOIN venta_productos vp ON v.id = vp.id_venta
                WHERE v.id_sucursal = ?";

        $params = [$sucursal_id];

        // Optimization: Use direct timestamp comparison to keep query SARGable (utilizes index on fecha_venta)
        if ($fecha_inicio) {
            $sql .= " AND v.fecha_venta >= ?";
            $params[] = $fecha_inicio . ' 00:00:00';
        }

        if ($fecha_fin) {
            $sql .= " AND v.fecha_venta <= ?";
            $params[] = $fecha_fin . ' 23:59:59';
        }

        $sql .= " GROUP BY v.id ORDER BY v.fecha_venta DESC";

        return $this->db->fetchAll($sql, $params);
    }

    public function getTotalVentas($sucursal_id, $fecha_inicio = null, $fecha_fin = null)
    {
        $sql = "SELECT COALESCE(SUM(total), 0) as total
                FROM {$this->table}
                WHERE id_sucursal = ? AND estado = 'completada'";

        $params = [$sucursal_id];

        if ($fecha_inicio) {
            $sql .= " AND fecha_venta >= ?";
            $params[] = $fecha_inicio . ' 00:00:00';
        }

        if ($fecha_fin) {
            $sql .= " AND fecha_venta <= ?";
            $params[] = $fecha_fin . ' 23:59:59';
        }

        $result = $this->db->fetchOne($sql, $params);
        return $result['total'] ?? 0;
    }

    public function getByDateRange($sucursal_id, $fecha_inicio, $fecha_fin)
    {
        // Optimization: Use direct timestamp comparison to keep query SARGable (utilizes index on fecha_venta)
        $sql = "SELECT v.*, 
                       CONCAT(COALESCE(c.nombre, ''), ' ', COALESCE(c.apellido, '')) as cliente_nombre,
                       CONCAT(u.primer_nombre, ' ', u.apellido_paterno) as usuario_nombre
                FROM {$this->table} v
                LEFT JOIN clientes c ON v.id_cliente = c.id
                LEFT JOIN usuarios u ON v.id_usuario = u.id
                WHERE v.id_sucursal = ? AND v.fecha_venta >= ? AND v.fecha_venta <= ?
                ORDER BY v.fecha_venta DESC";

        return $this->db->fetchAll($sql, [
            $sucursal_id,
            $fecha_inicio . ' 00:00:00',
            $fecha_fin . ' 23:59:59'
        ]);
    }

    public function getClienteStats($cliente_id)
    {
        $sql = "SELECT COUNT(*) as total_compras, COALESCE(SUM(total), 0) as monto_total
                FROM {$this->table}
                WHERE id_cliente = ?";

        return $this->db->fetchOne($sql, [$cliente_id]);
    }

    public function getVentasUltimosDias($sucursal_id, $dias = 7)
    {
        $sql = "SELECT DATE(fecha_venta) as fecha, COALESCE(SUM(total), 0) as total
                FROM {$this->table}
                WHERE id_sucursal = ? AND fecha_venta >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
                GROUP BY DATE(fecha_venta)
                ORDER BY fecha ASC";

        $result = $this->db->fetchAll($sql, [$sucursal_id, $dias]);

        // Indexar resultados por fecha para evitar búsqueda O(N^2)
        $indexedResult = array_column($result, 'total', 'fecha');

        $ventas = [];

        for ($i = $dias - 1; $i >= 0; $i--) {
            $fecha = date('Y-m-d', strtotime("-$i days"));
            $ventas[] = [
                'fecha' => date('d/m', strtotime($fecha)),
                'total' => (float)($indexedResult[$fecha] ?? 0)
            ];
        }

        return $ventas;
    }

    public function getVentasPorPeriodo($sucursal_id, $dias = 30)
    {
        $sql = "SELECT DATE(fecha_venta) as fecha, COALESCE(SUM(total), 0) as total
                FROM {$this->table}
                WHERE id_sucursal = ? AND fecha_venta >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
                GROUP BY DATE(fecha_venta)
                ORDER BY fecha ASC";

        $result = $this->db->fetchAll($sql, [$sucursal_id, $dias]);

        // Indexar resultados por fecha para evitar búsqueda O(N^2)
        $indexedResult = array_column($result, 'total', 'fecha');

        $ventas = [];
        for ($i = $dias - 1; $i >= 0; $i--) {
            $fecha = date('Y-m-d', strtotime("-$i days"));
            $ventas[] = [
                'fecha' => $fecha,
                'total' => (float)($indexedResult[$fecha] ?? 0)
            ];
        }

        return $ventas;
    }

    public function getVentasDiariasPorProducto($sucursal_id, $dias = 30)
    {
        $sql = "SELECT DATE(v.fecha_venta) as fecha, vp.id_producto, SUM(vp.cantidad) as cantidad
                FROM ventas v
                JOIN venta_productos vp ON v.id = vp.id_venta
                WHERE v.id_sucursal = ? 
                  AND v.fecha_venta >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
                  AND v.estado = 'completada'
                GROUP BY DATE(v.fecha_venta), vp.id_producto
                ORDER BY fecha ASC";

        return $this->db->fetchAll($sql, [$sucursal_id, $dias]);
    }

    public function getProductosMasVendidos($sucursal_id, $limit = 5)
    {
        $sql = "SELECT p.nombre, SUM(vp.cantidad) as cantidad
                FROM venta_productos vp
                INNER JOIN {$this->table} v ON vp.id_venta = v.id
                INNER JOIN productos p ON vp.id_producto = p.id
                WHERE v.id_sucursal = ?
                GROUP BY p.id, p.nombre
                ORDER BY cantidad DESC
                LIMIT " . (int)$limit;

        return $this->db->fetchAll($sql, [$sucursal_id]);
    }

    public function getBySucursal($sucursal_id)
    {
        $sql = "SELECT * FROM ventas WHERE id_sucursal = ?";
        return $this->db->fetchAll($sql, [$sucursal_id]);
    }

    public function getVentaConDetalles($id, $sucursal_id)
    {
        $sql = "SELECT v.*, 
            CONCAT(COALESCE(c.nombre, ''), ' ', COALESCE(c.apellido, '')) as cliente_nombre,
            CONCAT(u.primer_nombre, ' ', u.apellido_paterno) as usuario_nombre,
            s.nombre as sucursal_nombre,
            s.direccion as sucursal_direccion,
            s.telefono as sucursal_telefono,
            n.nombre as negocio_nombre,
            n.telefono as negocio_telefono
            FROM {$this->table} v
            LEFT JOIN clientes c ON v.id_cliente = c.id
            INNER JOIN usuarios u ON v.id_usuario = u.id
            INNER JOIN sucursales s ON v.id_sucursal = s.id
            INNER JOIN negocios n ON v.id_negocio = n.id
            WHERE v.id = ? AND v.id_sucursal = ?";

        return $this->db->fetchOne($sql, [$id, $sucursal_id]);
    }
}
