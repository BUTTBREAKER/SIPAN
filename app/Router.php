<?php

declare(strict_types=1);

namespace App;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class Router
{
    /** @var array<string, array<string, RequestHandlerInterface>> */
    private array $routes = [];

    /**
     * @param list<string> $methods
     * @param list<string> $patterns
     */
    public function attach(array $methods, array $patterns, RequestHandlerInterface $handler): void
    {
        foreach ($methods as $method) {
            foreach ($patterns as $pattern) {
                $this->routes[$method][$pattern] = $handler;
            }
        }
    }

    public function match(ServerRequestInterface $request): Result
    {
        if (!key_exists($request->getMethod(), $this->routes)) {
            return Result::failure();
        }

        foreach ($this->routes[$request->getMethod()] as $pattern => $handler) {
            $match = preg_match($pattern, $request->getUri()->getPath(), $matches);

            if (!$match) {
                continue;
            }

            $attributes = [];

            foreach ($matches as $name => $value) {
                if (is_string($name)) {
                    $attributes[$name] = $value;
                }
            }

            return Result::success($handler, $attributes);
        }

        return Result::failure();
    }
}
