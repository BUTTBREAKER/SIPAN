<?php

namespace App\Controllers;

use App\Models\Producto;
use App\Models\Negocio;
use App\Middlewares\AuthMiddleware;

class ProductosController {
    private $productoModel;
    private $negocioModel;
    
    public function __construct() {
        $this->productoModel = new Producto();
        $this->negocioModel = new Negocio();
    }
    
    public function index() {
        AuthMiddleware::check();
        
        $user = AuthMiddleware::getUser();
        $sucursal_id = $user['sucursal_id'];
        
        $productos = $this->productoModel->all($sucursal_id);
        
        require_once __DIR__ . '/../Views/productos/index.php';
    }
    
    public function create() {
        AuthMiddleware::checkRole(['administrador', 'empleado']);
        
        require_once __DIR__ . '/../Views/productos/create.php';
    }
    
    public function store() {
        AuthMiddleware::checkRole(['administrador', 'empleado']);
        
        header('Content-Type: application/json');
        
        $user = AuthMiddleware::getUser();
        $sucursal_id = $user['sucursal_id'];
        
        // Obtener negocio de la sucursal
        $negocio = $this->negocioModel->getBySucursal($sucursal_id);
        
        if (!$negocio) {
            echo json_encode(['success' => false, 'message' => 'No se encontró negocio para esta sucursal']);
            exit;
        }
        
        $data = [
            'id_negocio' => $negocio['id'],
            'id_sucursal' => $sucursal_id,
            'id_usuario' => $user['id'],
            'nombre' => $_POST['nombre'] ?? '',
            'categoria' => $_POST['categoria'] ?? 'Otro',
            'descripcion' => $_POST['descripcion'] ?? '',
            'stock_actual' => $_POST['stock_actual'] ?? 0,
            'stock_minimo' => $_POST['stock_minimo'] ?? 0,
            'precio_actual' => $_POST['precio_actual'] ?? 0
        ];
        
        try {
            $id = $this->productoModel->create($data);
            echo json_encode([
                'success' => true, 
                'message' => 'Producto creado correctamente', 
                'id' => $id, 
                'nombre' => $data['nombre'] 
            ]);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error al crear producto: ' . $e->getMessage()]);
        }
        exit;
    }
    
    public function edit($id) {
        AuthMiddleware::checkRole(['administrador', 'empleado']);
        
        $producto = $this->productoModel->find($id);
        
        if (!$producto) {
            header('Location: /productos');
            exit;
        }
        
        require_once __DIR__ . '/../Views/productos/edit.php';
    }
    
    public function update($id) {
        AuthMiddleware::checkRole(['administrador', 'empleado']);
        
        header('Content-Type: application/json');
        
        $data = [
            'nombre' => $_POST['nombre'] ?? '',
            'categoria' => $_POST['categoria'] ?? 'Otro',
            'descripcion' => $_POST['descripcion'] ?? '',
            'stock_actual' => $_POST['stock_actual'] ?? 0,
            'stock_minimo' => $_POST['stock_minimo'] ?? 0,
            'precio_actual' => $_POST['precio_actual'] ?? 0
        ];
        
        try {
            $this->productoModel->update($id, $data);
            echo json_encode(['success' => true, 'message' => 'Producto actualizado correctamente']);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error al actualizar producto: ' . $e->getMessage()]);
        }
        exit;
    }
    
    public function delete($id) {
        AuthMiddleware::checkRole(['administrador']);
        
        header('Content-Type: application/json');
        
        try {
            $this->productoModel->delete($id);
            echo json_encode(['success' => true, 'message' => 'Producto eliminado correctamente']);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error al eliminar producto: ' . $e->getMessage()]);
        }
        exit;
    }
    
    public function search() {
        AuthMiddleware::check();
        
        header('Content-Type: application/json');
        
        $user = AuthMiddleware::getUser();
        $sucursal_id = $user['sucursal_id'];
        $search = $_GET['q'] ?? '';
        
        if (empty($search)) {
            $productos = $this->productoModel->all($sucursal_id);
        } else {
            $productos = $this->productoModel->search($search, $sucursal_id);
        }
        
        echo json_encode(['success' => true, 'productos' => $productos]);
        exit;
    }
    
    public function stockBajo() {
        AuthMiddleware::check();
        
        header('Content-Type: application/json');
        
        $user = AuthMiddleware::getUser();
        $sucursal_id = $user['sucursal_id'];
        
        $productos = $this->productoModel->getWithStockBajo($sucursal_id);
        
        echo json_encode(['success' => true, 'productos' => $productos]);
        exit;
    }
}
