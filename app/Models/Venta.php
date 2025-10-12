<?php

namespace SIPAN\Models;

class Venta extends BaseModel {
    protected $table = 'ventas';
    
    public function createWithProducts($venta_data, $productos) {
        try {
            $this->db->beginTransaction();
            
            // Crear venta
            $venta_id = $this->create($venta_data);
            
            // Agregar productos
            foreach ($productos as $producto) {
                $sql = "INSERT INTO venta_productos (id_venta, id_producto, cantidad, precio_unitario, subtotal)
                        VALUES (?, ?, ?, ?, ?)";
                $this->db->execute($sql, [
                    $venta_id,
                    $producto['id_producto'],
                    $producto['cantidad'],
                    $producto['precio_unitario'],
                    $producto['subtotal']
                ]);
            }
            
            $this->db->commit();
            return $venta_id;
            
        } catch (\Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }
    
    public function getProductos($venta_id) {
        $sql = "SELECT vp.*, p.nombre as producto_nombre
                FROM venta_productos vp
                INNER JOIN productos p ON vp.id_producto = p.id
                WHERE vp.id_venta = ?";
        return $this->db->fetchAll($sql, [$venta_id]);
    }
    
    public function getWithDetails($sucursal_id, $fecha_inicio = null, $fecha_fin = null) {
        $sql = "SELECT v.*, u.primer_nombre, u.apellido_paterno,
                       COUNT(vp.id) as total_productos
                FROM {$this->table} v
                INNER JOIN usuarios u ON v.id_usuario = u.id
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
    
    public function getTotalVentas($sucursal_id, $fecha_inicio = null, $fecha_fin = null) {
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
    
    public function getByDateRange($sucursal_id, $fecha_inicio, $fecha_fin) {
        $sql = "SELECT v.*, 
                       CONCAT(c.primer_nombre, ' ', c.apellido_paterno) as cliente_nombre,
                       u.nombre as usuario_nombre
                FROM {$this->table} v
                LEFT JOIN clientes c ON v.id_cliente = c.id
                LEFT JOIN usuarios u ON v.id_usuario = u.id
                WHERE v.id_sucursal = ? AND DATE(v.fecha_venta) BETWEEN ? AND ?
                ORDER BY v.fecha_venta DESC";
        
        return $this->db->fetchAll($sql, [$sucursal_id, $fecha_inicio, $fecha_fin]);
    }
    
    public function getClienteStats($cliente_id) {
        $sql = "SELECT COUNT(*) as total_compras, COALESCE(SUM(total), 0) as monto_total
                FROM {$this->table}
                WHERE id_cliente = ?";
        
        return $this->db->fetchOne($sql, [$cliente_id]);
    }
    
    public function getVentasUltimosDias($sucursal_id, $dias = 7) {
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
    
    public function getProductosMasVendidos($sucursal_id, $limit = 5) {
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
}
