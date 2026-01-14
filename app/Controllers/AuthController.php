<?php

namespace App\Controllers;

use App\Models\Usuario;
use App\Models\Sucursal;
use App\Middlewares\AuthMiddleware;

class AuthController
{
    private $usuarioModel;
    private $sucursalModel;

    public function __construct()
    {
        $this->usuarioModel = new Usuario();
        $this->sucursalModel = new Sucursal();
    }

    public function showLogin()
    {
        if (AuthMiddleware::isAuthenticated()) {
            header('Location: /dashboard');
            exit;
        }

        require_once __DIR__ . '/../Views/auth/login.php';
    }

    public function login()
    {
        // Limpiar cualquier salida previa para asegurar un JSON válido
        if (ob_get_length()) {
            ob_clean();
        }
        header('Content-Type: application/json');

        // Cargar configuración
        $config = $_ENV;

        // Obtener datos
        $correo = $_POST['correo'] ?? '';
        $clave = $_POST['clave'] ?? '';

        // Validar campos requeridos
        if (empty($correo) || empty($clave)) {
            echo json_encode(['success' => false, 'message' => 'Correo y contraseña son requeridos']);
            exit;
        }

        // Validar CSRF token
        require_once __DIR__ . '/../Helpers/CSRF.php';
        if (!\App\Helpers\CSRF::validateRequest()) {
            echo json_encode(['success' => false, 'message' => 'Token de seguridad inválido. Por favor, recarga la página.']);
            exit;
        }

        // Rate Limiting por IP
        require_once __DIR__ . '/../Helpers/RateLimiter.php';
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $rateLimitKey = 'login:' . $ip;

        $maxAttempts = $config['rate_limit_login_max_attempts'];
        $window = $config['rate_limit_login_window'];

        if (!\App\Helpers\RateLimiter::attempt($rateLimitKey, $maxAttempts, $window)) {
            $availableIn = \App\Helpers\RateLimiter::availableIn($rateLimitKey, $window);
            $minutes = ceil($availableIn / 60);

            echo json_encode([
                'success' => false,
                'message' => "Demasiados intentos fallidos. Intenta nuevamente en $minutes minuto(s).",
                'retry_after' => $availableIn
            ]);
            exit;
        }

        // Autenticar usuario
        $user = $this->usuarioModel->authenticate($correo, $clave);

        if ($user) {
            if ($user['estado'] !== 'activo') {
                echo json_encode(['success' => false, 'message' => 'Usuario inactivo']);
                exit;
            }

            // Login exitoso - limpiar rate limit
            \App\Helpers\RateLimiter::clear($rateLimitKey);

            AuthMiddleware::setUser($user);

            // Obtener nombre de sucursal
            if ($user['id_sucursal']) {
                $sucursal = $this->sucursalModel->find($user['id_sucursal']);
                $_SESSION['sucursal_nombre'] = $sucursal['nombre'] ?? '';
            }

            echo json_encode(['success' => true, 'message' => 'Inicio de sesión exitoso']);
        } else {
            // Credenciales incorrectas
            $remaining = \App\Helpers\RateLimiter::remaining($rateLimitKey, $maxAttempts, $window);

            echo json_encode([
                'success' => false,
                'message' => 'Credenciales incorrectas',
                'attempts_remaining' => $remaining
            ]);
        }
        exit;
    }

    public function logout()
    {
        AuthMiddleware::logout();
    }

