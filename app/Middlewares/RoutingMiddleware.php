<?php

declare(strict_types=1);

namespace App\Middlewares;

use App\Router;
use NoDiscard;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Override;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class RoutingMiddleware implements MiddlewareInterface
{
    public function __construct(private Router $router)
    {
        //
    }

    #[Override]
    #[NoDiscard]
    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler,
    ): ResponseInterface {
        return (
            $this->router->match($request)->getHandler()?->handle($request)
            ?? $handler->handle($request)
        );
    }
}
