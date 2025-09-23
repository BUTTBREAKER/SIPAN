<?php

namespace SIPAN\Middlewares;

use SIPAN\App;

final readonly class EnsureUserIsNotLoggedMiddleware
{
  static function before(): void
  {
    if (auth()->id() !== null) {
      App::redirect('/administracion');
    }
  }
}
