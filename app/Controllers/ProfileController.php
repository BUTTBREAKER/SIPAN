<?php

namespace SIPAN\Controllers;

use Exception;
use SIPAN\App;
use PDO;

final readonly class ProfileController
{
  function __construct(private PDO $db) {}

  static function showLogin(): void
  {
    App::renderPage('login', 'Iniciar Sesión', 'dashtail-login-layout');
  }

  static function showRegister(): void
  {
    App::renderPage('register', 'Registro', 'dashtail-login-layout');
  }

  function handleRegister(): void
  {
    $data = App::request()->data;

    if (
      empty($data->correo)
      || empty($data->clave)
      || empty($data->primer_nombre)
      || empty($data->primer_apellido)
      || empty($data->nombre_negocio)
      || empty($data->telefono)
    ) {
      App::json(['error' => 'Todos los campos son obligatorios'], 400);

      return;
    }

    try {
      $this->db->beginTransaction();

      // 🔹 Verificar si ya existe el usuario
      $stmt = $this->db->prepare('SELECT id FROM usuarios WHERE correo = :correo');
      $stmt->execute([':correo' => $data->correo]);

      if ($stmt->fetch()) {
        throw new Exception('El correo ya está registrado.');
      }

      // 🔹 Insertar el negocio
      $stmt = $this->db->prepare('
        INSERT INTO negocios (nombre, telefono, correo, es_principal, fecha_registro)
        VALUES (:nombre, :telefono, :correo, :es_principal, NOW())
      ');

      $stmt->execute([
        ':nombre' => $data->nombre_negocio,
        ':telefono' => $data->telefono,
        ':correo' => $data->correo,
        ':es_principal' => true  // Primer negocio es principal
      ]);

      $negocio_id = $this->db->lastInsertId();

      // 🔹 Registrar el usuario como administrador
      $stmt = $this->db->prepare("
        INSERT INTO usuarios (primer_nombre, segundo_nombre, primer_apellido, segundo_apellido, correo, clave, rol, activado, fecha_registro)
        VALUES (:primer_nombre, :segundo_nombre, :primer_apellido, :segundo_apellido, :correo, :clave, 'Administrador', 1, NOW())
      ");

      $stmt->execute([
        ':primer_nombre' => $data->primer_nombre,
        ':segundo_nombre' => $data->segundo_nombre ?? null,
        ':primer_apellido' => $data->primer_apellido,
        ':segundo_apellido' => $data->segundo_apellido ?? null,
        ':correo' => $data->correo,
        ':clave' => password_hash((string) $data->clave, PASSWORD_DEFAULT)
      ]);

      $user_id = $this->db->lastInsertId();

      // 🔹 Asignar el negocio al administrador
      $stmt = $this->db->prepare("
        INSERT INTO asignacion_de_negocios (id_usuario, id_negocio, fecha_registro)
        VALUES (:id_usuario, :id_negocio, NOW())
      ");

      $stmt->execute([
        ':id_usuario' => $user_id,
        ':id_negocio' => $negocio_id
      ]);

      $this->db->commit();
      App::json(['success' => 'Usuario registrado correctamente']);
    } catch (Exception $error) {
      $this->db->rollBack();
      App::json(['error' => $error->getMessage()], 400);
    }
  }

  function handleLogin(): void
  {
    $data = App::request()->data;

    if (empty($data->correo) || empty($data->clave)) {
      App::json(['error' => 'Correo y contraseña son obligatorios'], 400);

      return;
    }

    $stmt = $this->db->prepare('SELECT * FROM usuarios WHERE correo = :correo');
    $stmt->execute([':correo' => $data->correo]);
    $user = $stmt->fetch();

    if (!$user || !password_verify((string) $data->clave, (string) $user['clave'])) {
      App::json(['error' => 'Credenciales incorrectas'], 401);

      return;
    }

    session()->set('user', $user);

    App::json(['success' => 'Inicio de sesión exitoso']);
  }

  static function handleLogout(): void
  {
    session()->destroy();
    App::redirect('/');
  }

  static function showProfile(): void
  {
    App::renderPage('perfil', 'Perfil', 'dashboard-layout');
  }
}
