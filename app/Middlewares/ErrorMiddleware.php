<?php

declare(strict_types=1);

namespace App\Middlewares;

use NoDiscard;
use Override;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Throwable;

final class ErrorMiddleware implements MiddlewareInterface
{
    public function __construct(private ResponseFactoryInterface $responseFactory)
    {
        //
    }

    #[Override]
    #[NoDiscard]
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            return $handler->handle($request);
        } catch (Throwable $throwable) {
            $response = $this->responseFactory->createResponse(500);
            $response->getBody()->write((string) $throwable);

            return $response;
        }
    }
}
