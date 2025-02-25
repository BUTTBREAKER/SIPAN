<?php

namespace Tests\E2E;

use DOMDocument;
use PHPUnit\Framework\Attributes\Test;

final class LandingTest extends TestCase
{
  #[Test]
  function it_renders_landing(): void
  {
    $response = $this->client->get("{$this->endpoint}/");
    $html = new DOMDocument;
    $html->loadHTML($response->getBody()->getContents());

    self::assertSame(200, $response->getStatusCode());
    self::assertSame('SIPAN', $html->getElementsByTagName('title')->item(0)->textContent);
  }
}
