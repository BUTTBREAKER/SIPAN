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
});

///////////////////////////////
// 📌 Rutas de autenticación //
///////////////////////////////
App::route('GET /oauth2/google', static function (): void {
  $error = App::request()->query->error;
  $codigo = App::request()->query->code;
  $estado = App::request()->query->state;

  if ($error) {
    flash()->set(['No se pudo iniciar sesión con Google. ' . $error], 'errores');
    App::redirect('/ingresar');

    return;
  }

  if (!$codigo) {
    $urlDeGoogle = auth()->client('google')->getAuthorizationUrl();
    $estadoDeGoogle = auth()->client('google')->getState();
    session()->set('oauth2state', $estadoDeGoogle);
    App::redirect($urlDeGoogle);

    return;
  }

  if (!$estado || ($estado !== session()->get('oauth2state'))) {
    session()->remove('oauth2state');
    flash()->set(['No se pudo iniciar sesión con Google.' . ' El estado es inválido'], 'errores');
    App::redirect('/ingresar');

    return;
  }

  try {
    $token = auth()->client('google')->getAccessToken('authorization_code', [
      'code' => $codigo,
    ]);

    $usuarioDeGoogle = auth()->client('google')->getResourceOwner($token)->toArray();

    auth()->fromOAuth([
      'token' => $token,
      'user' => [
        'primer_nombre' => $usuarioDeGoogle['given_name'],
        'primer_apellido' => $usuarioDeGoogle['family_name'],
        'email' => $usuarioDeGoogle['email'],
        'rol' => 'Administrador',
      ],
    ]);

    App::redirect('/administracion');
  } catch (Throwable $error) {
    flash()->set(['No se pudo iniciar sesión con Google. ' . $error->getMessage()], 'errores');
    App::redirect('/ingresar');

    return;
  }
});

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
  App::route('/salir', [ProfileController::class, 'handleLogout']);
  App::route('GET /perfil', [ProfileController::class, 'showProfile']);

  App::group('/productos', static function (): void {
    App::route('GET /', static function (): void {
      App::renderPage('products', 'Inventario', 'dashboard-layout', [
        'products' => db()->select('productos')->all(),
      ]);
    });
  });
}, [/*EnsureUserIsLoggedMiddleware::class*/]);
