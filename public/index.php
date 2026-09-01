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
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;

use function App\sendResponse;

require_once __DIR__ . '/../bootstrap/app.php';

$container = Container::getInstance();
$request = $container->get(ServerRequestInterface::class);

if (str_starts_with($request->getUri()->getPath(), '/delivery')) {
    require_once __DIR__ . '/../delivery/index.php';

    return;
}

$responseFactory = $container->get(ResponseFactoryInterface::class);

$router = new Router(
    $responseFactory,
    ...require __DIR__ . '/../routes/web.php',
);

$logger = $container->get(LoggerInterface::class);
$logRequestMiddleware = $container->get(LogRequestMiddleware::class);
$logRequestMiddleware->setLogger($logger);

$queueRequestHandler = new QueueRequestHandler(
    $container->get(NotFoundHandler::class),
    $container->get(SessionMiddleware::class),
    $container->get(ConstantsMiddleware::class),
    $logRequestMiddleware,
    new RoutingMiddleware($router),
);

sendResponse($queueRequestHandler->handle($request));
