<?php

declare(strict_types=1);

use flight\Container;
use GuzzleHttp\Psr7\HttpFactory;
use GuzzleHttp\Psr7\ServerRequest;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\Dotenv\Dotenv;

require_once __DIR__ . '/../vendor/autoload.php';

// Cargar configuración
(new Dotenv())->load(__DIR__ . '/../.env.example', __DIR__ . '/../.env');
$_ENV['app_debug'] = filter_var($_ENV['app_debug'], FILTER_VALIDATE_BOOL);

$_ENV['session_lifetime'] = filter_var(
    $_ENV['session_lifetime'],
    FILTER_VALIDATE_INT,
);

// Configurar errores según entorno
error_reporting(E_ALL);

$isProduction = getenv('app_env') === 'production';
$docrefRoot = $isProduction ? null : 'https://www.php.net/manual/es/';
$docrefExt = $isProduction ? null : '.php';

ini_set('display_errors', $isProduction ? 'Off' : 'On');
ini_set('display_startup_errors', $isProduction ? 'Off' : 'On');
ini_set('log_errors', 'On');
ini_set('log_errors_max_len', '0');
ini_set('ignore_repeated_errors', 'Off');
ini_set('ignore_repeated_source', 'Off');
ini_set('report_memleaks', 'On');
ini_set('html_errors', 'On');
ini_set('docref_root', $docrefRoot);
ini_set('docref_ext', $docrefExt);
ini_set('error_prepend_string', '<pre style="color: red">');
ini_set('error_append_string', '</pre>');
ini_set('error_log', __DIR__ . '/../storage/logs/php_errors.log');

Container::getInstance()->singleton(
    ServerRequestInterface::class,
    [ServerRequest::class, 'fromGlobals'],
);

Container::getInstance()->singleton(
    ResponseFactoryInterface::class,
    HttpFactory::class,
);
