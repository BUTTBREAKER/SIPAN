<?php

declare(strict_types=1);

namespace App;

use NoDiscard;
use Override;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;

/** @readonly */
final class NotFoundHandler implements RequestHandlerInterface
{
    public function __construct(
        private ResponseFactoryInterface $responseFactory,
    ) {
        //
    }

    #[Override]
    #[NoDiscard]
    public function handle(
        ServerRequestInterface $request,
    ): ResponseInterface {
        $response = $this->responseFactory->createResponse(404);
        ob_start();
        $response->getBody()->write(ob_get_clean());

        return $response;
    }
}
