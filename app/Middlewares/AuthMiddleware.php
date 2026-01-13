<?php

namespace App\Middlewares;

class AuthMiddleware {
    
    public static function check() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Verificar integridad completa de la sesión
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_rol']) || !isset($_SESSION['user_nombre'])) {
            // Si falta algún dato crítico, cerrar sesión para evitar errores
            session_unset();
            session_destroy();
            header('Location: /login');
            exit;
        }
        
        return true;
    }
    
    // Alias para checkAuth
    public static function checkAuth() {
        return self::check();
    }
    
    public static function checkRole($roles = []) {
        self::check();
        
        if (!empty($roles) && !in_array($_SESSION['user_rol'], $roles)) {
            header('Location: /dashboard');
            exit;
        }
        
        return true;
    }
    
    public static function isAuthenticated() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        return isset($_SESSION['user_id']);
    }
    
    public static function getUser() {
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
    
    public static function setUser($user) {
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
    
    public static function logout() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        session_unset();
        session_destroy();
        
        header('Location: /login');
        exit;
    }
}
