<?php

declare(strict_types=1);

use App\ConstantsMiddleware;
use App\LogRequestMiddleware;
use App\NotFoundHandler;
use App\QueueRequestHandler;
use App\Router;
use App\RoutingMiddleware;
use App\SessionMiddleware;
use flight\Container;
use GuzzleHttp\Psr7\HttpFactory;
use GuzzleHttp\Psr7\ServerRequest;
use Leaf\Http\Session;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\ErrorLogHandler;
use Monolog\Logger;
use Monolog\Processor\PsrLogMessageProcessor;
use Psr\Http\Message\ServerRequestInterface;

require_once __DIR__ . '/../bootstrap/app.php';

// SIPAN - Sistema Integral para Panaderías
// Archivo principal de enrutamiento
$request = Container::getInstance()->get(ServerRequestInterface::class);

// Detectar si la ruta es para el sistema de delivery
$isDeliveryPath = str_contains($request->getUri()->getPath(), '/delivery');

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
    new SessionMiddleware(new Session()),
    new ConstantsMiddleware(),
    new RoutingMiddleware($router),
    $logRequestMiddleware,
);

$response = $queueRequestHandler->handle(ServerRequest::fromGlobals());
http_response_code($response->getStatusCode());

foreach ($response->getHeaders() as $name => $values) {
    foreach ($values as $value) {
        header("$name: $value", false);
    }
}

echo $response->getBody();
