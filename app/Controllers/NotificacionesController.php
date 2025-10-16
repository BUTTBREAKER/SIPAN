<?php

namespace SIPAN\Controllers;

use SIPAN\Middlewares\AuthMiddleware;
use SIPAN\Models\Notificacion;

class NotificacionesController
{
  private $notificacionModel;

  public function __construct()
  {
    AuthMiddleware::checkAuth();
    $this->notificacionModel = new Notificacion();
  }

  public function getNoLeidas()
  {
    header('Content-Type: application/json');

    $usuario_id = $_SESSION['user_id'];
    $notificaciones = $this->notificacionModel->getNoLeidasByUsuario($usuario_id);

    echo json_encode([
      'success' => true,
      'notificaciones' => $notificaciones
    ]);
    exit;
  }

  public function marcarLeida($id)
  {
    header('Content-Type: application/json');

    $usuario_id = $_SESSION['user_id'];

    // Verificar que la notificación pertenece al usuario
    $notificacion = $this->notificacionModel->find($id);

    if (!$notificacion || $notificacion['id_usuario'] != $usuario_id) {
      echo json_encode(['success' => false, 'message' => 'Notificación no encontrada']);
      exit;
    }

    $this->notificacionModel->update($id, ['leida' => 1]);

    echo json_encode(['success' => true, 'message' => 'Notificación marcada como leída']);
    exit;
  }

  public function marcarTodasLeidas()
  {
    header('Content-Type: application/json');

    $usuario_id = $_SESSION['user_id'];
    $this->notificacionModel->marcarTodasLeidasByUsuario($usuario_id);

    echo json_encode(['success' => true, 'message' => 'Todas las notificaciones marcadas como leídas']);
    exit;
  }
}
