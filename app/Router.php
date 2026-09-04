<?php

declare(strict_types=1);

namespace App;

use NoDiscard;
use Psr\Http\Message\ResponseInterface;
use Override;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use RuntimeException;
use Throwable;

final class Router
{
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

            $handler = new class(
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

            return Result::success($handler);
        }

        return Result::failure();
    }
}
