<?php

use Illuminate\Container\Container;
use SIPAN\App;

$_ENV += require '.env.php';

session_start();
date_default_timezone_set($_ENV['TIMEZONE']);

$container = new Container;

$container->singleton(PDO::class, static fn(): PDO => new PDO(
  $_ENV['PDO']['DSN'],
  $_ENV['PDO']['USER'],
  $_ENV['PDO']['PASSWORD'],
));

App::registerContainerHandler([$container, 'get']);
