<?php

namespace SIPAN\Controllers;

use SIPAN\App;

final readonly class DashtailPagesController
{
  static function render(): void {
    $url = App::request()->url;
    $page = substr($url, 1, -5);

    if ($page === 'login') {
      App::redirect('/ingresar');

      return;
    }

    if ($page === 'forgot') {
      App::renderPage($page, 'Forgot Password', 'dashtail-login-layout');

      return;
    }

    if ($page === 'register') {
      App::redirect('/registrarse');

      return;
    }

    App::renderPage($page, ucfirst($page), 'dashtail-layout');
  }
}
