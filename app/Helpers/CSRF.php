<?php

namespace App\Helpers;

/**
 * Helper para generar y validar tokens CSRF
 */
class CSRF
{
    /**
     * Generar un token CSRF
     */
    public static function generateToken()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $token = bin2hex(random_bytes(32));
        $_SESSION['csrf_token'] = $token;
        $_SESSION['csrf_token_time'] = time();

        return $token;
    }

    /**
     * Obtener el token CSRF actual
     */
    public static function getToken()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['csrf_token'])) {
            return self::generateToken();
        }

        // Regenerar token si tiene más de 1 hora
        if (isset($_SESSION['csrf_token_time']) && (time() - $_SESSION['csrf_token_time']) > 3600) {
            return self::generateToken();
        }

        return $_SESSION['csrf_token'];
    }

    /**
     * Validar token CSRF
     */
    public static function validateToken($token)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['csrf_token'])) {
            return false;
        }

        // Verificar si el token ha expirado (1 hora)
        if (isset($_SESSION['csrf_token_time']) && (time() - $_SESSION['csrf_token_time']) > 3600) {
            return false;
        }

        return hash_equals($_SESSION['csrf_token'], $token);
    }

    /**
     * Generar campo hidden HTML con token CSRF
     */
    public static function field()
    {
        $token = self::getToken();
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
    }

    /**
     * Obtener token desde request (POST o header)
     */
    public static function getTokenFromRequest()
    {
        // Intentar obtener desde POST
        if (isset($_POST['csrf_token'])) {
            return $_POST['csrf_token'];
        }

        // Intentar obtener desde header
        $headers = getallheaders();
        if (isset($headers['X-CSRF-Token'])) {
            return $headers['X-CSRF-Token'];
        }

        return null;
    }

    /**
     * Validar request actual
     */
    public static function validateRequest()
    {
        $token = self::getTokenFromRequest();

        if (!$token) {
            return false;
        }

        return self::validateToken($token);
    }
}
