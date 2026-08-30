<?php

declare(strict_types=1);

use App\QueueRequestHandler;
use App\Router;
use App\RoutingMiddleware;
use App\SessionMiddleware;
use GuzzleHttp\Psr7\HttpFactory;
use GuzzleHttp\Psr7\ServerRequest;
use Leaf\Http\Session;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;

require_once __DIR__ . '/../bootstrap/app.php';

$responseFactory = new HttpFactory();

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
    new SessionMiddleware(new Session()),
    new RoutingMiddleware($router),
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
