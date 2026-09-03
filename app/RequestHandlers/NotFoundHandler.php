<?php

declare(strict_types=1);

namespace App\RequestHandlers;

use NoDiscard;
use Override;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class NotFoundHandler implements RequestHandlerInterface
{
    public function __construct(
        private ResponseFactoryInterface $responseFactory,
    ) {
        //
    }

    #[Override]
    #[NoDiscard]
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $response = $this->responseFactory->createResponse(404);

        if (str_contains($request->getUri()->getPath(), '/delivery')) {
            $response->getBody()->write('404 - Not Found (Delivery App)');
        } else {
            ob_start();
            require_once __DIR__ . '/../Views/404.php';
            $response->getBody()->write(ob_get_clean());
        }

        return $response;
    }
}
