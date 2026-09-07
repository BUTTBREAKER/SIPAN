<?php

declare(strict_types=1);

namespace App;

use Psr\Http\Server\RequestHandlerInterface;

final class Result
{
    /** @param array<string, string> $attributes */
    private function __construct(
        private ?RequestHandlerInterface $handler = null,
        private array $attributes = [],
    ) {
        //
    }

    /** @param array<string, string> $attributes */
    public static function success(RequestHandlerInterface $handler, array $attributes = []): self
    {
        return new self($handler, $attributes);
    }

    public static function failure(): self
    {
        return new self();
    }

    /** @phpstan-assert-if-true RequestHandlerInterface $this->handler */
    public function isSuccess(): bool
    {
        return $this->handler instanceof RequestHandlerInterface;
    }

    public function getHandler(): RequestHandlerInterface
    {
        assert($this->isSuccess(), 'Result is not successful, cannot get handler.');

        return $this->handler;
    }

    /** @return array<string, string> */
    public function getAttributes(): array
    {
        return $this->attributes;
    }
}
