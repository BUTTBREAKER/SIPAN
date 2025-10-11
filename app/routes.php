<?php

use SIPAN\App;
use SIPAN\Controllers\DashboardController;
use SIPAN\Controllers\DashtailPagesController;
use SIPAN\Controllers\GeminiController;
use SIPAN\Controllers\LandingController;
use SIPAN\Controllers\OAuth2Controller;
use SIPAN\Controllers\ProductApiController;
use SIPAN\Controllers\ProfileController;
use SIPAN\Controllers\UserApiController;
use SIPAN\Middlewares\EnsureUserIsLoggedMiddleware;
use SIPAN\Middlewares\EnsureUserIsNotLoggedMiddleware;

App::group('/api', static function (): void {
  App::route('POST /ingresar', UserApiController::login(...));
  App::route('POST /registrarse', UserApiController::register(...));
  App::route('/cerrar-sesion', UserApiController::logout(...));

  App::group('/productos', static function (): void {
    App::route('GET /', ProductApiController::index(...));
  });

  App::route('/gemini', GeminiController::simplePrompt(...));
});

////////////////////////////////////
// 📌 Ruta pública (Landing Page) //
////////////////////////////////////
App::route('GET /', LandingController::showLanding(...))->addMiddleware(EnsureUserIsNotLoggedMiddleware::class);
App::route('GET /*.html', DashtailPagesController::render(...));

///////////////////////////////
// 📌 Rutas de autenticación //
///////////////////////////////
App::group('/oauth2', static function (): void {
  App::route('GET /facebook', OAuth2Controller::loginWithFacebook(...));
  App::route('GET /twitter', OAuth2Controller::loginWithTwitter(...));
  App::route('GET /github', OAuth2Controller::loginWithGithub(...));
  App::route('GET /google', OAuth2Controller::loginWithGoogle(...));
});

App::group('/ingresar', static function (): void {
  App::route('GET /', ProfileController::showLogin(...));
  App::route('POST /', [ProfileController::class, 'handleLogin']);
}, [EnsureUserIsNotLoggedMiddleware::class]);

App::group('/registrarse', static function (): void {
  App::route('GET /', ProfileController::showRegister(...));
  App::route('POST /', [ProfileController::class, 'handleRegister']);
}, [EnsureUserIsNotLoggedMiddleware::class]);

///////////////////////////////////////////
// 📌 Rutas protegidas con autenticación //
///////////////////////////////////////////
App::group('/administracion', static function (): void {
  App::route('GET /', DashboardController::showDashboard(...));
  App::route('/salir', [ProfileController::class, 'handleLogout']);
  App::route('GET /perfil', [ProfileController::class, 'showProfile']);

  App::group('/productos', static function (): void {
    App::route('GET /', static function (): void {
      App::renderPage('products', 'Inventario', 'dashboard-layout', [
        'products' => db()->select('productos')->all(),
      ]);
    });
  });
}, [EnsureUserIsLoggedMiddleware::class]);
