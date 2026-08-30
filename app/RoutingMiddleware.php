<?php

declare(strict_types=1);

namespace App;

use NoDiscard;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Override;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/** @readonly */
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
        $result = $this->router->match($request);

        if ($result->isSuccess()) {
            return $result->getHandler()->handle($request);
        }

        return $handler->handle($request);
    }
}
