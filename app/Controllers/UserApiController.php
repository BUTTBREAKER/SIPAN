<?php

declare(strict_types=1);

namespace SIPAN\Controllers;

use SIPAN\App;

final readonly class UserApiController
{
  static function login(): void
  {
    $credentials = App::request()->data->getData();

    $validatedCredentials = form()->validate($credentials, [
      'correo' => 'email',
      'clave' => 'password',
    ]);

    if (!$validatedCredentials) {
      $firstError = array_values(form()->errors())[0];

      App::halt(400, $firstError);
    }

    if (!auth()->login($validatedCredentials)) {
      $firstError = array_values(auth()->errors())[0];

      App::halt(401, $firstError);
    }

    App::json(auth()->user()->get());
  }

  static function register(): void
  {
    $data = App::request()->data;

    $validatedData = form()->validate($data->getData(), [
      'nombre_negocio' => 'text',
      'telefono' => 'phone',
      'correo' => 'email',
      'es_principal' => 'boolean',
      'primer_nombre' => 'text',
      'segundo_nombre' => 'optional|text',
      'primer_apellido' => 'text',
      'segundo_apellido' => 'optional|text',
      'clave' => 'password',
    ]);

    if (!$validatedData) {
      $firstError = array_values(form()->errors())[0];

      App::halt(400, $firstError);
    }

    db()
      ->beginTransaction()
      ->insert('negocios')
      ->params([
        'nombre' => $validatedData['nombre_negocio'],
        'telefono' => $validatedData['telefono'],
        'correo' => $validatedData['correo'],
        'es_principal' => filter_var($validatedData['es_principal'], FILTER_VALIDATE_BOOL),
        'created_at' => date('Y-m-d H:i:s'),
      ])
      ->execute();

    auth()->register([
      'primer_nombre' => $validatedData['primer_nombre'],
      'segundo_nombre' => $validatedData['segundo_nombre'] ?: null,
      'primer_apellido' => $validatedData['primer_apellido'],
      'segundo_apellido' => $validatedData['segundo_apellido'] ?: null,
      'correo' => $validatedData['correo'],
      'clave' => $validatedData['clave'],
      'rol' => 'Administrador',
    ]);

    db()->commit();

    App::halt(201);
  }

  static function logout(): void
  {
    auth()->logout();
  }
}
