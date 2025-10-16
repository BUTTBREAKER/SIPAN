<?php

namespace SIPAN\Middlewares;

use SIPAN\App;

final readonly class EnsureUserIsNotLoggedMiddleware
{
  static function before()
  {
    if (auth()->user() === null) {
      return true;
    } else {
      App::redirect(App::getUrl('dashboard'));
    }
  }
}
