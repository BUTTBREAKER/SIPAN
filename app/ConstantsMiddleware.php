<?php

declare(strict_types=1);

namespace App;

use NoDiscard;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Override;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class ConstantsMiddleware implements MiddlewareInterface
{
    #[Override]
    #[NoDiscard]
    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler,
    ): ResponseInterface {
        if (!defined('BASE_URL')) {
            define(
                'BASE_URL',
                $request->getUri()->withQuery('')->withPath(''),
            );
        }

        return $handler->handle($request);
    }
}
