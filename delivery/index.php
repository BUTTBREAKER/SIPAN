<?php

declare(strict_types=1);

use App\QueueRequestHandler;
use App\Router;
use App\RoutingMiddleware;
use App\SessionMiddleware;
use flight\Container;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function App\sendResponse;

require_once __DIR__ . '/../bootstrap/app.php';

$container = Container::getInstance();
$responseFactory = $container->get(ResponseFactoryInterface::class);
$request = $container->get(ServerRequestInterface::class);

$router = new Router(
    $responseFactory,
    ...require __DIR__ . '/../routes/delivery.php',
);

$notFoundHandler = new class(
    $responseFactory,
) implements RequestHandlerInterface {
    public function __construct(
        private ResponseFactoryInterface $responseFactory,
    ) {
        //
    }

    #[Override]
    #[NoDiscard]
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $response = $this->responseFactory->createResponse(404);
        $response->getBody()->write('404 - Not Found (Delivery App)');

        return $response;
    }
};

$queueRequestHandler = new QueueRequestHandler(
    $notFoundHandler,
    $container->get(SessionMiddleware::class),
    new RoutingMiddleware($router),
);

$path = $request->getUri()->getPath();
$path = str_replace('/delivery', '', $path);
$path = rtrim($path, '/') ?: '/';
$request = $request->withUri($request->getUri()->withPath($path));
sendResponse($queueRequestHandler->handle($request));
