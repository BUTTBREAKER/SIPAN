<?php

namespace App\Models;

class Pedido extends BaseModel
{
    protected $table = 'pedidos';

    public function createWithProducts($pedido_data, $productos)
    {
        try {
            $this->db->beginTransaction();

            // Generar número de pedido único
            $pedido_data['numero_pedido'] = $this->generarNumeroPedido();

            // Crear pedido
            $pedido_id = $this->create($pedido_data);


            /// Agregar productos
            foreach ($productos as $producto) {
                $subtotal = $producto['precio'] * $producto['cantidad'];  // Calcular aquí

                $sql = "INSERT INTO pedido_productos (id_pedido, id_producto, cantidad, precio_unitario, subtotal)
            VALUES (?, ?, ?, ?, ?)";
                $this->db->execute($sql, [
                    $pedido_id,
                    $producto['id'],  // Cambiado a 'id'
                    $producto['cantidad'],
                    $producto['precio'],  // Cambiado a 'precio'
                    $subtotal
                ]);
            }

            $this->db->commit();
            return $pedido_id;
        } catch (\Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    public function registrarPago($pedido_id, $monto, $metodo_pago, $usuario_id, $referencia = null, $observaciones = null)
    {
        $sql = "INSERT INTO pedido_pagos (id_pedido, id_usuario, monto, metodo_pago, referencia, observaciones)
                VALUES (?, ?, ?, ?, ?, ?)";
        return $this->db->execute($sql, [$pedido_id, $usuario_id, $monto, $metodo_pago, $referencia, $observaciones]);
    }

    public function getPagos($pedido_id)
    {
        $sql = "SELECT pp.*, u.primer_nombre, u.apellido_paterno
                FROM pedido_pagos pp
                LEFT JOIN usuarios u ON pp.id_usuario = u.id
                WHERE pp.id_pedido = ?
                ORDER BY pp.fecha_pago DESC";
        return $this->db->fetchAll($sql, [$pedido_id]);
    }

    public function getProductos($pedido_id)
    {
        $sql = "SELECT pp.*, p.nombre as producto_nombre
                FROM pedido_productos pp
                INNER JOIN productos p ON pp.id_producto = p.id
                WHERE pp.id_pedido = ?";
        return $this->db->fetchAll($sql, [$pedido_id]);
    }

    public function getByCliente($cliente_id)
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE id_cliente = ? 
                ORDER BY fecha_pedido DESC";
        return $this->db->fetchAll($sql, [$cliente_id]);
    }

    public function getWithDetails($sucursal_id, $estado_pedido = null, $estado_pago = null)
    {
        $sql = "SELECT p.*, c.nombre as cliente_nombre, c.apellido as cliente_apellido,
                       u.primer_nombre, u.apellido_paterno
                FROM {$this->table} p
                INNER JOIN clientes c ON p.id_cliente = c.id
                LEFT JOIN usuarios u ON p.id_usuario = u.id
                WHERE p.id_sucursal = ?";

        $params = [$sucursal_id];

        if ($estado_pedido) {
            $sql .= " AND p.estado_pedido = ?";
            $params[] = $estado_pedido;
        }

        if ($estado_pago) {
            $sql .= " AND p.estado_pago = ?";
            $params[] = $estado_pago;
        }

        $sql .= " ORDER BY p.fecha_pedido DESC";

        return $this->db->fetchAll($sql, $params);
    }

    private function generarNumeroPedido()
    {
        $fecha = date('Ymd');
        $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE DATE(fecha_pedido) = CURDATE()";
        $result = $this->db->fetchOne($sql);
        $numero = ($result['total'] ?? 0) + 1;
        return "PED-{$fecha}-" . str_pad($numero, 4, '0', STR_PAD_LEFT);
    }
}
