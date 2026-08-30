<?php

declare(strict_types=1);

namespace App;

use NoDiscard;
use Psr\Http\Message\ResponseInterface;
use Override;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Throwable;

/** @readonly */
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
        foreach ($this->routes as $route) {
            if (!$route->matchRequestMethod($request)) {
                continue;
            }

            $params = $route->getParamsFromUri($request->getUri());

            if ($params === false) {
                continue;
            }

            $handler = new class(
                $this->responseFactory,
                $route,
                ...$params,
            ) implements RequestHandlerInterface {
                /** @param string[] $params */
                private array $params;

                public function __construct(
                    private ResponseFactoryInterface $responseFactory,
                    private Route $route,
                    string ...$params,
                ) {
                    $this->params = $params;
                }

                #[Override]
                #[NoDiscard]
                public function handle(
                    ServerRequestInterface $request,
                ): ResponseInterface {
                    $callable = $this->route->getCallable();

                    $acceptJson = in_array(
                        'application/json',
                        $request->getHeader('accept'),
                    );

                    try {
                        ob_start();
                        $callable($this->params);
                        $response = $this->responseFactory->createResponse();
                        $response->getBody()->write(ob_get_clean());
                    } catch (Throwable $throwable) {
                        $response = $this
                            ->responseFactory
                            ->createResponse(500);

                        $message = "Error: {$throwable->getMessage()}";

                        if ($acceptJson) {
                            $response = $response->withHeader(
                                'content-type',
                                'application/json',
                            );

                            $response->getBody()->write(json_encode([
                                'success' => false,
                                'message' => $message,
                            ]));
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
