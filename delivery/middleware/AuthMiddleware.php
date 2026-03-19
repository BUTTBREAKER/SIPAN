<?php

namespace Delivery\Middleware;

class AuthMiddleware
{
    public static function check()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Si no hay sesión, al login
        if (!isset($_SESSION['user_id'])) {
            header('Location: /delivery/login');
            exit;
        }

        // Si hay sesión pero no es repartidor, lo sacamos por seguridad
        // Un administrador sí podría entrar para revisar la app si lo deseamos, 
        // pero por ahora estrictamente 'repartidor'
        if ($_SESSION['user_rol'] !== 'repartidor' && $_SESSION['user_rol'] !== 'administrador') {
            session_unset();
            session_destroy();
            header('Location: /delivery/login?error=unauthorized');
            exit;
        }

        return true;
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
}
