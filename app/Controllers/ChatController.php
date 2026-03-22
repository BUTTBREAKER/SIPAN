<?php

namespace App\Controllers;

use App\Models\ChatMensaje;
use App\Middlewares\AuthMiddleware;

class ChatController
{
    private $chatModel;

    public function __construct()
    {
        require_once __DIR__ . '/../Helpers/CSRF.php';
        $this->chatModel = new ChatMensaje();
    }

    /**
     * Vista principal del chat
     */
    public function index()
    {
        AuthMiddleware::check();
        $pageTitle = 'Chat Interno';
        $currentPage = 'chat';
        require_once __DIR__ . '/../Views/chat/index.php';
    }

    /**
     * API: Lista de conversaciones del usuario
     */
    public function getConversaciones()
    {
        AuthMiddleware::check();
        header('Content-Type: application/json');

        $userId = $_SESSION['user_id'];
        $conversaciones = $this->chatModel->getConversaciones($userId);

        echo json_encode(['success' => true, 'conversaciones' => $conversaciones]);
        exit;
    }

    /**
     * API: Lista de usuarios disponibles para chatear
     */
    public function getUsuarios()
    {
        AuthMiddleware::check();
        header('Content-Type: application/json');

        $userId = $_SESSION['user_id'];
        $usuarios = $this->chatModel->getUsuariosDisponibles($userId);

        // Agrupar por sucursal
        $agrupados = [];
        foreach ($usuarios as $u) {
            $sucNombre = $u['sucursal_nombre'] ?? 'Sin sucursal';
            if (!isset($agrupados[$sucNombre])) {
                $agrupados[$sucNombre] = [];
            }
            $agrupados[$sucNombre][] = $u;
        }

        echo json_encode(['success' => true, 'usuarios' => $agrupados]);
        exit;
    }

    /**
     * API: Obtener o crear conversación directa con un usuario
     */
    public function getOrCreateDirecta()
    {
        AuthMiddleware::check();
        header('Content-Type: application/json');

        if (!\App\Helpers\CSRF::validateToken($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '')) {
            echo json_encode(['success' => false, 'message' => 'Token de seguridad inválido']);
            exit;
        }

        $userId = $_SESSION['user_id'];
        $otherUserId = intval($_POST['user_id'] ?? 0);

        if ($otherUserId <= 0 || $otherUserId === $userId) {
            echo json_encode(['success' => false, 'message' => 'Usuario inválido']);
            exit;
        }

        try {
            $convId = $this->chatModel->getOrCreateDirecta($userId, $otherUserId);
            echo json_encode(['success' => true, 'conversacion_id' => $convId]);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error al crear conversación: ' . $e->getMessage()]);
        }
        exit;
    }

    /**
     * API: Obtener mensajes de una conversación
     */
    public function getMensajes($convId)
    {
        AuthMiddleware::check();
        header('Content-Type: application/json');

        $userId = $_SESSION['user_id'];
        $convId = intval($convId);

        // Verificar que es participante
        if (!$this->chatModel->esParticipante($convId, $userId)) {
            echo json_encode(['success' => false, 'message' => 'Acceso no autorizado']);
            exit;
        }

        $beforeId = intval($_GET['before'] ?? 0) ?: null;
        $mensajes = $this->chatModel->getMensajes($convId, 50, $beforeId);

        // Marcar como leída
        $this->chatModel->marcarLeida($convId, $userId);

        echo json_encode([
            'success' => true,
            'mensajes' => $mensajes,
            'user_id' => $userId
        ]);
        exit;
    }

    /**
     * API: Enviar mensaje
     */
    public function enviar($convId)
    {
        AuthMiddleware::check();
        header('Content-Type: application/json');

        if (!\App\Helpers\CSRF::validateToken($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '')) {
            echo json_encode(['success' => false, 'message' => 'Token de seguridad inválido']);
            exit;
        }

        $userId = $_SESSION['user_id'];
        $convId = intval($convId);
        $mensaje = trim($_POST['mensaje'] ?? '');

        if (empty($mensaje)) {
            echo json_encode(['success' => false, 'message' => 'El mensaje no puede estar vacío']);
            exit;
        }

        if (mb_strlen($mensaje) > 2000) {
            echo json_encode(['success' => false, 'message' => 'El mensaje es demasiado largo (máx. 2000 caracteres)']);
            exit;
        }

        // Verificar que es participante
        if (!$this->chatModel->esParticipante($convId, $userId)) {
            echo json_encode(['success' => false, 'message' => 'Acceso no autorizado']);
            exit;
        }

        try {
            $msgId = $this->chatModel->enviar($convId, $userId, $mensaje);
            // Marcar como leída al enviar
            $this->chatModel->marcarLeida($convId, $userId);

            echo json_encode(['success' => true, 'message_id' => $msgId]);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error al enviar: ' . $e->getMessage()]);
        }
        exit;
    }

    /**
     * API: Polling - devuelve timestamp del último cambio y total no leídos
     */
    public function poll()
    {
        AuthMiddleware::check();
        header('Content-Type: application/json');

        $userId = $_SESSION['user_id'];
        $ultima = $this->chatModel->getUltimaActividad($userId);
        $noLeidos = $this->chatModel->contarNoLeidos($userId);

        echo json_encode([
            'success' => true,
            'ultima_actividad' => $ultima,
            'no_leidos' => $noLeidos
        ]);
        exit;
    }

    /**
     * API UNIFICADA: Sync - un solo endpoint para todo el polling.
     * Parámetros GET opcionales:
     *   conv_id  = ID de conversación activa (para traer mensajes)
     *   last_ts  = timestamp de última actividad conocida (para detectar cambios)
     *   full     = 1 para forzar respuesta completa (conversaciones + mensajes)
     */
    public function sync()
    {
        AuthMiddleware::check();
        header('Content-Type: application/json');

        $userId = $_SESSION['user_id'];
        $convId = intval($_GET['conv_id'] ?? 0);
        $lastTs = $_GET['last_ts'] ?? null;
        $full = intval($_GET['full'] ?? 0);

        $response = ['success' => true];

        // Siempre devolver: no leídos totales + última actividad
        $ultima = $this->chatModel->getUltimaActividad($userId);
        $noLeidos = $this->chatModel->contarNoLeidos($userId);
        $response['no_leidos'] = $noLeidos;
        $response['ultima_actividad'] = $ultima;

        // ¿Hubo cambios desde la última vez?
        $hasChanges = ($lastTs === null || $ultima !== $lastTs);
        $response['has_changes'] = $hasChanges;

        // Si hubo cambios o se pidió full, incluir conversaciones
        if ($hasChanges || $full) {
            $response['conversaciones'] = $this->chatModel->getConversaciones($userId);
        }

        // Si hay conv activa y hubo cambios, incluir mensajes
        if ($convId > 0 && $this->chatModel->esParticipante($convId, $userId)) {
            if ($hasChanges || $full) {
                $response['mensajes'] = $this->chatModel->getMensajes($convId);
                $this->chatModel->marcarLeida($convId, $userId);
            }
        }

        echo json_encode($response);
        exit;
    }
}
