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
final class QueueRequestHandler implements RequestHandlerInterface
{
    /** @var MiddlewareInterface[] */
    private array $middlewares;

    public function __construct(
        private RequestHandlerInterface $fallbackHandler,
        MiddlewareInterface ...$middlewares,
    ) {
        $this->middlewares = $middlewares;
    }

    #[Override]
    #[NoDiscard]
    public function handle(
        ServerRequestInterface $request,
    ): ResponseInterface {
        return (
            array_shift($this->middlewares)?->process($request, $this)
            ?? $this->fallbackHandler->handle($request)
        );
    }
}
