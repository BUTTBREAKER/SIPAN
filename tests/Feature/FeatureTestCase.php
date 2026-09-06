<?php

declare(strict_types=1);

namespace Tests\Feature;

use GuzzleHttp\Client;
use Override;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;

abstract class FeatureTestCase extends TestCase
{
    protected static ClientInterface $client;

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        self::$client ??= new Client([
            'base_uri' => 'http://sipan.local',
        ]);
    }
}
