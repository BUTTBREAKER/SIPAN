<?php

declare(strict_types=1);

use App\NotFoundHandler;
use App\QueueRequestHandler;
use App\Router;
use App\RoutingMiddleware;
use flight\Container;
use GuzzleHttp\Psr7\HttpFactory;
use GuzzleHttp\Psr7\ServerRequest;
use Leaf\Http\Session;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\Dotenv\Dotenv;

use function App\getenv;

require_once __DIR__ . '/../vendor/autoload.php';

// SIPAN - Sistema Integral para Panaderías
// Archivo principal de enrutamiento

// Cargar configuración
(new Dotenv())->load(__DIR__ . '/../.env.example', __DIR__ . '/../.env');
$_ENV['app_debug'] = filter_var($_ENV['app_debug'], FILTER_VALIDATE_BOOL);

$_ENV['session_lifetime'] = filter_var(
    $_ENV['session_lifetime'],
    FILTER_VALIDATE_INT,
);

// Configurar errores según entorno
error_reporting(E_ALL);

$isProduction = getenv('app_env') === 'production';
$docrefRoot = $isProduction ? null : 'https://www.php.net/manual/es/';
$docrefExt = $isProduction ? null : '.php';

ini_set('display_errors', $isProduction ? 'Off' : 'On');
ini_set('display_startup_errors', $isProduction ? 'Off' : 'On');
ini_set('log_errors', 'On');
ini_set('log_errors_max_len', 0);
ini_set('ignore_repeated_errors', 'Off');
ini_set('ignore_repeated_source', 'Off');
ini_set('report_memleaks', 'On');
ini_set('html_errors', 'On');
ini_set('docref_root', $docrefRoot);
ini_set('docref_ext', $docrefExt);
ini_set('error_prepend_string', '<pre style="color: red">');
ini_set('error_append_string', '</pre>');
ini_set('error_log', __DIR__ . '/../storage/logs/php_errors.log');

Container::getInstance()->singleton(
    ServerRequestInterface::class,
    [ServerRequest::class, 'fromGlobals'],
);

Container::getInstance()->singleton(
    ResponseFactoryInterface::class,
    HttpFactory::class,
);

$request = Container::getInstance()->get(ServerRequestInterface::class);

// Detectar si estamos detrás de un proxy/túnel con HTTPS
$isSecure = (
    $request->getUri()->getScheme() === 'https'
    || strtolower($request->getHeaderLine('X_FORWARDED_PROTO')) === 'https'
);

// Detectar protocolo (compatible con proxy/túnel como Cloudflare)
$scheme = $isSecure ? 'https' : 'http';
$request = $request->withUri($request->getUri()->withScheme($scheme));

$response = Container::getInstance()
    ->get(ResponseFactoryInterface::class)
    ->createResponse();

// Detectar si la ruta es para el sistema de delivery
$isDeliveryPath = str_contains($request->getUri()->getPath(), '/delivery');

if (session_status() === PHP_SESSION_NONE) {
    // Configurar parámetros de la cookie de sesión ANTES de iniciar la sesión
    $sessionParams = [
        'lifetime' => (int) getenv('session_lifetime'),
        'secure' => $isSecure,
        'httponly' => true,
        'samesite' => 'Strict',
    ] + session_get_cookie_params();

    session_set_cookie_params($sessionParams);

    // Nombre de sesión dinámico para permitir múltiples sesiones independientes en la misma red/dominio
    $baseSessionName = getenv('session_name');

    $finalSessionName = $isDeliveryPath
        ? "{$baseSessionName}_DELIVERY"
        : $baseSessionName;

    session_name($finalSessionName);
    Session::start();
}

// La ruta ya fue detectada arriba
// ------ INTEGRACIÓN APP DELIVERY (Pivote de enrutamiento) ------
// Si la ruta empieza con /delivery y no es un archivo físico (ya manejado por el servidor)
if ($isDeliveryPath) {
    require_once __DIR__ . '/../delivery/index.php';

    return;
}

// ---------------------------------------------------------------
define('BASE_URL', $request->getUri()->withQuery('')->withPath(''));

// Debug (comentar en producción)
error_log(
    getenv('app_debug') === 'true'
        ? "Path: {$request->getUri()->getPath()}, Method: {$request->getMethod()}, URI: {$request->getRequestTarget()}"
        : "{$request->getMethod()} {$request->getRequestTarget()}"
);

$responseFactory = new HttpFactory();

$router = new Router(
    $responseFactory,
    ...require __DIR__ . '/../routes/web.php',
);

$queueRequestHandler = new QueueRequestHandler(
    new NotFoundHandler($responseFactory),
    new RoutingMiddleware($router),
);

$response = $queueRequestHandler->handle(ServerRequest::fromGlobals());
http_response_code($response->getStatusCode());

foreach ($response->getHeaders() as $name => $values) {
    foreach ($values as $value) {
        header("$name: $value", false);
    }
}

echo $response->getBody();
