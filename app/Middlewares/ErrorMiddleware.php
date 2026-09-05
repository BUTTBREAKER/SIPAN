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
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Throwable;

use function App\getenv;

final class ErrorMiddleware implements MiddlewareInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

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
            $this->logger?->debug('', ['exception' => $throwable]);
            $response = $this->responseFactory->createResponse(500);

            if (getenv('app_env') === 'production') {
                $response->getBody()->write($response->getReasonPhrase());
            } else {
                $response->getBody()->write("<pre>$throwable</pre>");
            }

            return $response;
        }
    }
}
