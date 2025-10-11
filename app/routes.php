<?php

use SIPAN\App;
use SIPAN\Controllers\DashboardController;
use SIPAN\Controllers\LandingController;
use SIPAN\Controllers\ProductApiController;
use SIPAN\Controllers\ProfileController;
use SIPAN\Controllers\UserApiController;
use SIPAN\Middlewares\EnsureUserIsNotLoggedMiddleware;
use Smolblog\OAuth2\Client\Provider\Twitter;

App::group('/api', static function (): void {
  App::route('POST /ingresar', UserApiController::login(...));
  App::route('POST /registrarse', UserApiController::register(...));
  App::route('/cerrar-sesion', UserApiController::logout(...));

  App::group('/productos', static function (): void {
    App::route('GET /', ProductApiController::index(...));
  });

  App::route('/gemini', static function (): void {
    $prompt = (App::request()->data->prompt ?: App::request()->query->prompt) ?: 'Hola';
    $apiKey = $_ENV['GEMINI_API_KEY'];
    $geminiClient = Gemini::client($apiKey);

    $transporterReflectionProperty = new ReflectionProperty($geminiClient, 'transporter');
    $transporterReflectionProperty->setAccessible(true);
    $transporter = $transporterReflectionProperty->getValue($geminiClient);

    $httpClientReflectionProperty = new ReflectionProperty($transporter, 'client');
    $httpClientReflectionProperty->setAccessible(true);
    $httpClient = $httpClientReflectionProperty->getValue($transporter);

    $configReflectionProperty = new ReflectionProperty($httpClient, 'config');
    $configReflectionProperty->setAccessible(true);

    $configReflectionProperty->setValue(
      $httpClient,
      ['verify' => false] + $configReflectionProperty->getValue($httpClient)
    );

    $response = $geminiClient
      ->generativeModel(model: 'gemini-2.0-flash')
      ->generateContent($prompt);

    // echo $response->text(); // Hello! How can I assist you today?
    Flight::halt(200, $response->text());

    // Helper method usage
    // $response = $geminiClient->generativeModel(
    //     model: GeminiHelper::generateGeminiModel(
    //         variation: ModelVariation::FLASH,
    //         generation: 2.5,
    //         version: "preview-04-17"
    //     ), // models/gemini-2.5-flash-preview-04-17
    // );
    // $response->text(); // Hello! How can I assist you today?
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
App::route('GET /oauth2/facebook', static function (): void {
  $codigo = App::request()->query->code;
  $estado = App::request()->query->state;

  if (!$codigo) {
    $urlDeFacebook = auth()->client('facebook')->getAuthorizationUrl();
    $estadoDeFacebook = auth()->client('facebook')->getState();
    session()->set('oauth2state', $estadoDeFacebook);
    App::redirect($urlDeFacebook);

    return;
  }

  if (!$estado || ($estado !== session()->get('oauth2state'))) {
    session()->remove('oauth2state');
    flash()->set(['No se pudo iniciar sesión con Facebook.' . ' El estado es inválido'], 'errores');
    App::redirect('/ingresar');

    return;
  }

  try {
    $token = auth()->client('facebook')->getAccessToken('authorization_code', [
      'code' => $codigo,
    ]);

    $usuarioDeFacebook = auth()->client('facebook')->getResourceOwner($token)->toArray();

    dd($usuarioDeFacebook);

    auth()->fromOAuth([
      'token' => $token,
      'user' => [
        'primer_nombre' => $usuarioDeFacebook['first_name'],
        'primer_apellido' => $usuarioDeFacebook['last_name'],
        'email' => $usuarioDeFacebook['email'] ?? ($usuarioDeFacebook['id'] . '@facebook.com'),
        'rol' => 'Administrador',
      ],
    ]);

    App::redirect('/administracion');
  } catch (Throwable $error) {
    flash()->set(['No se pudo iniciar sesión con Facebook. ' . $error->getMessage()], 'errores');
    App::redirect('/ingresar');

    return;
  }
});

App::route('GET /oauth2/twitter', static function (): void {
  $codigo = App::request()->query->code;
  $estado = App::request()->query->state;
  $twitter = auth()->client('twitter');

  assert($twitter instanceof Twitter);

  if (!$codigo) {
    session()->unset('oauth2state');
    session()->unset('oauth2verifier');

    $urlDeTwitter = auth()->client('twitter')->getAuthorizationUrl(/*[
      'scope' => [
        'tweet.read',
        'tweet.write',
        'tweet.moderate.write',
        'users.email',
        'users.read',
        'follows.read',
        'follows.write',
        'offline.access',
        'space.read',
        'mute.read',
        'mute.write',
        'like.read',
        'like.write',
        'list.read',
        'list.write',
        'block.read',
        'block.write',
        'bookmark.read',
        'bookmark.write',
      ],
    ]*/);

    $estadoDeTwitter = $twitter->getState();
    $verificadorDeTwitter = $twitter->getPkceVerifier();

    session()->set('oauth2state', $estadoDeTwitter);
    session()->set('oauth2verifier', $verificadorDeTwitter);

    App::redirect($urlDeTwitter);

    return;
  }

  if (!$estado || ($estado !== session()->get('oauth2state'))) {
    session()->remove('oauth2state');
    flash()->set(['No se pudo iniciar sesión con Twitter.' . ' El estado es inválido'], 'errores');
    App::redirect('/ingresar');

    return;
  }

  try {
    $token = $twitter->getAccessToken('authorization_code', [
      'code' => $codigo,
      'code_verifier' => session()->get('oauth2verifier'),
    ]);

    $usuarioDeTwitter = $twitter->getResourceOwner($token)->toArray();

    dd($usuarioDeTwitter);

    auth()->fromOAuth([
      'token' => $token,
      'user' => [
        'primer_nombre' => $usuarioDeTwitter['name'],
        'email' => $usuarioDeTwitter['email'] ?? ($usuarioDeTwitter['id'] . '@twitter.com'),
        'rol' => 'Administrador',
      ],
    ]);

    App::redirect('/administracion');
  } catch (Throwable $error) {
    flash()->set(['No se pudo iniciar sesión con Twitter. ' . $error->getMessage()], 'errores');
    App::redirect('/ingresar');

    return;
  }
});

App::route('GET /oauth2/github', static function (): void {
  $codigo = App::request()->query->code;
  $estado = App::request()->query->state;

  if (!$codigo) {
    $urlDeGithub = auth()->client('github')->getAuthorizationUrl();
    $estadoDeGithub = auth()->client('github')->getState();
    session()->set('oauth2state', $estadoDeGithub);
    App::redirect($urlDeGithub);

    return;
  }

  if (!$estado || ($estado !== session()->get('oauth2state'))) {
    session()->remove('oauth2state');
    flash()->set(['No se pudo iniciar sesión con GitHub.' . ' El estado es inválido'], 'errores');
    App::redirect('/ingresar');

    return;
  }

  try {
    $token = auth()->client('github')->getAccessToken('authorization_code', [
      'code' => $codigo,
    ]);

    $usuarioDeGithub = auth()->client('github')->getResourceOwner($token)->toArray();

    auth()->fromOAuth([
      'token' => $token,
      'user' => [
        'email' => $usuarioDeGithub['email'] ?? ($usuarioDeGithub['login'] . '@users.noreply.github.com'),
        'rol' => 'Administrador',
      ],
    ]);

    App::redirect('/administracion');
  } catch (Throwable $error) {
    flash()->set(['No se pudo iniciar sesión con GitHub. ' . $error->getMessage()], 'errores');
    App::redirect('/ingresar');

    return;
  }
});

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
