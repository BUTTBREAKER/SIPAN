<?php

namespace SIPAN\Middlewares;

use SIPAN\App;

final readonly class EnsureUserIsLoggedMiddleware
{
  static function before(): void
  {
    if (auth()->id() === null) {
      App::redirect('/ingresar');
    }
  }
}
