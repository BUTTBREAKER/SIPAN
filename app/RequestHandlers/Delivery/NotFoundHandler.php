<?php

declare(strict_types=1);

namespace App\RequestHandlers\Delivery;

use NoDiscard;
use Override;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class NotFoundHandler implements RequestHandlerInterface
{
    public function __construct(private ResponseFactoryInterface $responseFactory)
    {
        //
    }

    #[Override]
    #[NoDiscard]
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $response = $this->responseFactory->createResponse(404);
        $response->getBody()->write('404 - Not Found (Delivery App)');

        return $response;
    }
}
