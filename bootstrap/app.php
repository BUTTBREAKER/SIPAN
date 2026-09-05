<?php

declare(strict_types=1);

use flight\Container;
use GuzzleHttp\Psr7\HttpFactory;
use GuzzleHttp\Psr7\ServerRequest;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\ErrorLogHandler;
use Monolog\Logger;
use Monolog\Processor\PsrLogMessageProcessor;
use Monolog\Processor\WebProcessor;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;

require_once __DIR__ . '/../vendor/autoload.php';

// Configurar errores según entorno
error_reporting(E_ALL);

$isProduction = getenv('app_env') === 'production';
$docrefRoot = $isProduction ? '' : 'https://www.php.net/manual/es/';
$docrefExt = $isProduction ? '' : '.php';

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
ini_set('error_prepend_string', '<pre>');
ini_set('error_append_string', '</pre>');
ini_set('error_log', __DIR__ . '/../storage/logs/php_errors.log');
ini_set('assert.active', 'On');
ini_set('assert.exception', 'On');
ini_set('assert.warning', 'On');
ini_set('assert.bail', 'Off');
ini_set('assert.callback', '0');

date_default_timezone_set('america/caracas');

$container = Container::getInstance();

$container->singleton(ContainerInterface::class, $container);

$container->singleton(
    ServerRequestInterface::class,
    [ServerRequest::class, 'fromGlobals'],
);

$container->singleton(ResponseFactoryInterface::class, HttpFactory::class);

$container->singleton(
    LoggerInterface::class,
    static function (): LoggerInterface {
        $formatter = new LineFormatter("%level_name%: %message%\n\t%context%\n\t%extra%");

        return new Logger(
            '',
            [(new ErrorLogHandler())->setFormatter($formatter)],
            // @phpstan-ignore argument.type
            [
                new WebProcessor(),
                new PsrLogMessageProcessor()
            ],
        );
    },
);
