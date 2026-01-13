<?php

namespace App\Controllers;

use App\Models\Auditoria;
use App\Middlewares\AuthMiddleware;

class AuditoriasController {
    private $auditoriaModel;
    
    public function __construct() {
        $this->auditoriaModel = new Auditoria();
    }
    
    public function index() {
        AuthMiddleware::checkRole(['administrador']);
        
        $user = AuthMiddleware::getUser();
        $sucursal_id = $user['sucursal_id'];
        
        $tabla = $_GET['tabla'] ?? null;
        $usuario_id = $_GET['usuario_id'] ?? null;
        
        $auditorias = $this->auditoriaModel->getWithDetails($sucursal_id, $tabla, $usuario_id);
        
        require_once __DIR__ . '/../Views/auditorias/index.php';
    }
    
    public function show($id) {
        AuthMiddleware::checkRole(['administrador']);
        
        header('Content-Type: application/json');
        
        $auditoria = $this->auditoriaModel->find($id);
        
        if (!$auditoria) {
            echo json_encode([
                'success' => false,
                'message' => 'Auditoría no encontrada'
            ]);
            exit;
        }
        
        echo json_encode([
            'success' => true,
            'auditoria' => $auditoria
        ]);
        exit;
    }
    
    public function deshacer() {
        AuthMiddleware::checkRole(['administrador']);
        
        header('Content-Type: application/json');
        
        $user = AuthMiddleware::getUser();
        $data = json_decode(file_get_contents('php://input'), true);
        $auditoria_id = $data['auditoria_id'] ?? 0;
        
        if (!$auditoria_id) {
            echo json_encode(['success' => false, 'message' => 'ID de auditoría no válido']);
            exit;
        }
        
        // Verificar si se puede deshacer
        if (!$this->auditoriaModel->puedeDeshacer($auditoria_id)) {
            echo json_encode([
                'success' => false, 
                'message' => 'Esta acción no puede ser deshecha (ya fue revertida o han pasado más de 24 horas)'
            ]);
            exit;
        }
        
        $resultado = $this->auditoriaModel->deshacer($auditoria_id, $user['id']);
        echo json_encode($resultado);
        exit;
    }
    
    public function verificarDeshacer($id) {
        AuthMiddleware::checkRole(['administrador']);
        
        header('Content-Type: application/json');
        
        $puede_deshacer = $this->auditoriaModel->puedeDeshacer($id);
        
        echo json_encode([
            'success' => true,
            'puede_deshacer' => $puede_deshacer
        ]);
        exit;
    }
    
    public function estadisticas() {
        AuthMiddleware::checkRole(['administrador']);
        
        $user = AuthMiddleware::getUser();
        $sucursal_id = $user['sucursal_id'];
        
        $estadisticas = $this->auditoriaModel->getEstadisticas($sucursal_id);
        
        require_once __DIR__ . '/../Views/auditorias/estadisticas.php';
    }
}
