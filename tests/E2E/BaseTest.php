<?php

namespace Tests\E2E;

use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;

abstract class BaseTest extends TestCase
{
  protected readonly Client $client;
  protected readonly string $endpoint;

  function __construct(string $name)
  {
    parent::__construct($name);

    $env = require __DIR__ . '/../../.env.php';
    $this->endpoint = $env['TEST_ENDPOINT'];
    $this->client = new Client;
  }
}
