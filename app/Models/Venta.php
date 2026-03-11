<?php

namespace App\Models;

class Venta extends BaseModel
{
    protected $table = 'ventas';

    public function createWithProducts($venta_data, $productos, $pagos = [])
    {
        try {
            $this->db->beginTransaction();

            // 1. Validar stock en lote (Optimización Bolt)
            if (!empty($productos)) {
                // Agrupar cantidades por ID de producto (por si vienen duplicados en el input)
                $cantidadesRequeridas = [];
                foreach ($productos as $p) {
                    $id = $p['id_producto'];
                    $cantidadesRequeridas[$id] = ($cantidadesRequeridas[$id] ?? 0) + $p['cantidad'];
                }

                $productIds = array_keys($cantidadesRequeridas);
                $placeholders = implode(',', array_fill(0, count($productIds), '?'));
                $sql_stock = "SELECT id, nombre, stock_actual FROM productos WHERE id IN ($placeholders)";
                $stocks_db = $this->db->fetchAll($sql_stock, $productIds);

                $stocksMap = array_column($stocks_db, 'stock_actual', 'id');
                $nombresMap = array_column($stocks_db, 'nombre', 'id');

                foreach ($cantidadesRequeridas as $id => $cantidad) {
                    if (!isset($stocksMap[$id])) {
                        throw new \Exception("Producto con ID $id no encontrado");
                    }
                    if ($stocksMap[$id] < $cantidad) {
                        throw new \Exception("Stock insuficiente para el producto: " . ($nombresMap[$id] ?? $id));
                    }
                }
            }

            // Crear venta maestra
            // Si hay múltiples pagos, podemos poner 'mixto' o el primero en el campo legacy
            $venta_id = $this->create($venta_data);

            if (!$venta_id) {
                throw new \Exception("Error al crear la venta");
            }

            // 2. Registrar Pagos (Batch Insert - Optimización Bolt)
            $pagos_insert = [];
            $pagos_params = [];

            if (!empty($pagos)) {
                foreach ($pagos as $pago) {
                    $pagos_insert[] = "(?, ?, ?, ?)";
                    $pagos_params[] = $venta_id;
                    $pagos_params[] = $pago['metodo'];
                    $pagos_params[] = $pago['monto'];
                    $pagos_params[] = $pago['referencia'] ?? null;
                }
            } else {
                // Compatibilidad hacia atrás
                $pagos_insert[] = "(?, ?, ?, ?)";
                $pagos_params[] = $venta_id;
                $pagos_params[] = $venta_data['metodo_pago'];
                $pagos_params[] = $venta_data['total'];
                $pagos_params[] = null;
            }

            $sql_pago = "INSERT INTO venta_pagos (id_venta, metodo_pago, monto, referencia) VALUES " . implode(', ', $pagos_insert);
            $this->db->execute($sql_pago, $pagos_params);

            // 3. Agregar productos (Batch Insert - Optimización Bolt)
            // No actualizamos stock manualmente ya que el trigger tr_actualizar_stock_venta lo hace
            if (!empty($productos)) {
                $prod_insert = [];
                $prod_params = [];
                foreach ($productos as $producto) {
                    $prod_insert[] = "(?, ?, ?, ?, ?)";
                    $prod_params[] = $venta_id;
                    $prod_params[] = $producto['id_producto'];
                    $prod_params[] = $producto['cantidad'];
                    $prod_params[] = $producto['precio_unitario'];
                    $prod_params[] = $producto['subtotal'];
                }

                $sql_prod = "INSERT INTO venta_productos (id_venta, id_producto, cantidad, precio_unitario, subtotal) VALUES " . implode(', ', $prod_insert);
                $this->db->execute($sql_prod, $prod_params);
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

        if ($fecha_inicio) {
            $sql .= " AND DATE(v.fecha_venta) >= ?";
            $params[] = $fecha_inicio;
        }

        if ($fecha_fin) {
            $sql .= " AND DATE(v.fecha_venta) <= ?";
            $params[] = $fecha_fin;
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
            $sql .= " AND DATE(fecha_venta) >= ?";
            $params[] = $fecha_inicio;
        }

        if ($fecha_fin) {
            $sql .= " AND DATE(fecha_venta) <= ?";
            $params[] = $fecha_fin;
        }

        $result = $this->db->fetchOne($sql, $params);
        return $result['total'] ?? 0;
    }

    public function getByDateRange($sucursal_id, $fecha_inicio, $fecha_fin)
    {
        $sql = "SELECT v.*, 
                       CONCAT(COALESCE(c.nombre, ''), ' ', COALESCE(c.apellido, '')) as cliente_nombre,
                       CONCAT(u.primer_nombre, ' ', u.apellido_paterno) as usuario_nombre
                FROM {$this->table} v
                LEFT JOIN clientes c ON v.id_cliente = c.id
                LEFT JOIN usuarios u ON v.id_usuario = u.id
                WHERE v.id_sucursal = ? AND DATE(v.fecha_venta) BETWEEN ? AND ?
                ORDER BY v.fecha_venta DESC";

        return $this->db->fetchAll($sql, [$sucursal_id, $fecha_inicio, $fecha_fin]);
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

        // Asegurar que todos los días estén presentes
        // Optimización Bolt: Usar hash map (O(N+M)) en lugar de bucle anidado (O(N*M))
        $indexedResults = array_column($result, 'total', 'fecha');
        $ventas = [];

        for ($i = $dias - 1; $i >= 0; $i--) {
            $fecha = date('Y-m-d', strtotime("-$i days"));
            $ventas[] = [
                'fecha' => date('d/m', strtotime($fecha)),
                'total' => isset($indexedResults[$fecha]) ? (float)$indexedResults[$fecha] : 0.0
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

        // Llenar huecos de días sin ventas
        // Empezar desde hace $dias hasta hoy
        // La lógica del bucle anterior era backwards, aquí lo hacemos igual para mantener consistencia
        // Optimización Bolt: Usar hash map (O(N+M)) en lugar de bucle anidado (O(N*M))
        $indexedResults = array_column($result, 'total', 'fecha');
        $ventas = [];

        for ($i = $dias - 1; $i >= 0; $i--) {
            $fecha = date('Y-m-d', strtotime("-$i days"));
            $ventas[] = [
                'fecha' => $fecha,
                'total' => isset($indexedResults[$fecha]) ? (float)$indexedResults[$fecha] : 0.0
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
