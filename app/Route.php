<?php

declare(strict_types=1);

namespace App;

use Closure;
use flight\Container;
use InvalidArgumentException;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Throwable;

final class Route
{
    private Closure $callable;

    /**
     * @param array{class-string<object>, string}|callable ...$callables
     * @throws Throwable
     */
    public function __construct(
        private string $method,
        private string $pattern,
        array|callable ...$callables,
    ) {
        $this->pattern = str_replace('/', '\\/', $this->pattern);

        $this->pattern = preg_replace(
            '/\{([a-zA-Z0-9_]+)\}/',
            '(?<$1>.+)',
            $this->pattern,
        ) ?: throw new InvalidArgumentException("Invalid pattern: $this->pattern");

        $this->pattern = "/^{$this->pattern}$/";

        $this->callable = static function (string ...$attributes) use ($callables): void {
            foreach ($callables as $callable) {
                if (
                    is_array($callable)
                    && count($callable) === 2
                    && method_exists($callable[0], $callable[1])
                ) {
                    if (is_string($callable[0]) && class_exists($callable[0])) {
                        $object = Container::getInstance()->get($callable[0]);

                        if (
                            $object instanceof LoggerAwareInterface
                            && Container::getInstance()->has(LoggerInterface::class)
                        ) {
                            $object->setLogger(Container::getInstance()->get(LoggerInterface::class));
                        }

                        $object->{$callable[1]}(...$attributes);

                        continue;
                    }
                }

                if (is_callable($callable)) {
                    $callable(...$attributes);

                    continue;
                }
            }
        };
    }

    public function getCallable(): callable
    {
        return $this->callable;
    }

    /** @return ?array<string, string> */
    public function getParamsFromUriPath(string $path): ?array
    {
        if (preg_match($this->pattern, $path, $matches)) {
            return array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
        }

        return null;
    }

    public function hasMethod(string $method): bool
    {
        return $this->method === $method;
    }
}
