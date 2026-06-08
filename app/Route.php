<?php

declare(strict_types=1);

namespace App;

use Closure;
use Exception;
use flight\Container;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;

final class Route
{
    private string $method;
    private string $path;
    private Closure $callable;

    /**
     * @param array{0: class-string, 1: string}|callable ...$callables
     * @throws Exception
     */
    function __construct(
        string $method,
        string $path,
        ...$callables
    ) {
        foreach ($callables as &$callable) {
            if (is_array($callable)) {
                if (!class_exists($callable[0])) {
                    throw new Exception("Controlador no encontrado: {$callable[0]}");
                }

                if (!Container::getInstance()->has($callable[0])) {
                    Container::getInstance()->singleton($callable[0]);
                }

                $callable[0] = Container::getInstance()->get($callable[0]);

                if (!method_exists($callable[0], $callable[1])) {
                    throw new Exception("Método no encontrado: {$callable[1]}");
                }
            }
        }

        $this->method = $method;
        $this->path = $path;

        $this->callable = static function (array $parameters) use ($callables): void {
            foreach ($callables as $callable) {
                call_user_func_array($callable, $parameters);
            }
        };
    }

    function getCallable(): callable
    {
        return $this->callable;
    }

    function getParamsFromUri(UriInterface $uri)
    {
        $pattern = $this->path;

        $pattern = preg_replace('/\{[a-zA-Z0-9_]+\}/', '([a-zA-Z0-9_-]+)', $pattern);
        $pattern = "#^$pattern$#";

        if (preg_match($pattern, $uri->getPath(), $matches)) {
            array_shift($matches);

            return $matches;
        }

        return false;
    }

    function matchRequestMethod(RequestInterface $request): bool
    {
        return $this->method === $request->getMethod();
    }
}