    public function cambiarSucursal()
    {
        AuthMiddleware::checkRole(['administrador']);

        if (ob_get_length()) {
            ob_clean();
        }
        header('Content-Type: application/json');

        $sucursal_id = $_POST['sucursal_id'] ?? null;

        if (!$sucursal_id) {
            echo json_encode(['success' => false, 'message' => 'Sucursal no válida']);
            exit;
        }

        $sucursal = $this->sucursalModel->find($sucursal_id);

        if ($sucursal) {
            $_SESSION['sucursal_id'] = $sucursal['id'];
            $_SESSION['sucursal_nombre'] = $sucursal['nombre'];
            echo json_encode(['success' => true, 'message' => 'Sucursal cambiada correctamente']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Sucursal no encontrada']);
        }
        exit;
    }

    public function showRegister()
    {
        require_once __DIR__ . '/../Views/auth/register.php';
    }

    public function register()
    {
        if (ob_get_length()) {
            ob_clean();
        }
        header('Content-Type: application/json');

        $input = json_decode(file_get_contents('php://input'), true);

        // Validar CSRF para peticiones JSON (desde header X-CSRF-Token o input oculto)
        require_once __DIR__ . '/../Helpers/CSRF.php';
        if (!\App\Helpers\CSRF::validateRequest()) {
            echo json_encode(['success' => false, 'message' => 'Token de seguridad inválido. Recarga la página.']);
            exit;
        }

        if (!$input) {
            echo json_encode(['success' => false, 'message' => 'No se recibieron datos']);
            exit;
        }

        // Validaciones básicas
        if (
            empty($input['primer_nombre']) || empty($input['apellido_paterno']) ||
            empty($input['dni']) || empty($input['telefono']) ||
            empty($input['correo']) || empty($input['clave']) ||
            empty($input['id_sucursal']) || empty($input['rol'])
        ) {
            echo json_encode(['success' => false, 'message' => 'Complete todos los campos requeridos']);
            exit;
        }

        if (!in_array($input['rol'], ['cajero', 'empleado'])) {
            echo json_encode(['success' => false, 'message' => 'Rol no válido']);
            exit;
        }

        // Validación de email existente
        if ($this->usuarioModel->findByEmail($input['correo'])) {
            echo json_encode(['success' => false, 'message' => 'El correo ya está registrado']);
            exit;
        }

        // Validación de DNI existente
        if ($this->usuarioModel->findByDNI($input['dni'])) {
            echo json_encode(['success' => false, 'message' => 'La cédula/RIF ya está registrada']);
            exit;
        }

        // Validar sucursal
        $sucursal = $this->sucursalModel->find($input['id_sucursal']);

        if (!$sucursal) {
            echo json_encode(['success' => false, 'message' => 'Sucursal no encontrada']);
            exit;
        }

        if (empty($sucursal['negocio_id'])) {
            echo json_encode(['success' => false, 'message' => 'Error interno: negocio_id no encontrado']);
            exit;
        }

        try {
            $data = [
                'primer_nombre'     => $input['primer_nombre'],
                'segundo_nombre'    => $input['segundo_nombre'] ?? '',
                'apellido_paterno'  => $input['apellido_paterno'],
                'apellido_materno'  => $input['apellido_materno'] ?? '',
                'dni'               => $input['dni'],
                'telefono'          => $input['telefono'],
                'correo'            => $input['correo'],
                'clave'             => $input['clave'],
                'rol'               => $input['rol'],
                'estado'            => 'activo',
                'id_sucursal'       => $input['id_sucursal'],
            ];

            $usuario_id = $this->usuarioModel->register($data);

            echo json_encode([
                'success' => true,
                'message' => 'Usuario registrado exitosamente',
                'usuario_id' => $usuario_id
            ]);
        } catch (\Exception $e) {
            error_log("REGISTER ERROR: " . $e->getMessage());

            echo json_encode([
                'success' => false,
                'message' => 'Error interno del servidor'
            ]);
        }

        exit;
    }


    public function verificarSucursal()
    {
        if (ob_get_length()) {
            ob_clean();
        }
        header('Content-Type: application/json');

        $input = json_decode(file_get_contents('php://input'), true);
        $clave = $input['clave'] ?? '';

        if (empty($clave)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Clave requerida']);
            exit;
        }

        $sucursal = $this->sucursalModel->findByClave($clave);

        if ($sucursal) {
            echo json_encode([
                'success' => true,
                'sucursal' => [
                    'id' => $sucursal['id'],
                    'nombre' => $sucursal['nombre'],
                    'direccion' => $sucursal['direccion']
                ]
            ]);
        } else {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Clave de sucursal inválida']);
        }

        exit;
    }

    private function generarClaveSucursal()
    {
        // Generar clave alfanumérica de 8 caracteres
        $caracteres = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        $clave = '';
        for ($i = 0; $i < 8; $i++) {
            $clave .= $caracteres[rand(0, strlen($caracteres) - 1)];
        }
        return $clave;
    }

    public function verificarClaveSucursal()
    {
        if (ob_get_length()) {
            ob_clean();
        }
        header('Content-Type: application/json');

        $data = json_decode(file_get_contents('php://input'), true);
        $clave_sucursal = strtoupper($data['clave_sucursal'] ?? '');

        if (empty($clave_sucursal)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Clave de sucursal requerida']);
            exit;
        }

        $sucursal = $this->sucursalModel->findByClave($clave_sucursal);

        if ($sucursal && $sucursal['estado'] === 'activa') {
            echo json_encode([
                'success' => true,
                'sucursal' => [
                    'id' => $sucursal['id'],
                    'nombre' => $sucursal['nombre'],
                    'direccion' => $sucursal['direccion']
                ]
            ]);
        } else {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Clave de sucursal inválida o sucursal inactiva']);
        }
        exit;
    }
}
