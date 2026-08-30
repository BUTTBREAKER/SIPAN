<?php

declare(strict_types=1);

use App\ConstantsMiddleware;
use App\LogRequestMiddleware;
use App\NotFoundHandler;
use App\QueueRequestHandler;
use App\Router;
use App\RoutingMiddleware;
use flight\Container;
use GuzzleHttp\Psr7\HttpFactory;
use GuzzleHttp\Psr7\ServerRequest;
use Leaf\Http\Session;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\ErrorLogHandler;
use Monolog\Logger;
use Monolog\Processor\PsrLogMessageProcessor;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;

use function App\getenv;

require_once __DIR__ . '/../bootstrap/app.php';

// SIPAN - Sistema Integral para Panaderías
// Archivo principal de enrutamiento
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
$responseFactory = new HttpFactory();

$router = new Router(
    $responseFactory,
    ...require __DIR__ . '/../routes/web.php',
);

$format = '[%datetime%] %level_name%: %message%';

$logger = new Logger(
    '',
    [(new ErrorLogHandler())->setFormatter(new LineFormatter($format))],
    [new PsrLogMessageProcessor()],
);

$logRequestMiddleware = new LogRequestMiddleware();
$logRequestMiddleware->setLogger($logger);

$queueRequestHandler = new QueueRequestHandler(
    new NotFoundHandler($responseFactory),
    $logRequestMiddleware,
    new ConstantsMiddleware(),
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
