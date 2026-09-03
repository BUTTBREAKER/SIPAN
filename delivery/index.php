<?php

declare(strict_types=1);

use App\RequestHandlers\Delivery\NotFoundHandler;
use App\RequestHandlers\QueueRequestHandler;
use App\Router;
use App\RoutingMiddleware;
use App\SessionMiddleware;
use flight\Container;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;

use function App\sendResponse;

require_once __DIR__ . '/../bootstrap/app.php';

$container = Container::getInstance();
$request = $container->get(ServerRequestInterface::class);
$responseFactory = $container->get(ResponseFactoryInterface::class);

$router = new Router(
    $responseFactory,
    ...require __DIR__ . '/../routes/delivery.php',
);

$queueRequestHandler = new QueueRequestHandler(
    $container->get(NotFoundHandler::class),
    $container->get(SessionMiddleware::class),
    new RoutingMiddleware($router),
);

$path = $request->getUri()->getPath();
$path = str_replace('/delivery', '', $path);
$path = rtrim($path, '/') ?: '/';
$request = $request->withUri($request->getUri()->withPath($path));
sendResponse($queueRequestHandler->handle($request));
