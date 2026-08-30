<?php

declare(strict_types=1);

namespace App;

use Psr\Http\Server\RequestHandlerInterface;

/** @readonly */
final class Result
{
    private function __construct(
        private ?RequestHandlerInterface $handler = null,
    ) {
        //
    }

    public static function success(RequestHandlerInterface $handler): self
    {
        return new self($handler);
    }

    public static function failure(): self
    {
        return new self();
    }

    public function isSuccess(): bool
    {
        return $this->handler !== null;
    }

    public function getHandler(): ?RequestHandlerInterface
    {
        return $this->handler;
    }
}
