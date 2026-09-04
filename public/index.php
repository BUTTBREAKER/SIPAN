<?php

declare(strict_types=1);

use App\Middlewares\RoutingMiddleware;
use App\Middlewares\SessionMiddleware;
use App\RequestHandlers\NotFoundHandler;
use App\RequestHandlers\QueueRequestHandler;
use App\Router;
use flight\Container;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;

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

$queueRequestHandler = new QueueRequestHandler(
    $container->get(NotFoundHandler::class),
    $container->get(SessionMiddleware::class),
    new RoutingMiddleware($router),
);

sendResponse($queueRequestHandler->handle($request));
