<?php

declare(strict_types=1);

namespace Tests\Unit;

use flight\Container;
use GuzzleHttp\Psr7\HttpFactory;
use Override;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

abstract class UnitTestCase extends TestCase
{
    protected static ContainerInterface $container;

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();
        $container = Container::getInstance();
        $container->singleton(ResponseFactoryInterface::class, HttpFactory::class);
        $container->singleton(StreamFactoryInterface::class, HttpFactory::class);
        self::$container = $container;
    }
}
