<?php
namespace App\Controllers;

use App\Middlewares\AuthMiddleware;
use App\Models\Notificacion;

class NotificacionesController {
    private $notificacionModel;
    
    public function __construct() {
        AuthMiddleware::checkAuth();
        $this->notificacionModel = new Notificacion();
    }
    
    public function getNoLeidas() {
        if (ob_get_length()) ob_clean();
        header('Content-Type: application/json');
        
        $usuario_id = $_SESSION['user_id'];
        $sucursal_id = $_SESSION['sucursal_id']; // Asumiendo que el ID de sucursal está en la sesión
        $notificaciones = $this->notificacionModel->getNoLeidas($sucursal_id, $usuario_id);
        
        echo json_encode([
            'success' => true,
            'notificaciones' => $notificaciones
        ]);
        exit;
    }
    
    public function marcarLeida($id) {
        if (ob_get_length()) ob_clean();
        header('Content-Type: application/json');
        
        $usuario_id = $_SESSION['user_id'];
        
        // Verificar que la notificación pertenece al usuario
        $notificacion = $this->notificacionModel->find($id);
        
        if (!$notificacion || ($notificacion['id_usuario'] != $usuario_id && $notificacion['id_usuario'] !== null)) {
            echo json_encode(['success' => false, 'message' => 'Notificación no encontrada o no pertenece al usuario']);
            exit;
        }
        
        $this->notificacionModel->marcarComoLeida($id);
        
        echo json_encode(['success' => true, 'message' => 'Notificación marcada como leída']);
        exit;
    }
    
    public function marcarTodasLeidas() {
        if (ob_get_length()) ob_clean();
        header('Content-Type: application/json');
        
        $usuario_id = $_SESSION['user_id'];
        $sucursal_id = $_SESSION['sucursal_id']; // Asumiendo que el ID de sucursal está en la sesión
        $this->notificacionModel->marcarTodasComoLeidas($sucursal_id, $usuario_id);
        
        echo json_encode(['success' => true, 'message' => 'Todas las notificaciones marcadas como leídas']);
        exit;
    }
}

