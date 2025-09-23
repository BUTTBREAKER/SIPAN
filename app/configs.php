<?php

use flight\Container;
use SIPAN\App;

$envFilePath = __DIR__ . '/../.env.php';

$_ENV = array_merge(
  require __DIR__ . '/../.env.example.php',
  file_exists($envFilePath)
    ? require $envFilePath
    : [],
);

session_start();
date_default_timezone_set($_ENV['TIMEZONE']);

$container = Container::getInstance();

$container->singleton(PDO::class, static fn(): PDO => new PDO(
  $_ENV['PDO']['DSN'],
  $_ENV['PDO']['USER'],
  $_ENV['PDO']['PASSWORD'],
));

@db()->connection($container->get(PDO::class));
auth()->dbConnection($container->get(PDO::class));
(new ReflectionProperty(auth(), 'db'))->setValue(auth(), db());

auth()->config('id.key', 'id');
auth()->config('db.table', 'usuarios');
auth()->config('password.key', 'clave');
auth()->config('session', true);
// auth()->config('session.lifetime', '1 hour'); // 1 hour
// auth()->config('session.lifetime', 60 * 60 * 24 * 7); // 1 week
auth()->config('session.lifetime', 0); // never expire

$loginParamsError = '¡Correo o contraseña incorrecta!';
auth()->config('messages.loginParamsError', $loginParamsError);
auth()->config('messages.loginPasswordError', $loginParamsError);
auth()->config('hidden', ['clave', 'id', 'correo']);
auth()->config('unique', ['correo']);
auth()->config('timestamps', true);
auth()->config('timestamps.format', 'YYYY-MM-DD HH:MM:SS');


auth()->config('session.cookie', [
  'secure' => true,
  'httponly' => true,
  'samesite' => 'lax'
]);

App::registerContainerHandler($container->get(...));
