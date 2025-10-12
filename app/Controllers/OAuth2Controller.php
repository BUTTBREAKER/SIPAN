<?php

namespace SIPAN\Controllers;

use SIPAN\App;
use Smolblog\OAuth2\Client\Provider\Twitter;
use Throwable;

final readonly class OAuth2Controller
{
  static function loginWithGoogle(): void
  {
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
  }

  static function loginWithFacebook(): void
  {
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
  }

  static function loginWithGithub(): void
  {
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
  }

  static function loginWithTwitter(): void
  {
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
  }
}
