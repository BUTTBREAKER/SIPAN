<?php

namespace SIPAN\Middlewares;

use SIPAN\App;

final readonly class EnsureUserIsLoggedMiddleware
{
  static function before()
  {
    if (auth()->user() === null) {
      App::redirect(App::getUrl('login.get'));
    } else {
      return true;
    }
  }
}
