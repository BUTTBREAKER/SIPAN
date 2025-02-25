<?php

namespace SIPAN\Middlewares;

use SIPAN\App;

final readonly class EnsureUserIsLoggedMiddleware
{
  static function before(): void
  {
    if (!key_exists('loggedUserId', $_SESSION)) {
      App::redirect('/');
    }
  }
}
