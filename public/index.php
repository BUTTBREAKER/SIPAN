<?php

use flight\Container;
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
error_reporting(E_ALL);

if ($_ENV['app_env'] === 'production') {
    // Producción: No mostrar errores en pantalla, solo registrar en log
    ini_set('display_errors', 'Off');
    ini_set('error_log', __DIR__ . '/../storage/logs/php-errors.log');
} else {
    // Desarrollo: Mostrar todos los errores
    ini_set('display_errors', $_ENV['app_debug']);
    ini_set('error_log', __DIR__ . '/../storage/logs/sipan-debug.log');
}

// Detectar la ruta ANTES de iniciar la sesión para poder variar el nombre de la sesión
$request_uri = $_SERVER['REQUEST_URI'] ?? '/';
$path = parse_url($request_uri, PHP_URL_PATH) ?: '/';

// Asegurar que la ruta comience con /
if (!str_starts_with($path, '/')) {
    $path = "/$path";
}

// Detectar si la ruta es para el sistema de delivery
$isDeliveryPath = str_contains($path, '/delivery');

// Detectar si estamos detrás de un proxy/túnel con HTTPS
$isSecure = @$_SERVER['HTTPS'] === 'on' || @$_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https';

// Configurar parámetros de la cookie de sesión ANTES de iniciar la sesión
$sessionParams = session_get_cookie_params();

session_set_cookie_params([
    'lifetime' => $_ENV['session_lifetime'],
    'path' => $sessionParams['path'],
    'domain' => $sessionParams['domain'],
    'secure' => $isSecure,
    'httponly' => true,
    'samesite' => 'Lax',
]);

if (session_status() === PHP_SESSION_NONE) {
    // Nombre de sesión dinámico para permitir múltiples sesiones independientes en la misma red/dominio
    $baseSessionName = $_ENV['session_name'];
    $finalSessionName = $isDeliveryPath ? $baseSessionName . '_DELIVERY' : $baseSessionName;

    session_name($finalSessionName);
    session_start();
}

// Habilitar log de debug
ini_set('log_errors', 'Off');

// La ruta ya fue detectada arriba
// ------ INTEGRACIÓN APP DELIVERY (Pivote de enrutamiento) ------
// Si la ruta empieza con /delivery y no es un archivo físico (ya manejado por el servidor)
if ($isDeliveryPath) {
    require_once __DIR__ . '/../delivery/index.php';

    exit;
}
// ---------------------------------------------------------------

// Remover el subdirectorio base si no estamos en la raíz
$script_name = dirname($_SERVER['SCRIPT_NAME']);

// Detectar protocolo (compatible con proxy/túnel como Cloudflare)
$protocol = $isSecure ? 'https' : 'http';

$base_url = $protocol . '://' . $_SERVER['HTTP_HOST'] . $script_name;

define('BASE_URL', rtrim($base_url, '/\\') . '/');

if (!in_array($script_name, ['/', '\\'])) {
    $path = str_replace($script_name, '', $path);
}

// Remover index.php si está presente
$path = str_replace('/index.php', '', $path);

// Limpiar la ruta
$path = rtrim($path, '/') ?: '/';

// Método HTTP
$method = $_SERVER['REQUEST_METHOD'];

// Debug (comentar en producción)
error_log(
    $_ENV['app_debug']
        ? "Path: $path, Method: $method, URI: $request_uri"
        : "$method | $request_uri"
);

// Enrutador
$routes = [];

foreach (glob(__DIR__ . '/../routes/*.php') ?: [] as $routesFilePath) {
    $routes += require $routesFilePath;
}

// Buscar ruta coincidente
$matched = false;
$params = [];
$acceptJson = str_contains($_SERVER['HTTP_ACCEPT'], 'application/json');

foreach ($routes as $route => [$controllerName, $controllerMethod]) {
    [$routeMethod, $routePath] = explode('|', $route);

    if ($routeMethod !== $method) {
        continue;
    }

    $params = matchRoute($routePath, $path);

    if ($params === false) {
        continue;
    }

    $matched = true;
    $methodName = $controllerMethod;

    try {
        if (!class_exists($controllerName)) {
            throw new Exception("Controlador no encontrado: $controllerName");
        }

        $controller = Container::getInstance()->get($controllerName);

        if (!method_exists($controller, $methodName)) {
            throw new Exception("Método no encontrado: $methodName");
        }

        call_user_func_array([$controller, $methodName], $params);
    } catch (Throwable $exception) {
        http_response_code(500);
        $message = "Error: {$exception->getMessage()}";

        if ($acceptJson) {
            header('Content-Type: application/json');

            echo json_encode([
                'success' => false,
                'message' => $message,
            ]);
        } else {
            echo $message;
        }
    }

    break;
}

// Si no se encontró ruta, mostrar 404
if (!$matched) {
    http_response_code(404);

    require __DIR__ . '/../app/Views/404.php';
}
