<?php

declare(strict_types=1);

namespace App\Helpers;

use AssertionError;
use Leaf\Http\Session;

/** Helper para generar y validar tokens CSRF */
final class CSRF
{
    /** Generar un token CSRF */
    private static function generateToken(): string
    {
        $token = bin2hex(random_bytes(32));
        Session::set('csrf_token', $token);
        Session::set('csrf_token_time', time());

        return $token;
    }

    /**
     * Obtener el token CSRF actual
     * @throws AssertionError
     */
    public static function getToken(): string
    {
        if (!Session::has('csrf_token')) {
            return self::generateToken();
        }

        // Regenerar token si tiene más de 1 hora
        if (self::tokenHasExpired()) {
            return self::generateToken();
        }

        $token = Session::get('csrf_token');
        assert(is_string($token), 'csrf_token debe ser una cadena');

        return $token;
    }

    /** @throws AssertionError */
    private static function tokenHasExpired(): bool
    {
        $tokenTime = Session::get('csrf_token_time');
        assert(is_int($tokenTime), 'csrf_token_time debe ser un entero');

        return Session::has('csrf_token_time') && (time() - $tokenTime > 3600);
    }

    /**
     * Validar token CSRF
     * @throws AssertionError
     */
    public static function validateToken(string $token): bool
    {
        if (!Session::has('csrf_token')) {
            return false;
        }

        // Verificar si el token ha expirado (1 hora)
        if (self::tokenHasExpired()) {
            return false;
        }

        return hash_equals(self::getToken(), $token);
    }

    /**
     * Generar campo hidden HTML con token CSRF
     * @throws AssertionError
     */
    public static function field(): string
    {
        $token = self::getToken();

        return "<input type='hidden' name='csrf_token' value='$token'>";
    }

    /** Obtener token desde request (POST o header) */
    private static function getTokenFromRequest(): ?string
    {
        // Intentar obtener desde POST
        if (isset($_POST['csrf_token'])) {
            $token = $_POST['csrf_token'];

            return is_string($token) ? $token : null;
        }

        // Intentar obtener desde header
        $headers = getallheaders();

        if (is_array($headers) && isset($headers['X-CSRF-Token'])) {
            $token = $headers['X-CSRF-Token'];

            return is_string($token) ? $token : null;
        }

        return null;
    }

    /**
     * Validar request actual
     * @throws AssertionError
     */
    public static function validateRequest(): bool
    {
        $token = self::getTokenFromRequest();

        return $token ? self::validateToken($token) : false;
    }
}
