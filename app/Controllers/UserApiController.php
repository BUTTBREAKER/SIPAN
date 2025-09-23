<?php

declare(strict_types=1);

namespace SIPAN\Controllers;

use SIPAN\App;

final readonly class UserApiController
{
  static function login(): void
  {
    $credentials = App::request()->data->getData();

    if (!auth()->login($credentials)) {
      $firstError = array_values(auth()->errors())[0];

      App::halt(401, $firstError);
    }

    App::json(auth()->user()->get());
  }

  static function register(): void
  {
    $data = App::request()->data;

    db()
      ->beginTransaction()
      ->insert('negocios')
      ->params([
        'nombre' => $data->nombre_negocio,
        'telefono' => $data->telefono,
        'correo' => $data->correo,
        'es_principal' => filter_var($data->es_principal, FILTER_VALIDATE_BOOL),
        'created_at' => date('Y-m-d H:i:s'),
      ])
      ->execute();

    auth()->register([
      'primer_nombre' => $data->primer_nombre,
      'segundo_nombre' => $data->segundo_nombre ?: null,
      'primer_apellido' => $data->primer_apellido,
      'segundo_apellido' => $data->segundo_apellido ?: null,
      'correo' => $data->correo,
      'clave' => $data->clave,
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
