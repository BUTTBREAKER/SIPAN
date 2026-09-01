<?php

declare(strict_types=1);

namespace App;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Override;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

final class LogRequestMiddleware implements
    MiddlewareInterface,
    LoggerAwareInterface
{
    use LoggerAwareTrait;

    #[Override]
    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler,
    ): ResponseInterface {
        $message = getenv('app_debug')
            ? "Path: {path}, Method: {method}, URI: {uri}"
            : "{method} {uri}";

        $this->logger?->debug($message, [
            'path' => $request->getUri()->getPath(),
            'method' => $request->getMethod(),
            'uri' => $request->getRequestTarget(),
        ]);

        return $handler->handle($request);
    }
}
