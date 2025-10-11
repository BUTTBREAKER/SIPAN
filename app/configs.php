<?php

use flight\Container;
use League\OAuth2\Client\Provider\Facebook;
use League\OAuth2\Client\Provider\Github;
use SIPAN\App;
use Smolblog\OAuth2\Client\Provider\Twitter;

$envFilePath = __DIR__ . '/../.env.php';

$_ENV = array_merge(
  require __DIR__ . '/../.env.example.php',
  file_exists($envFilePath)
    ? require $envFilePath
    : [],
);

date_default_timezone_set($_ENV['TIMEZONE']);

$container = Container::getInstance();

$container->singleton(PDO::class, static fn(): PDO => new PDO(
  $_ENV['PDO']['DSN'],
  $_ENV['PDO']['USER'],
  $_ENV['PDO']['PASSWORD'],
));

db()->connection($container->get(PDO::class));
(new ReflectionProperty(auth(), 'db'))->setValue(auth(), db());

$github = new Github([
  'clientId' => $_ENV['GITHUB_AUTH_CLIENT_ID'],
  'clientSecret' => $_ENV['GITHUB_AUTH_CLIENT_SECRET'],
  'redirectUri' => $_ENV['GITHUB_AUTH_REDIRECT_URI'],
]);

$facebook = new Facebook([
  'clientId' => $_ENV['FACEBOOK_AUTH_CLIENT_ID'],
  'clientSecret' => $_ENV['FACEBOOK_AUTH_CLIENT_SECRET'],
  'redirectUri' => $_ENV['FACEBOOK_AUTH_REDIRECT_URI'],
  'graphApiVersion' => 'v2.10',
]);

$twitter = new Twitter([
  'clientId' => $_ENV['TWITTER_AUTH_CLIENT_ID'],
  'clientSecret' => $_ENV['TWITTER_AUTH_CLIENT_SECRET'],
  'redirectUri' => $_ENV['TWITTER_AUTH_REDIRECT_URI'],
]);

$noExpirationLifetime = 0;

auth()->config('db.table', 'usuarios');
auth()->config('session', true);
auth()->config('password.key', 'clave');
auth()->config('session.lifetime', $noExpirationLifetime);
auth()->config('messages.loginParamsError', '¡Correo o contraseña incorrecta!');
auth()->config('messages.loginPasswordError', auth()->config('messages.loginParamsError'));
auth()->withProvider('github', $github);
auth()->withProvider('facebook', $facebook);
auth()->withProvider('twitter', $twitter);

App::registerContainerHandler($container->get(...));

form()->rule('password', '/^.{8,}$/', 'La clave debe tener al menos 8 caracteres.');
form()->rule('text', '/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/');
form()->rule('boolean', '/^(true|false|1|0|on|off)$/');
