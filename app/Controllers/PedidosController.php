<?php

namespace SIPAN\Controllers;

use SIPAN\Models\Pedido;
use SIPAN\Models\Cliente;
use SIPAN\Models\Producto;
use SIPAN\Middlewares\AuthMiddleware;

class PedidosController {
    private $pedidoModel;
    private $clienteModel;
    private $productoModel;
    
    public function __construct() {
        $this->pedidoModel = new Pedido();
        $this->clienteModel = new Cliente();
        $this->productoModel = new Producto();
    }
    
    public function index() {
        AuthMiddleware::check();
        
        $user = AuthMiddleware::getUser();
        $sucursal_id = $user['sucursal_id'];
        
        $pedidos = $this->pedidoModel->getWithDetails($sucursal_id);
        
        require_once __DIR__ . '/../Views/pedidos/index.php';
    }
    
    public function create() {
        AuthMiddleware::checkRole(['administrador', 'cajero', 'empleado']);
        
        $user = AuthMiddleware::getUser();
        $sucursal_id = $user['sucursal_id'];
        
        $clientes = $this->clienteModel->all($sucursal_id);
        $productos = $this->productoModel->all($sucursal_id);
        
        require_once __DIR__ . '/../Views/pedidos/create.php';
    }
    
    public function store() {
        AuthMiddleware::checkRole(['administrador', 'cajero', 'empleado']);
        
        header('Content-Type: application/json');
        
        $user = AuthMiddleware::getUser();
        $sucursal_id = $user['sucursal_id'];
        
        $pedido_data = [
            'id_cliente' => $_POST['id_cliente'] ?? 0,
            'id_sucursal' => $sucursal_id,
            'id_usuario' => $user['id'],
            'fecha_entrega' => $_POST['fecha_entrega'] ?? null,
            'estado_pedido' => $_POST['estado_pedido'] ?? 'pendiente',
            'estado_pago' => 'pendiente',
            'subtotal' => $_POST['subtotal'] ?? 0,
            'descuento' => $_POST['descuento'] ?? 0,
            'total' => $_POST['total'] ?? 0,
            'monto_pagado' => 0,
            'monto_deuda' => $_POST['total'] ?? 0,
            'observaciones' => $_POST['observaciones'] ?? ''
        ];
        
        $productos = json_decode($_POST['productos'] ?? '[]', true);
        
        if (empty($productos)) {
            echo json_encode(['success' => false, 'message' => 'Debe agregar al menos un producto']);
            exit;
        }
        
        try {
            $pedido_id = $this->pedidoModel->createWithProducts($pedido_data, $productos);
            echo json_encode(['success' => true, 'message' => 'Pedido creado correctamente', 'id' => $pedido_id]);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error al crear pedido: ' . $e->getMessage()]);
        }
        exit;
    }
    
    public function show($id) {
        AuthMiddleware::check();
        
        $pedido = $this->pedidoModel->find($id);
        
        if (!$pedido) {
            header('Location: /pedidos');
            exit;
        }
        
        $cliente = $this->clienteModel->find($pedido['id_cliente']);
        $productos = $this->pedidoModel->getProductos($id);
        $pagos = $this->pedidoModel->getPagos($id);
        
        require_once __DIR__ . '/../Views/pedidos/show.php';
    }
    
    public function update($id) {
        AuthMiddleware::checkRole(['administrador', 'empleado']);
        
        header('Content-Type: application/json');
        
        $data = [
            'estado_pedido' => $_POST['estado_pedido'] ?? 'pendiente',
            'fecha_entrega' => $_POST['fecha_entrega'] ?? null,
            'observaciones' => $_POST['observaciones'] ?? ''
        ];
        
        try {
            $this->pedidoModel->update($id, $data);
            echo json_encode(['success' => true, 'message' => 'Pedido actualizado correctamente']);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error al actualizar pedido: ' . $e->getMessage()]);
        }
        exit;
    }
    
    public function registrarPago() {
        AuthMiddleware::checkRole(['administrador', 'cajero', 'empleado']);
        
        header('Content-Type: application/json');
        
        $user = AuthMiddleware::getUser();
        
        $pedido_id = $_POST['pedido_id'] ?? 0;
        $monto = $_POST['monto'] ?? 0;
        $metodo_pago = $_POST['metodo_pago'] ?? 'efectivo';
        $referencia = $_POST['referencia'] ?? null;
        $observaciones = $_POST['observaciones'] ?? null;
        
        if (!$pedido_id || !$monto) {
            echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
            exit;
        }
        
        try {
            $this->pedidoModel->registrarPago($pedido_id, $monto, $metodo_pago, $user['id'], $referencia, $observaciones);
            echo json_encode(['success' => true, 'message' => 'Pago registrado correctamente']);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error al registrar pago: ' . $e->getMessage()]);
        }
        exit;
    }
}
