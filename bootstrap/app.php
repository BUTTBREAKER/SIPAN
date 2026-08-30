<?php

declare(strict_types=1);

use Symfony\Component\Dotenv\Dotenv;

require_once __DIR__ . '/../vendor/autoload.php';

// Cargar configuración
(new Dotenv())->load(__DIR__ . '/../.env.example', __DIR__ . '/../.env');
$_ENV['app_debug'] = filter_var($_ENV['app_debug'], FILTER_VALIDATE_BOOL);

$_ENV['session_lifetime'] = filter_var(
    $_ENV['session_lifetime'],
    FILTER_VALIDATE_INT,
);
