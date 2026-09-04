<?php

declare(strict_types=1);

namespace App;

use OutOfBoundsException;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Dotenv\Dotenv;

function getenv(string $name): int|float|bool|string
{
    if (!key_exists($name, $_ENV)) {
        $dotenv = new Dotenv();
        $dotenv->load(__DIR__ . '/../.env.example', __DIR__ . '/../.env');
    }

    $message = "La variable de entorno '$name' no está definida.";

    $env = $_ENV[$name] ?? throw new OutOfBoundsException($message);

    if ($filteredVar = filter_var($env, FILTER_VALIDATE_BOOL)) {
        return $filteredVar;
    }

    if ($filteredVar = filter_var($env, FILTER_VALIDATE_INT)) {
        return $filteredVar;
    }

    if ($filteredVar = filter_var($env, FILTER_VALIDATE_FLOAT)) {
        return $filteredVar;
    }

    assert(is_string($env), 'La variable de entorno debe ser una cadena de texto.');

    return $env;
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
