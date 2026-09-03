<?php

declare(strict_types=1);

namespace App\Middlewares;

use Leaf\Http\Session;
use NoDiscard;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Override;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function App\getenv;

final class SessionMiddleware implements MiddlewareInterface
{
    public function __construct(private Session $session)
    {
        //
    }

    #[Override]
    #[NoDiscard]
    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler,
    ): ResponseInterface {
        // Detectar si estamos detrás de un proxy/túnel con HTTPS
        $isSecure = (
            $request->getUri()->getScheme() === 'https'
            || $request->getHeaderLine('X_FORWARDED_PROTO') === 'https'
        );

        // Detectar si la ruta es para el sistema de delivery
        $isDeliveryPath = str_contains($request->getUri()->getPath(), '/delivery');

        if (session_status() === PHP_SESSION_NONE) {
            // Configurar parámetros de la cookie de sesión ANTES de iniciar la sesión
            $sessionParams = [
                'lifetime' => (int) getenv('session_lifetime'),
                'secure' => $isSecure,
                'httponly' => true,
                'samesite' => 'Strict',
            ] + session_get_cookie_params();

            session_set_cookie_params($sessionParams);

            // Nombre de sesión dinámico para permitir múltiples sesiones independientes en la misma red/dominio
            $baseSessionName = getenv('session_name');

            $finalSessionName = $isDeliveryPath
                ? "{$baseSessionName}_DELIVERY"
                : $baseSessionName;

            session_name($finalSessionName);
            $this->session::start();
        }

        return $handler->handle($request);
    }
}
