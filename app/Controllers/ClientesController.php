<?php

namespace App\Controllers;

use App\Models\Cliente;
use App\Models\Pedido;
use App\Middlewares\AuthMiddleware;

class ClientesController {
    private $clienteModel;
    private $pedidoModel;
    
    public function __construct() {
        $this->clienteModel = new Cliente();
        $this->pedidoModel = new Pedido();
    }
    
    public function index() {
        AuthMiddleware::check();
        
        $user = AuthMiddleware::getUser();
        $sucursal_id = $user['sucursal_id'];
        
        $clientes = $this->clienteModel->getWithResumen($sucursal_id);
        
        require_once __DIR__ . '/../Views/clientes/index.php';
    }
    
    public function create() {
        AuthMiddleware::checkRole(['administrador', 'empleado']);
        
        require_once __DIR__ . '/../Views/clientes/create.php';
    }
    
    public function store() {
        AuthMiddleware::checkRole(['administrador', 'empleado']);
        
        header('Content-Type: application/json');
        
        $user = AuthMiddleware::getUser();
        $sucursal_id = $user['sucursal_id'];
        
        $data = [
            'id_sucursal' => $sucursal_id,
            'nombre' => $_POST['nombre'] ?? '',
            'apellido' => $_POST['apellido'] ?? '',
            'documento_tipo' => $_POST['documento_tipo'] ?? 'DNI',
            'documento_numero' => $_POST['documento_numero'] ?? '',
            'telefono' => $_POST['telefono'] ?? '',
            'correo' => $_POST['correo'] ?? '',
            'direccion' => $_POST['direccion'] ?? ''
        ];
        
        try {
            $id = $this->clienteModel->create($data);
            echo json_encode(['success' => true, 'message' => 'Cliente creado correctamente', 'id' => $id]);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error al crear cliente: ' . $e->getMessage()]);
        }
        exit;
    }
    
    public function edit($id) {
        AuthMiddleware::checkRole(['administrador', 'empleado']);
        
        $cliente = $this->clienteModel->find($id);
        
        if (!$cliente) {
            header('Location: /clientes');
            exit;
        }
        
        require_once __DIR__ . '/../Views/clientes/edit.php';
    }
    
    public function update($id) {
        AuthMiddleware::checkRole(['administrador', 'empleado']);
        
        header('Content-Type: application/json');
        
        $data = [
            'nombre' => $_POST['nombre'] ?? '',
            'apellido' => $_POST['apellido'] ?? '',
            'documento_tipo' => $_POST['documento_tipo'] ?? 'DNI',
            'documento_numero' => $_POST['documento_numero'] ?? '',
            'telefono' => $_POST['telefono'] ?? '',
            'correo' => $_POST['correo'] ?? '',
            'direccion' => $_POST['direccion'] ?? '',
            'estado' => $_POST['estado'] ?? 'activo'
        ];
        
        try {
            $this->clienteModel->update($id, $data);
            echo json_encode(['success' => true, 'message' => 'Cliente actualizado correctamente']);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error al actualizar cliente: ' . $e->getMessage()]);
        }
        exit;
    }
    
    public function delete($id) {
        AuthMiddleware::checkRole(['administrador']);
        
        header('Content-Type: application/json');
        
        try {
            $this->clienteModel->delete($id);
            echo json_encode(['success' => true, 'message' => 'Cliente eliminado correctamente']);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error al eliminar cliente: ' . $e->getMessage()]);
        }
        exit;
    }
    
    public function show($id) {
        AuthMiddleware::check();
        
        $cliente = $this->clienteModel->find($id);
        
        if (!$cliente) {
            header('Location: /clientes');
            exit;
        }
        
        $pedidos = $this->pedidoModel->getByCliente($id);
        
        require_once __DIR__ . '/../Views/clientes/show.php';
    }
    
    public function search() {
        AuthMiddleware::check();
        
        header('Content-Type: application/json');
        
        $user = AuthMiddleware::getUser();
        $sucursal_id = $user['sucursal_id'];
        $search = $_GET['q'] ?? '';
        
        if (empty($search)) {
            $clientes = $this->clienteModel->all($sucursal_id);
        } else {
            $clientes = $this->clienteModel->search($search, $sucursal_id);
        }
        
        echo json_encode(['success' => true, 'clientes' => $clientes]);
        exit;
    }
}
