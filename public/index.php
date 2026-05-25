<?php

use App\Route;
use Leaf\Http\Session;
use Mpdf\PsrHttpMessageShim\Request;
use Mpdf\PsrHttpMessageShim\Response;
use Mpdf\PsrHttpMessageShim\Stream;
use Mpdf\PsrHttpMessageShim\Uri;
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

// Detectar si estamos detrás de un proxy/túnel con HTTPS
$isSecure = @$_SERVER['HTTPS'] === 'on' || @$_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https';

// Detectar protocolo (compatible con proxy/túnel como Cloudflare)
$scheme = $isSecure ? 'https' : 'http';

$headers = [];

foreach (headers_list() as $header) {
    [$headerName, $headerValues] = explode(':', $header);

    $headers[$headerName] = $headerValues;
}

$uri = (new Uri($_SERVER['REQUEST_URI']))
    ->withHost($_SERVER['SERVER_NAME'])
    ->withPort($_SERVER['SERVER_PORT'])
    ->withScheme($scheme);

$request = (new Request($_SERVER['REQUEST_METHOD'], $uri, $headers))
    ->withBody(Stream::createFromResource(fopen('php://input', 'r')))
    ->withProtocolVersion(ltrim($_SERVER['SERVER_PROTOCOL'], 'HTTP/'));

$response = (new Response)
    ->withBody(Stream::createFromResource(fopen('php://output', 'w')))
    ->withProtocolVersion($request->getProtocolVersion());

// Detectar si la ruta es para el sistema de delivery
$isDeliveryPath = str_contains($uri->getPath(), '/delivery');

// Configurar parámetros de la cookie de sesión ANTES de iniciar la sesión
$sessionParams = [
    'lifetime' => filter_var($_ENV['session_lifetime'], FILTER_VALIDATE_INT),
    'secure' => $isSecure,
    'httponly' => true,
    'samesite' => 'Strict',
] + session_get_cookie_params();

session_set_cookie_params($sessionParams);

if (session_status() === PHP_SESSION_NONE) {
    // Nombre de sesión dinámico para permitir múltiples sesiones independientes en la misma red/dominio
    $baseSessionName = $_ENV['session_name'];
    $finalSessionName = $isDeliveryPath ? "{$baseSessionName}_DELIVERY" : $baseSessionName;

    session_name($finalSessionName);
    Session::start();
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

define('BASE_URL', $uri->withQuery('')->__toString());

// Debug (comentar en producción)
error_log(
    $_ENV['app_debug']
        ? "Path: {$uri->getPath()}, Method: {$request->getMethod()}, URI: {$request->getRequestTarget()}"
        : "{$request->getMethod()} {$request->getRequestTarget()}"
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

/** @var Route */
foreach ($routes as $route) {
    if ($route->getMethod() !== $request->getMethod()) {
        continue;
    }

    $params = matchRoute($route->getPath(), $uri->getPath());

    if ($params === false) {
        continue;
    }

    $matched = true;

    try {
        ob_start();
        call_user_func($route->getCallable(), $params);
        $response->getBody()->write(ob_get_clean() ?: '');

        echo $response->getBody()->getContents();
    } catch (Throwable $exception) {
        $response = $response->withStatus(500);
        $message = "Error: {$exception->getMessage()}";

        if ($acceptJson) {
            $response = $response->withHeader('Content-Type', 'application/json');

            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => $message,
            ]) ?: '');
        } else {
            $response->getBody()->write($message);
        }
    }

    http_response_code($response->getStatusCode());
    echo $response->getBody()->getContents();

    break;
}

// Si no se encontró ruta, mostrar 404
if (!$matched) {
    $response = $response->withStatus(404);

    ob_start();

    require __DIR__ . '/../app/Views/404.php';

    $response->getBody()->write(ob_get_clean() ?: '');
    echo $response->getBody()->getContents();
}
