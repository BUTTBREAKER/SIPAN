<?php

namespace App\Helpers;

/**
 * Helper para cargar variables de entorno desde archivo .env
 */
class Environment
{
    private static $loaded = false;
    private static $vars = [];

    /**
     * Cargar variables de entorno desde archivo .env
     */
    public static function load($path = null)
    {
        if (self::$loaded) {
            return;
        }

        if ($path === null) {
            $path = dirname(__DIR__, 2) . '/.env';
        }

        if (!file_exists($path)) {
            // Si no existe .env, intentar cargar desde .env.example
            $examplePath = dirname(__DIR__, 2) . '/.env.example';
            if (file_exists($examplePath)) {
                $path = $examplePath;
            } else {
                throw new \Exception('.env file not found');
            }
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {
            // Ignorar comentarios
            if (strpos(trim($line), '#') === 0) {
                continue;
            }

            // Parsear línea
            if (strpos($line, '=') !== false) {
                list($name, $value) = explode('=', $line, 2);
                $name = trim($name);
                $value = trim($value);

                // Remover comillas si existen
                if (preg_match('/^(["\'])(.*)\\1$/', $value, $matches)) {
                    $value = $matches[2];
                }

                // Convertir valores booleanos
                if (strtolower($value) === 'true') {
                    $value = true;
                } elseif (strtolower($value) === 'false') {
                    $value = false;
                } elseif (strtolower($value) === 'null') {
                    $value = null;
                }

                // Guardar en array estático y en $_ENV
                self::$vars[$name] = $value;
                $_ENV[$name] = $value;
                putenv("$name=$value");
            }
        }

        self::$loaded = true;
    }

    /**
     * Obtener valor de variable de entorno
     */
    public static function get($key, $default = null)
    {
        if (!self::$loaded) {
            self::load();
        }

        return self::$vars[$key] ?? getenv($key) ?: $default;
    }

    /**
     * Verificar si es entorno de producción
     */
    public static function isProduction()
    {
        return strtolower(self::get('APP_ENV', 'production')) === 'production';
    }

    /**
     * Verificar si es entorno de desarrollo
     */
    public static function isDevelopment()
    {
        return strtolower(self::get('APP_ENV', 'development')) === 'development';
    }

    /**
     * Verificar si debug está activado
     */
    public static function isDebug()
    {
        return self::get('APP_DEBUG', false) === true || self::get('APP_DEBUG', false) === 'true';
    }
}
