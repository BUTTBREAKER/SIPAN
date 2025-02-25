<?php

use Illuminate\Container\Container;
use SIPAN\App;

$envFilePath = __DIR__ . '/../.env.php';

$_ENV = array_merge(
  require __DIR__ . '/../.env.example.php',
  file_exists($envFilePath)
    ? require $envFilePath
    : []
);

session_start();
date_default_timezone_set($_ENV['TIMEZONE']);

$container = new Container;

$container->singleton(PDO::class, static fn(): PDO => new PDO(
  $_ENV['PDO']['DSN'],
  $_ENV['PDO']['USER'],
  $_ENV['PDO']['PASSWORD'],
));

App::registerContainerHandler($container->get(...));
