<?php

declare(strict_types=1);

namespace App\Middlewares;

use App\Models\Caja;
use Leaf\Http\Session;

final class AuthMiddleware
{
    /** @return never|true */
    public static function check(): bool
    {
        // Verificar integridad de la sesión de usuario
        if (!self::isAuthenticated()) {
            // Si es una petición AJAX/JSON, devolver 401 en lugar de redireccionar
            if (
                (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest')
                || (isset($_SERVER['HTTP_ACCEPT']) && str_contains($_SERVER['HTTP_ACCEPT'], 'application/json'))
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
        if (!Session::has('user_rol') || !Session::has('user_nombre')) {
            // Limpiar solo datos de usuario, no toda la sesión (preservar CSRF si es posible)
            Session::unset('user_id');
            Session::unset('user_rol');
            Session::unset('user_nombre');
            header('Location: /login');

            exit;
        }

        // Bloqueo si no hay caja abierta (excepto para administradores en módulos de sistema)
        return self::checkCaja();
    }

    /**
     * Verifica si hay una caja abierta para la sucursal actual
     * @return never|true
     */
    private static function checkCaja(): bool
    {
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        // Rutas exceptuadas de comprobación de caja
        $excepciones = [
            '/login',
            '/logout',
            '/cajas/abrir',
            '/cajas/aprir',
            '/auth/verificar-sucursal',
            '/auth/cambiar-sucursal',
            '/cajas',
        ];

        // Si el usuario es administrador, permitir acceso a módulos de sistema sin caja abierta
        if (Session::get('user_rol') === 'administrador') {
            foreach (['/usuarios', '/sucursales', '/auditorias', '/respaldos', '/config'] as $s) {
                if (str_starts_with($path, $s)) {
                    return true;
                }
            }
        }

        if (in_array($path, $excepciones)) {
            return true;
        }

        $cajaModel = new Caja();
        $id_sucursal = Session::get('sucursal_id');

        if ($id_sucursal && !$cajaModel->getActiva($id_sucursal)) {
            Session::set('flash_message', [
                'type' => 'warning',
                'content' => 'Debes realizar la apertura de caja antes de continuar.'
            ]);

            header('Location: /cajas/aprir');

            exit;
        }

        return true;
    }

    /** @return never|true */
    public static function checkAuth(): bool
    {
        return self::check();
    }

    /**
     * @param list<'administrador'|'empleado'|'cajero'|'repartidor'> $roles
     * @return never|true
     */
    public static function checkRole(array $roles = []): bool
    {
        self::check();

        if (!empty($roles) && !in_array(Session::get('user_rol'), $roles)) {
            header('Location: /dashboard');

            exit;
        }

        return true;
    }

    public static function isAuthenticated(): bool
    {
        return Session::has('user_id');
    }

    /**
     * @return array{
     *   id: ?int,
     *   nombre: ?string,
     *   correo: ?string,
     *   rol: null|'administrador'|'empleado'|'cajero'|'repartidor',
     *   sucursal_id: ?int,
     *   sucursal_nombre: ?string,
     * }
     */
    public static function getUser(): array
    {
        return [
            'id' => Session::get('user_id'),
            'nombre' => Session::get('user_nombre'),
            'correo' => Session::get('user_correo'),
            'rol' => Session::get('user_rol'),
            'sucursal_id' => Session::get('sucursal_id'),
            'sucursal_nombre' => Session::get('sucursal_nombre'),
        ];
    }

    /**
     * @param array{
     *   id: int,
     *   primer_nombre: string,
     *   apellido_paterno: string,
     *   correo: string,
     *   rol: 'administrador'|'empleado'|'cajero'|'repartidor',
     *   id_sucursal: int,
     * } $user
     */
    public static function setUser(array $user): void
    {
        Session::regenerate(true);
        Session::set('user_id', $user['id']);
        Session::set('user_nombre', "{$user['primer_nombre']} {$user['apellido_paterno']}");
        Session::set('user_correo', $user['correo']);
        Session::set('user_rol', $user['rol']);
        Session::set('sucursal_id', $user['id_sucursal']);
    }

    /** @return never */
    public static function logout(): void
    {
        Session::clear();
        Session::destroy();

        header('Location: /login');

        exit;
    }
}
