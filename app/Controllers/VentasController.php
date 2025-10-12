<?php

namespace SIPAN\Controllers;

use SIPAN\Models\Venta;
use SIPAN\Models\Producto;
use SIPAN\Models\Negocio;
use SIPAN\Middlewares\AuthMiddleware;

class VentasController {
    private $ventaModel;
    private $productoModel;
    private $negocioModel;
    
    public function __construct() {
        $this->ventaModel = new Venta();
        $this->productoModel = new Producto();
        $this->negocioModel = new Negocio();
    }
    
    public function index() {
        AuthMiddleware::check();
        
        $user = AuthMiddleware::getUser();
        $sucursal_id = $user['sucursal_id'];
        
        $ventas = $this->ventaModel->getWithDetails($sucursal_id);
        
        require_once __DIR__ . '/../Views/ventas/index.php';
    }
    
    public function create() {
        AuthMiddleware::checkRole(['administrador', 'cajero', 'empleado']);
        
        $user = AuthMiddleware::getUser();
        $sucursal_id = $user['sucursal_id'];
        
        $productos = $this->productoModel->all($sucursal_id);
        
        require_once __DIR__ . '/../Views/ventas/create.php';
    }
    
    public function store() {
        AuthMiddleware::checkRole(['administrador', 'cajero', 'empleado']);
        
        header('Content-Type: application/json');
        
        $user = AuthMiddleware::getUser();
        $sucursal_id = $user['sucursal_id'];
        
        $negocio = $this->negocioModel->getBySucursal($sucursal_id);
        
        if (!$negocio) {
            echo json_encode(['success' => false, 'message' => 'No se encontró negocio para esta sucursal']);
            exit;
        }
        
        $venta_data = [
            'id_negocio' => $negocio['id'],
            'id_sucursal' => $sucursal_id,
            'id_usuario' => $user['id'],
            'total' => $_POST['total'] ?? 0,
            'metodo_pago' => $_POST['metodo_pago'] ?? 'efectivo',
            'estado' => 'completada'
        ];
        
        $productos = json_decode($_POST['productos'] ?? '[]', true);
        
        if (empty($productos)) {
            echo json_encode(['success' => false, 'message' => 'Debe agregar al menos un producto']);
            exit;
        }
        
        try {
            $venta_id = $this->ventaModel->createWithProducts($venta_data, $productos);
            echo json_encode(['success' => true, 'message' => 'Venta registrada correctamente', 'id' => $venta_id]);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error al registrar venta: ' . $e->getMessage()]);
        }
        exit;
    }
    
    public function show($id) {
        AuthMiddleware::check();
        
        $venta = $this->ventaModel->find($id);
        
        if (!$venta) {
            header('Location: /ventas');
            exit;
        }
        
        $productos = $this->ventaModel->getProductos($id);
        
        require_once __DIR__ . '/../Views/ventas/show.php';
    }
    
    public function ticket($id) {
        AuthMiddleware::check();
        
        $venta = $this->ventaModel->find($id);
        
        if (!$venta) {
            header('Location: /ventas');
            exit;
        }
        
        $productos = $this->ventaModel->getProductos($id);
        
        require_once __DIR__ . '/../Views/ventas/ticket.php';
    }
}
