<?php

use Symfony\Component\Dotenv\Dotenv;

require_once __DIR__ . '/../vendor/autoload.php';

// SIPAN - Sistema Integral para Panaderías
// Archivo principal de enrutamiento

// Habilitar buffering de salida para prevenir errores de cabeceras
ob_start();

// Cargar configuración
(new Dotenv())->load(__DIR__ . '/../.env.example', __DIR__ . '/../.env');
$_ENV['app_debug'] = filter_var($_ENV['app_debug'], FILTER_VALIDATE_BOOL);

// Configurar errores según entorno
if ($_ENV['app_env'] === 'production') {
    // Producción: No mostrar errores en pantalla, solo registrar en log
    error_reporting(E_ALL);
    ini_set('display_errors', false);
    ini_set('error_log', __DIR__ . '/../storage/logs/php-errors.log');
} else {
    // Desarrollo: Mostrar todos los errores
    error_reporting(E_ALL);
    ini_set('display_errors', $_ENV['app_debug']);
    ini_set('error_log', __DIR__ . '/../storage/logs/sipan-debug.log');
}

// Detectar si estamos detrás de un proxy/túnel con HTTPS
$isSecure = @$_SERVER['HTTPS'] === 'on' || @$_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https';

// Configurar parámetros de la cookie de sesión ANTES de iniciar la sesión
$sessionParams = session_get_cookie_params();

session_set_cookie_params([
    'lifetime' => $_ENV['session_lifetime'] ?? 86400 /* 1 day */,
    'path' => $sessionParams['path'],
    'domain' => $sessionParams['domain'],
    'secure' => $isSecure,
    'httponly' => true,
    'samesite' => 'Lax' // Necesario para túneles y navegadores modernos
]);

if (session_status() === PHP_SESSION_NONE) {
    session_name($_ENV['session_name'] ?? 'SIPAN_SESSION');
    session_start();
}

// Habilitar log de debug
ini_set('log_errors', true);

// Obtener la ruta solicitada
$request_uri = $_SERVER['REQUEST_URI'];
$path = parse_url($request_uri, PHP_URL_PATH);

// Asegurar que la ruta comience con /
if (!str_starts_with($path, '/')) {
    $path = "/$path";
}

// Remover index.php si está presente
$path = str_replace('/index.php', '', $path);

// Limpiar la ruta
$path = rtrim($path, '/') ?: '/';

// Debug (comentar en producción)
if ($_ENV['app_debug']) {
    error_log("Path: $path, Method: {$_SERVER['REQUEST_METHOD']}, URI: $request_uri");
} else {
    // Log todas las peticiones
    $log_message = "{$_SERVER['REQUEST_METHOD']} | {$_SERVER['REQUEST_URI']}";
    error_log($log_message);
}

// Método HTTP
$method = $_SERVER['REQUEST_METHOD'];

// Enrutador
$routes = [];

foreach (glob(__DIR__ . '/../routes/*.php') as $routesFilePath) {
    $routes += require $routesFilePath;
}

// Buscar ruta coincidente
$matched = false;
$params = [];

foreach ($routes as $route => $handler) {
    list($routeMethod, $routePath) = explode('|', $route);

    if ($routeMethod !== $method) {
        continue;
    }

    $params = matchRoute($routePath, $path);

    if ($params !== false) {
        $matched = true;
        $controllerName = 'App\\Controllers\\' . $handler[0];
        $methodName = $handler[1];

        try {
            if (class_exists($controllerName)) {
                $controller = new $controllerName();

                if (method_exists($controller, $methodName)) {
                    call_user_func_array([$controller, $methodName], $params);
                } else {
                    http_response_code(500);
                    if (
                        $_SERVER['HTTP_ACCEPT'] === 'application/json'
                        || strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false
                    ) {
                        header('Content-Type: application/json');
                        echo json_encode(['success' => false, 'message' => "Método no encontrado: {$methodName}"]);
                    } else {
                        echo "Método no encontrado: {$methodName}";
                    }
                }
            } else {
                http_response_code(500);
                if (
                    $_SERVER['HTTP_ACCEPT'] === 'application/json'
                    || strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false
                ) {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'message' => "Controlador no encontrado: {$controllerName}"]);
                } else {
                    echo "Controlador no encontrado: {$controllerName}";
                }
            }
        } catch (Exception $e) {
            http_response_code(500);
            if (
                $_SERVER['HTTP_ACCEPT'] === 'application/json'
                || strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false
            ) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
            } else {
                echo "Error: " . $e->getMessage();
            }
        }

        break;
    }
}

// Si no se encontró ruta, mostrar 404
if (!$matched) {
    http_response_code(404);
    echo "<!DOCTYPE html>
    <html lang='es'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>404 - Página no encontrada</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
                margin: 0;
                background: linear-gradient(135deg, #D4A574 0%, #8B6F47 100%);
                color: white;
            }
            .error-container {
                text-align: center;
            }
            .error-code {
                font-size: 8rem;
                font-weight: bold;
                margin: 0;
            }
            .error-message {
                font-size: 1.5rem;
                margin: 1rem 0;
            }
            .error-link {
                display: inline-block;
                margin-top: 2rem;
                padding: 1rem 2rem;
                background: white;
                color: #8B6F47;
                text-decoration: none;
                border-radius: 8px;
                font-weight: bold;
            }
        </style>
    </head>
    <body>
        <div class='error-container'>
            <h1 class='error-code'>404</h1>
            <p class='error-message'>Página no encontrada</p>
            <p>La ruta solicitada no existe en el sistema.</p>
            <p>Ruta: {$path}</p>
            <a href='/dashboard' class='error-link'>Volver al Dashboard</a>
        </div>
    </body>
    </html>";
}
