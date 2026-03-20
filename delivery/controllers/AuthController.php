<?php

namespace Delivery\Controllers;

use App\Models\Usuario;

class AuthController
{
    private $usuarioModel;

    public function __construct()
    {
        // Reutilizamos el modelo de la app principal
        $this->usuarioModel = new Usuario();
    }

    public function showLogin()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Si ya está logueado como repartidor, ir al dashboard
        if (isset($_SESSION['user_id']) && ($_SESSION['user_rol'] === 'repartidor' || $_SESSION['user_rol'] === 'administrador')) {
            header('Location: /delivery/dashboard');
            exit;
        }

        require_once __DIR__ . '/../views/login.php';
    }

    public function login()
    {
        header('Content-Type: application/json');

        $correo = $_POST['correo'] ?? '';
        $clave = $_POST['clave'] ?? '';

        // Validar CSRF
        require_once __DIR__ . '/../../app/Helpers/CSRF.php';
        if (!\App\Helpers\CSRF::validateRequest()) {
            echo json_encode(['success' => false, 'message' => 'Token de seguridad inválido. Por favor, recarga la página.']);
            return;
        }

        if (empty($correo) || empty($clave)) {
            echo json_encode(['success' => false, 'message' => 'Por favor, complete todos los campos']);
            return;
        }

        $usuario = $this->usuarioModel->findByEmail($correo);

        if (!$usuario || !password_verify($clave, $usuario['clave'])) {
            echo json_encode(['success' => false, 'message' => 'Credenciales incorrectas']);
            return;
        }

        if ($usuario['estado'] !== 'activo') {
            echo json_encode(['success' => false, 'message' => 'Usuario inactivo']);
            return;
        }

        // Opcional: Solo permitir rol de repartidor
        if ($usuario['rol'] !== 'repartidor' && $usuario['rol'] !== 'administrador') {
            echo json_encode(['success' => false, 'message' => 'Acceso denegado. Esta app es exclusiva para repartidores.']);
            return;
        }

        // Crear sesión
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        session_regenerate_id(true);

        $_SESSION['user_id'] = $usuario['id'];
        $_SESSION['user_nombre'] = $usuario['primer_nombre'] . ' ' . $usuario['apellido_paterno'];
        $_SESSION['user_correo'] = $usuario['correo'];
        $_SESSION['user_rol'] = $usuario['rol'];
        $_SESSION['sucursal_id'] = $usuario['id_sucursal'];

        // Obtener nombre de sucursal
        require_once __DIR__ . '/../../app/Models/Sucursal.php';
        $sucursalModel = new \App\Models\Sucursal();
        $sucursal = $sucursalModel->find($usuario['id_sucursal']);
        $_SESSION['sucursal_nombre'] = $sucursal['nombre'] ?? '';

        echo json_encode(['success' => true, 'redirect' => '/delivery/dashboard']);
    }

    public function logout()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        session_unset();
        session_destroy();
        
        header('Location: /delivery/login');
        exit;
    }
}
