<?php

declare(strict_types=1);

namespace App;

use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Dotenv\Dotenv;

function getenv(string $name): null|int|float|bool|string
{
    if (!key_exists($name, $_ENV)) {
        $dotenv = new Dotenv();
        $dotenv->load(__DIR__ . '/../.env.example', __DIR__ . '/../.env');
    }

    $env = $_ENV[$name] ?? null;

    if ($filteredVar = filter_var($env, FILTER_VALIDATE_BOOL)) {
        return $filteredVar;
    }

    if ($filteredVar = filter_var($env, FILTER_VALIDATE_INT)) {
        return $filteredVar;
    }

    if ($filteredVar = filter_var($env, FILTER_VALIDATE_FLOAT)) {
        return $filteredVar;
    }

    if (is_string($env)) {
        return $env;
    }

    return null;
}

function sendResponse(ResponseInterface $response): void
{
    http_response_code($response->getStatusCode());

    foreach ($response->getHeaders() as $name => $values) {
        foreach ($values as $value) {
            header("$name: $value", false);
        }
    }

    echo $response->getBody();
}
