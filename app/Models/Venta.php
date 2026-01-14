<?php

namespace App\Models;

class Venta extends BaseModel
{
    protected $table = 'ventas';

    public function createWithProducts($venta_data, $productos, $pagos = [])
    {
        try {
            $this->db->beginTransaction();

            // Validar stock antes de crear la venta
            foreach ($productos as $producto) {
                $sql_stock = "SELECT stock_actual FROM productos WHERE id = ?";
                $producto_db = $this->db->fetchOne($sql_stock, [$producto['id_producto']]);

                if (!$producto_db) {
                    throw new \Exception("Producto con ID {$producto['id_producto']} no encontrado");
                }

                if ($producto_db['stock_actual'] < $producto['cantidad']) {
                    throw new \Exception("Stock insuficiente para el producto");
                }
            }

            // Crear venta maestra
            // Si hay múltiples pagos, podemos poner 'mixto' o el primero en el campo legacy
            $venta_id = $this->create($venta_data);

            if (!$venta_id) {
                throw new \Exception("Error al crear la venta");
            }

            // Registrar Pagos Múltiples
            if (!empty($pagos)) {
                $sql_pago = "INSERT INTO venta_pagos (id_venta, metodo_pago, monto, referencia) VALUES (?, ?, ?, ?)";
                foreach ($pagos as $pago) {
                    $this->db->execute($sql_pago, [
                    $venta_id,
                    $pago['metodo'],
                    $pago['monto'],
                    $pago['referencia'] ?? null
                    ]);
                }
            } else {
                // Compatibilidad hacia atrás: si no vienen pagos detallados, crear uno con el método global
                $sql_pago = "INSERT INTO venta_pagos (id_venta, metodo_pago, monto, referencia) VALUES (?, ?, ?, ?)";
                $this->db->execute($sql_pago, [
                $venta_id,
                $venta_data['metodo_pago'],
                $venta_data['total'],
                null
                ]);
            }

            // Agregar productos y actualizar stock
            foreach ($productos as $producto) {
                // Insertar detalle de venta
                $sql = "INSERT INTO venta_productos (id_venta, id_producto, cantidad, precio_unitario, subtotal)
                    VALUES (?, ?, ?, ?, ?)";
                $this->db->execute($sql, [
                $venta_id,
                $producto['id_producto'],
                $producto['cantidad'],
                $producto['precio_unitario'],
                $producto['subtotal']
                ]);

                // Actualizar stock del producto
                $sql_update_stock = "UPDATE productos 
                                SET stock_actual = stock_actual - ? 
                                WHERE id = ?";
                $this->db->execute($sql_update_stock, [
                    $producto['cantidad'],
                    $producto['id_producto']
                ]);
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
        $ventas = [];
        for ($i = $dias - 1; $i >= 0; $i--) {
            $fecha = date('Y-m-d', strtotime("-$i days"));
            $encontrado = false;
            foreach ($result as $row) {
                if ($row['fecha'] === $fecha) {
                    $ventas[] = [
                        'fecha' => date('d/m', strtotime($fecha)),
                        'total' => (float)$row['total']
                    ];
                    $encontrado = true;
                    break;
                }
            }
            if (!$encontrado) {
                $ventas[] = [
                    'fecha' => date('d/m', strtotime($fecha)),
                    'total' => 0
                ];
            }
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
        $ventas = [];
        // Empezar desde hace $dias hasta ayer (o hoy)
        // La lógica del bucle anterior era backwards, aquí lo hacemos igual para mantener consistencia
        // pero devolviendo formato Y-m-d para el helper

        for ($i = $dias - 1; $i >= 0; $i--) {
            $fecha = date('Y-m-d', strtotime("-$i days"));
            $encontrado = false;
            foreach ($result as $row) {
                if ($row['fecha'] === $fecha) {
                    $ventas[] = [
                        'fecha' => $fecha,
                        'total' => (float)$row['total']
                    ];
                    $encontrado = true;
                    break;
                }
            }
            if (!$encontrado) {
                $ventas[] = [
                    'fecha' => $fecha,
                    'total' => 0.0
                ];
            }
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
