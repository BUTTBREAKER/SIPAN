<?php

declare(strict_types=1);

use App\Middlewares\ErrorMiddleware;
use App\Middlewares\RoutingMiddleware;
use App\Middlewares\SessionMiddleware;
use App\RequestHandlers\QueueRequestHandler;
use App\Result;
use App\Route;
use App\Router;
use flight\Container;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;

use function App\sendResponse;

require_once __DIR__ . '/../bootstrap/app.php';

$container = Container::getInstance();
$request = $container->get(ServerRequestInterface::class);
$responseFactory = $container->get(ResponseFactoryInterface::class);

$notFoundHandler = new class(
    $responseFactory
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

$logger = $container->get(LoggerInterface::class);
$errorMiddleware = $container->get(ErrorMiddleware::class);
$errorMiddleware->setLogger($logger);

$queueRequestHandler = new QueueRequestHandler(
    $notFoundHandler,
    $container->get(ErrorMiddleware::class),
    $container->get(SessionMiddleware::class),
    new RoutingMiddleware(new class($responseFactory, ...require __DIR__ . '/../routes/delivery.php') extends Router {
        /** @var Route[] $routes */
        private array $routes;

        public function __construct(
            private ResponseFactoryInterface $responseFactory,
            Route ...$routes,
        ) {
            $this->routes = $routes;
        }

        public function match(ServerRequestInterface $request): Result
        {
            $responseFactory = $this->responseFactory;

            foreach ($this->routes as $route) {
                if (!$route->hasMethod($request->getMethod())) {
                    continue;
                }

                $params = $route->getParamsFromUriPath($request->getUri()->getPath());

                if ($params === null) {
                    continue;
                }

                $handler = $route->getHandler() ?? new class(
                    $responseFactory,
                    $route,
                    ...$params,
                ) implements RequestHandlerInterface {
                    /** @var array<string, string> $params */
                    private array $params;

                    public function __construct(
                        private ResponseFactoryInterface $responseFactory,
                        private Route $route,
                        string ...$params,
                    ) {
                        $this->params = array_filter($params, 'is_string', ARRAY_FILTER_USE_KEY);
                    }

                    #[Override]
                    #[NoDiscard]
                    public function handle(
                        ServerRequestInterface $request,
                    ): ResponseInterface {
                        $acceptJson = in_array(
                            'application/json',
                            $request->getHeader('accept'),
                        );

                        try {
                            $response = $this->responseFactory->createResponse();
                            $message = 'Failed to capture output for route';
                            ob_start();
                            $this->route->getCallable()(...$this->params);
                            $response
                                ->getBody()
                                ->write(ob_get_clean() ?: throw new RuntimeException($message));
                        } catch (Throwable $throwable) {
                            $response = $this->responseFactory->createResponse(500);
                            $message = "Error: {$throwable->getMessage()}";

                            if ($acceptJson) {
                                $response = $response->withHeader(
                                    'content-type',
                                    'application/json',
                                );

                                $response->getBody()->write(json_encode([
                                    'success' => false,
                                    'message' => $message,
                                ]) ?: throw new RuntimeException('Failed to encode JSON response for error'));
                            } else {
                                $response->getBody()->write($message);
                            }
                        }

                        return $response;
                    }
                };

                return Result::success($handler, $params);
            }

            return Result::failure();
        }
    }),
);

$path = $request->getUri()->getPath();
$path = str_replace('/delivery', '', $path);
$path = rtrim($path, '/') ?: '/';
$request = $request->withUri($request->getUri()->withPath($path));
sendResponse($queueRequestHandler->handle($request));
