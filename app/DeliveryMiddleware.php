<?php

declare(strict_types=1);

namespace App;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Override;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/** @readonly */
final class DeliveryMiddleware implements MiddlewareInterface
{
    public function __construct(
        private ResponseFactoryInterface $responseFactory,
    ) {
        //
    }

    #[Override]
    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler,
    ): ResponseInterface {
        $isDeliveryPath = str_contains(
            $request->getUri()->getPath(),
            '/delivery',
        );

        if (!$isDeliveryPath) {
            return $handler->handle($request);
        }

        ob_start();
        require_once __DIR__ . '/../delivery/index.php';

        $response = $this
            ->responseFactory
            ->createResponse(http_response_code());

        $response->getBody()->write(ob_get_clean());

        return $response;
    }
}
