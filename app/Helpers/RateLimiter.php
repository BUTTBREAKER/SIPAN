<?php

namespace App\Helpers;

/**
 * Helper para implementar rate limiting
 */
class RateLimiter
{
    /**
     * Verificar si se excedió el límite de intentos
     *
     * @param string $key Clave única para el rate limit (ej: 'login:192.168.1.1')
     * @param int $maxAttempts Número máximo de intentos permitidos
     * @param int $windowSeconds Ventana de tiempo en segundos
     * @return bool True si se puede proceder, False si se excedió el límite
     */
    public static function attempt($key, $maxAttempts = 5, $windowSeconds = 300)
    {
        $file = self::getFilePath($key);
        $attempts = self::getAttempts($file);

        // Limpiar intentos antiguos
        $now = time();
        $attempts = array_filter($attempts, function ($timestamp) use ($now, $windowSeconds) {
            return ($now - $timestamp) < $windowSeconds;
        });

        // Verificar si se excedió el límite
        if (count($attempts) >= $maxAttempts) {
            self::saveAttempts($file, $attempts);
            return false;
        }

        // Registrar nuevo intento
        $attempts[] = $now;
        self::saveAttempts($file, $attempts);

        return true;
    }

    /**
     * Registrar un intento
     */
    public static function hit($key)
    {
        $file = self::getFilePath($key);
        $attempts = self::getAttempts($file);
        $attempts[] = time();
        self::saveAttempts($file, $attempts);
    }

    /**
     * Obtener número de intentos restantes
     */
    public static function remaining($key, $maxAttempts = 5, $windowSeconds = 300)
    {
        $file = self::getFilePath($key);
        $attempts = self::getAttempts($file);

        // Limpiar intentos antiguos
        $now = time();
        $attempts = array_filter($attempts, function ($timestamp) use ($now, $windowSeconds) {
            return ($now - $timestamp) < $windowSeconds;
        });

        return max(0, $maxAttempts - count($attempts));
    }

    /**
     * Obtener tiempo en segundos hasta que se resetee el límite
     */
    public static function availableIn($key, $windowSeconds = 300)
    {
        $file = self::getFilePath($key);
        $attempts = self::getAttempts($file);

        if (empty($attempts)) {
            return 0;
        }

        $now = time();
        $oldestAttempt = min($attempts);

        $timeElapsed = $now - $oldestAttempt;
        $timeRemaining = max(0, $windowSeconds - $timeElapsed);

        return $timeRemaining;
    }

    /**
     * Limpiar todos los intentos de una clave
     */
    public static function clear($key)
    {
        $file = self::getFilePath($key);
        if (file_exists($file)) {
            unlink($file);
        }
    }

    /**
     * Obtener ruta del archivo de rate limit
     */
    private static function getFilePath($key)
    {
        $dir = dirname(__DIR__, 2) . '/logs/rate-limits';

        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $hash = md5($key);
        return $dir . '/' . $hash . '.json';
    }

    /**
     * Obtener intentos desde archivo
     */
    private static function getAttempts($file)
    {
        if (!file_exists($file)) {
            return [];
        }

        $content = file_get_contents($file);
        $data = json_decode($content, true);

        return is_array($data) ? $data : [];
    }

    /**
     * Guardar intentos en archivo
     */
    private static function saveAttempts($file, $attempts)
    {
        file_put_contents($file, json_encode(array_values($attempts)));
    }
}
