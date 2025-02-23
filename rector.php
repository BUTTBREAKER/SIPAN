<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;

return RectorConfig::configure()
  ->withPaths([
    __DIR__ . '/app',
    __DIR__ . '/tests',
    __DIR__ . '/views',
    __DIR__ . '/index.php'
  ])
  ->withPhpSets(php82: true);
