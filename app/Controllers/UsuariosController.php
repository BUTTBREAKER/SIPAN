<?php

namespace App\Controllers;

use App\Models\Usuario;
use App\Models\Auditoria;
use App\Middlewares\AuthMiddleware;

class UsuariosController
{
    private $usuarioModel;
    private $auditoriaModel;

    public function __construct()
    {
        AuthMiddleware::checkAuth();

        $this->usuarioModel = new Usuario();
        $this->auditoriaModel = new Auditoria();
    }

    public function index()
    {
        $user = AuthMiddleware::getUser();

        // Solo administradores pueden ver todos los usuarios
        if ($user['rol'] !== 'administrador') {
            header('Location: /usuarios/perfil');
            exit;
        }

        $sucursal_id = $user['sucursal_id'];
        $usuarios = $this->usuarioModel->getBySucursal($sucursal_id);

        require_once __DIR__ . '/../Views/usuarios/index.php';
    }

    public function perfil()
    {
        $user = AuthMiddleware::getUser();
        $usuario = $this->usuarioModel->find($user['id']);

        // Construir nombre completo
        $usuario['nombre'] = trim(
            ($usuario['primer_nombre'] ?? '') . ' ' .
                ($usuario['segundo_nombre'] ?? '') . ' ' .
                ($usuario['apellido_paterno'] ?? '') . ' ' .
                ($usuario['apellido_materno'] ?? '')
        );

        // Obtener actividad reciente del usuario
        $actividad = $this->auditoriaModel->getByUsuario($user['id'], 20);

        require_once __DIR__ . '/../Views/usuarios/perfil.php';
    }

    public function actividad()
    {
        $user = AuthMiddleware::getUser();
        $usuario_id = $_GET['usuario_id'] ?? $user['id'];

        // Solo administradores pueden ver actividad de otros usuarios
        if ($user['rol'] !== 'administrador' && $usuario_id != $user['id']) {
            header('Location: /usuarios/perfil');
            exit;
        }

        $usuario = $this->usuarioModel->find($usuario_id);
        $actividad = $this->auditoriaModel->getByUsuario($usuario_id, 100);

        require_once __DIR__ . '/../Views/usuarios/actividad.php';
    }

    public function actualizarPerfil()
    {
        header('Content-Type: application/json');

        $user = AuthMiddleware::getUser();
        $input = json_decode(file_get_contents('php://input'), true);

        try {
            $data = [];

            // Opción 1: Si se envía "nombre" completo (formulario con un solo campo)
            if (isset($input['nombre']) && !empty($input['nombre'])) {
                $partes_nombre = explode(' ', trim($input['nombre']));

                if (count($partes_nombre) >= 2) {
                    $data['primer_nombre'] = $partes_nombre[0];
                    $data['apellido_paterno'] = $partes_nombre[count($partes_nombre) - 1];

                    // Si hay más de 2 partes, asignar segundo nombre y apellido materno
                    if (count($partes_nombre) == 3) {
                        $data['segundo_nombre'] = $partes_nombre[1];
                    } elseif (count($partes_nombre) >= 4) {
                        $data['segundo_nombre'] = $partes_nombre[1];
                        $data['apellido_materno'] = $partes_nombre[2];
                    }
                } else {
                    $data['primer_nombre'] = $partes_nombre[0];
                }

                $nombre_completo = $input['nombre'];
            }
            // Opción 2: Si se envían campos individuales
            else {
                if (isset($input['primer_nombre'])) {
                    $data['primer_nombre'] = $input['primer_nombre'];
                }
                if (isset($input['segundo_nombre'])) {
                    $data['segundo_nombre'] = $input['segundo_nombre'];
                }
                if (isset($input['apellido_paterno'])) {
                    $data['apellido_paterno'] = $input['apellido_paterno'];
                }
                if (isset($input['apellido_materno'])) {
                    $data['apellido_materno'] = $input['apellido_materno'];
                }

                // Construir nombre completo para la sesión
                $nombre_completo = trim(
                    ($input['primer_nombre'] ?? '') . ' ' .
                        ($input['segundo_nombre'] ?? '') . ' ' .
                        ($input['apellido_paterno'] ?? '') . ' ' .
                        ($input['apellido_materno'] ?? '')
                );
            }

            $data['correo'] = $input['correo'];
            $data['telefono'] = $input['telefono'] ?? null;

            // Si se proporciona nueva contraseña
            if (!empty($input['clave_nueva'])) {
                // Verificar contraseña actual
                $usuario = $this->usuarioModel->find($user['id']);
                if (!password_verify($input['clave_actual'], $usuario['clave'])) {
                    echo json_encode(['success' => false, 'message' => 'Contraseña actual incorrecta']);
                    exit;
                }

                $data['clave'] = password_hash($input['clave_nueva'], PASSWORD_DEFAULT);
            }

            $this->usuarioModel->update($user['id'], $data);

            // Actualizar sesión con el nombre completo construido
            $_SESSION['user_nombre'] = $nombre_completo;
            $_SESSION['user_correo'] = $data['correo'];

            echo json_encode(['success' => true, 'message' => 'Perfil actualizado correctamente']);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error al actualizar perfil: ' . $e->getMessage()]);
        }

        exit;
    }

    public function cambiarEstado()
    {
        AuthMiddleware::checkRole(['administrador']);

        header('Content-Type: application/json');

        $input = json_decode(file_get_contents('php://input'), true);
        $usuario_id = $input['usuario_id'] ?? null;
        $estado = $input['estado'] ?? null;

        if (!$usuario_id || !$estado) {
            echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
            exit;
        }

        try {
            $this->usuarioModel->update($usuario_id, ['estado' => $estado]);
            echo json_encode(['success' => true, 'message' => 'Estado actualizado correctamente']);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error al actualizar estado']);
        }

        exit;
    }
}
