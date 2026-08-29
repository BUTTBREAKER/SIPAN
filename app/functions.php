<?php

declare(strict_types=1);

namespace App;

use Symfony\Component\Dotenv\Dotenv;

function getenv(string $name): ?string
{
    if (!key_exists($name, $_ENV)) {
        $dotenv = new Dotenv();
        $dotenv->load(__DIR__ . '/../.env.example', __DIR__ . '/../.env');
    }

    return $_ENV[$name] ?? null;
}
