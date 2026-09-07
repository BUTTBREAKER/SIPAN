<?php

declare(strict_types=1);

namespace Tests\Feature;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\HttpFactory;
use GuzzleHttp\RequestOptions;
use Override;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

abstract class FeatureTestCase extends TestCase
{
    protected static ClientInterface $client;
    protected static RequestFactoryInterface $requestFactory;
    protected static StreamFactoryInterface $streamFactory;

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        self::$client ??= new Client([
            'base_uri' => 'http://sipan.local',
            RequestOptions::ALLOW_REDIRECTS => false,
        ]);

        self::$requestFactory = new HttpFactory();
        self::$streamFactory = new HttpFactory();
    }
}
