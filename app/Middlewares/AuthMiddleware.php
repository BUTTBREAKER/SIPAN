<?php

namespace App\Middlewares;

class AuthMiddleware
{
    public static function check()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Verificar integridad de la sesión de usuario
        if (!isset($_SESSION['user_id'])) {
            // Si es una petición AJAX/JSON, devolver 401 en lugar de redireccionar
            if (
                (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') ||
                (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)
            ) {
                http_response_code(401);
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Sesión expirada o no autorizada']);
                exit;
            }

            header('Location: /login');
            exit;
        }

        // Si existe user_id pero faltan datos críticos (integridad)
        if (!isset($_SESSION['user_rol']) || !isset($_SESSION['user_nombre'])) {
            // Limpiar solo datos de usuario, no toda la sesión (preservar CSRF si es posible)
            unset($_SESSION['user_id'], $_SESSION['user_rol'], $_SESSION['user_nombre']);
            header('Location: /login');
            exit;
        }

        // Bloqueo si no hay caja abierta (excepto para administradores en módulos de sistema)
        self::checkCaja();

        return true;
    }

    /**
     * Verifica si hay una caja abierta para la sucursal actual
     */
    public static function checkCaja()
    {
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        // Rutas exceptuadas de comprobación de caja
        $excepciones = [
            '/login', '/logout', '/cajas/abrir', '/cajas/abrir', '/auth/verificar-sucursal',
            '/auth/cambiar-sucursal', '/cajas'
        ];

        // Si el usuario es administrador, permitir acceso a módulos de sistema sin caja abierta
        if (($_SESSION['user_rol'] ?? '') === 'administrador') {
            $sistemaPaths = ['/usuarios', '/sucursales', '/auditorias', '/respaldos', '/config'];
            foreach ($sistemaPaths as $s) {
                if (strpos($path, $s) === 0) {
                    return true;
                }
            }
        }

        if (in_array($path, $excepciones)) {
            return true;
        }

        require_once __DIR__ . '/../Models/Caja.php';
        $cajaModel = new \App\Models\Caja();
        $id_sucursal = $_SESSION['sucursal_id'] ?? null;

        if ($id_sucursal && !$cajaModel->getActiva($id_sucursal)) {
            $_SESSION['flash_message'] = [
                'type' => 'warning',
                'content' => 'Debes realizar la apertura de caja antes de continuar.'
            ];
            header('Location: /cajas/abrir');
            exit;
        }

        return true;
    }

    // Alias para checkAuth
    public static function checkAuth()
    {
        return self::check();
    }

    public static function checkRole($roles = [])
    {
        self::check();

        if (!empty($roles) && !in_array($_SESSION['user_rol'], $roles)) {
            header('Location: /dashboard');
            exit;
        }

        return true;
    }

    public static function isAuthenticated()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        return isset($_SESSION['user_id']);
    }

    public static function getUser()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        return [
            'id' => $_SESSION['user_id'] ?? null,
            'nombre' => $_SESSION['user_nombre'] ?? null,
            'correo' => $_SESSION['user_correo'] ?? null,
            'rol' => $_SESSION['user_rol'] ?? null,
            'sucursal_id' => $_SESSION['sucursal_id'] ?? null,
            'sucursal_nombre' => $_SESSION['sucursal_nombre'] ?? null
        ];
    }

    public static function setUser($user)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        session_regenerate_id(true);

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_nombre'] = $user['primer_nombre'] . ' ' . $user['apellido_paterno'];
        $_SESSION['user_correo'] = $user['correo'];
        $_SESSION['user_rol'] = $user['rol'];
        $_SESSION['sucursal_id'] = $user['id_sucursal'];
    }

    public static function logout()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        session_unset();
        session_destroy();

        header('Location: /login');
        exit;
    }
}
