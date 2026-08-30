<?php

declare(strict_types=1);

use App\ConstantsMiddleware;
use App\LogRequestMiddleware;
use App\NotFoundHandler;
use App\QueueRequestHandler;
use App\Router;
use App\RoutingMiddleware;
use App\SessionMiddleware;
use GuzzleHttp\Psr7\HttpFactory;
use GuzzleHttp\Psr7\ServerRequest;
use Leaf\Http\Session;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\ErrorLogHandler;
use Monolog\Logger;
use Monolog\Processor\PsrLogMessageProcessor;

require_once __DIR__ . '/../bootstrap/app.php';

$responseFactory = new HttpFactory();

$router = new Router(
    $responseFactory,
    ...require __DIR__ . '/../routes/delivery.php',
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


$request = ServerRequest::fromGlobals();
$path = $request->getUri()->getPath();
$path = str_replace('/delivery', '', $path);
$path = rtrim($path, '/') ?: '/';

$response = $queueRequestHandler
    ->handle($request->withUri($request->getUri()->withPath($path)));

http_response_code($response->getStatusCode());

foreach ($response->getHeaders() as $name => $values) {
    foreach ($values as $value) {
        header("$name: $value", false);
    }
}

echo $response->getBody();
