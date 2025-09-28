<?php

use SIPAN\App;
use SIPAN\Controllers\DashboardController;
use SIPAN\Controllers\LandingController;
use SIPAN\Controllers\ProductApiController;
use SIPAN\Controllers\ProfileController;
use SIPAN\Controllers\UserApiController;
use SIPAN\Middlewares\EnsureUserIsNotLoggedMiddleware;

App::group('/api', static function (): void {
  App::route('POST /ingresar', UserApiController::login(...));
  App::route('POST /registrarse', UserApiController::register(...));
  App::route('/cerrar-sesion', UserApiController::logout(...));

  App::group('/productos', static function (): void {
    App::route('GET /', ProductApiController::index(...));
  });
});

////////////////////////////////////
// 📌 Ruta pública (Landing Page) //
////////////////////////////////////
App::route('GET /', [LandingController::class, 'showLanding']);

App::route('GET /*.html', static function (): void {
  $url = App::request()->url;
  $page = substr($url, 1, -5);

  App::renderPage($page, ucfirst($page), 'dashtail-layout');
});

///////////////////////////////
// 📌 Rutas de autenticación //
///////////////////////////////
App::group('/ingresar', static function (): void {
  App::route('GET /', [ProfileController::class, 'showLogin']);
  App::route('POST /', [ProfileController::class, 'handleLogin']);
}, [EnsureUserIsNotLoggedMiddleware::class]);

App::group('/registrarse', static function (): void {
  App::route('GET /', [ProfileController::class, 'showRegister']);
  App::route('POST /', [ProfileController::class, 'handleRegister']);
}, [EnsureUserIsNotLoggedMiddleware::class]);

///////////////////////////////////////////
// 📌 Rutas protegidas con autenticación //
///////////////////////////////////////////
App::group('/administracion', static function (): void {
  App::route('GET /', DashboardController::showDashboard(...));
  App::route('POST /salir', [ProfileController::class, 'handleLogout']);
  App::route('GET /perfil', [ProfileController::class, 'showProfile']);

  App::group('/productos', static function (): void {
    App::route('GET /', static function (): void {
      App::renderPage('products', 'Inventario', 'dashboard-layout', [
        'products' => db()->select('productos')->all(),
      ]);
    });
  });
}, [/*EnsureUserIsLoggedMiddleware::class*/]);
