<?php

namespace SIPAN\Middlewares;

use SIPAN\App;

final readonly class EnsureUserIsNotLoggedMiddleware
{
  static function before(): void
  {
    if (key_exists('loggedUserId', $_SESSION)) {
      App::redirect('/app');
    }
  }
}
