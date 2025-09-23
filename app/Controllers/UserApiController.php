<?php

declare(strict_types=1);

namespace SIPAN\Controllers;

use SIPAN\App;

final readonly class UserApiController
{
  static function login(): void
  {
    $credentials = App::request()->data->getData();

    if (!auth()->login($credentials)) {
      $firstError = array_values(auth()->errors())[0];

      App::halt(401, $firstError);
    }

    App::json(auth()->user()->get());
  }

  static function register(): void
  {
    $data = App::request()->data->getData();

    auth()->register($data);

    App::halt(201);
  }

  static function logout(): void
  {
    auth()->logout();
  }
}
