<?php

namespace SIPAN\Controllers;

use SIPAN\App;
use SIPAN\Models\Usuario;
use SIPAN\Models\Sucursal;
use SIPAN\Middlewares\AuthMiddleware;

class AuthController
{
  private $usuarioModel;
  private $sucursalModel;

  public function __construct()
  {
    $this->usuarioModel = new Usuario();
    $this->sucursalModel = new Sucursal();
  }

  public function showLogin()
  {
    if (AuthMiddleware::isAuthenticated()) {
      header('Location: /dashboard');
      exit;
    }

    App::render('pages/auth/login');
  }

  public function login()
  {
    header('Content-Type: application/json');

    $correo = $_POST['correo'] ?? '';
    $clave = $_POST['clave'] ?? '';

    if (empty($correo) || empty($clave)) {
      echo json_encode(['success' => false, 'message' => 'Correo y contraseña son requeridos']);
      exit;
    }

    $user = $this->usuarioModel->authenticate($correo, $clave);

    if ($user) {
      if ($user['estado'] !== 'activo') {
        echo json_encode(['success' => false, 'message' => 'Usuario inactivo']);
        exit;
      }

      AuthMiddleware::setUser($user);

      // Obtener nombre de sucursal
      if ($user['id_sucursal']) {
        $sucursal = $this->sucursalModel->find($user['id_sucursal']);
        $_SESSION['sucursal_nombre'] = $sucursal['nombre'] ?? '';
      }

      echo json_encode(['success' => true, 'message' => 'Inicio de sesión exitoso']);
    } else {
      echo json_encode(['success' => false, 'message' => 'Credenciales incorrectas']);
    }
    exit;
  }

  public function logout()
  {
    AuthMiddleware::logout();
  }

  public function cambiarSucursal()
  {
    AuthMiddleware::checkRole(['administrador']);

    header('Content-Type: application/json');

    $sucursal_id = $_POST['sucursal_id'] ?? null;

    if (!$sucursal_id) {
      echo json_encode(['success' => false, 'message' => 'Sucursal no válida']);
      exit;
    }

    $sucursal = $this->sucursalModel->find($sucursal_id);

    if ($sucursal) {
      $_SESSION['sucursal_id'] = $sucursal['id'];
      $_SESSION['sucursal_nombre'] = $sucursal['nombre'];
      echo json_encode(['success' => true, 'message' => 'Sucursal cambiada correctamente']);
    } else {
      echo json_encode(['success' => false, 'message' => 'Sucursal no encontrada']);
    }
    exit;
  }

  public function showRegister()
  {
    App::render('pages/auth/register');
  }

  public function register()
  {
    header('Content-Type: application/json');

    $input = json_decode(file_get_contents('php://input'), true);

    // Validar datos requeridos
    if (
      empty($input['nombre']) || empty($input['dni']) || empty($input['telefono']) ||
      empty($input['correo']) || empty($input['clave']) || empty($input['clave_sucursal']) ||
      empty($input['rol'])
    ) {
      echo json_encode(['success' => false, 'message' => 'Complete todos los campos requeridos']);
      exit;
    }

    // Validar que el rol sea solo cajero o empleado
    if (!in_array($input['rol'], ['cajero', 'empleado'])) {
      echo json_encode(['success' => false, 'message' => 'Rol no válido. Solo puede registrarse como Cajero o Empleado']);
      exit;
    }

    // Verificar clave_sucursal
    $sucursal = $this->sucursalModel->findByClave($input['clave_sucursal']);
    if (!$sucursal) {
      echo json_encode(['success' => false, 'message' => 'Código de sucursal inválido']);
      exit;
    }

    // Verificar si el correo ya existe
    $existingUser = $this->usuarioModel->findByEmail($input['correo']);
    if ($existingUser) {
      echo json_encode(['success' => false, 'message' => 'El correo ya está registrado']);
      exit;
    }

    // Verificar si el DNI ya existe
    $existingDNI = $this->usuarioModel->findByDNI($input['dni']);
    if ($existingDNI) {
      echo json_encode(['success' => false, 'message' => 'La cédula/RIF ya está registrada']);
      exit;
    }

    $sucursal_id = $sucursal['id'];
    $negocio_id = $sucursal['id_negocio'];

    try {
      // Crear usuario
      $usuario_id = $this->usuarioModel->create([
        'nombre' => $input['nombre'],
        'correo' => $input['correo'],
        'clave' => password_hash($input['clave'], PASSWORD_DEFAULT),
        'rol' => $input['rol'],
        'id_sucursal' => $sucursal_id,
        'id_negocio' => $negocio_id,
        'dni' => $input['dni'],
        'telefono' => $input['telefono'],
        'estado' => 'activo'
      ]);

      $response = [
        'success' => true,
        'message' => 'Usuario registrado exitosamente',
        'usuario_id' => $usuario_id
      ];

      echo json_encode($response);
    } catch (\Exception $e) {
      // Si se usa una transacción, haga rollback usando la instancia correcta.
      // Por ejemplo: $this->usuarioModel->rollBack(); -- descomentar y ajustar si aplica.
      echo json_encode(['success' => false, 'message' => 'Error al registrar usuario: ' . $e->getMessage()]);
    }

    exit;
  }

  public function verificarSucursal()
  {
    header('Content-Type: application/json');

    $input = json_decode(file_get_contents('php://input'), true);
    $clave = $input['clave'] ?? '';

    if (empty($clave)) {
      echo json_encode(['success' => false, 'message' => 'Clave requerida']);
      exit;
    }

    $sucursal = $this->sucursalModel->findByClave($clave);

    if ($sucursal) {
      echo json_encode([
        'success' => true,
        'sucursal' => [
          'id' => $sucursal['id'],
          'nombre' => $sucursal['nombre'],
          'direccion' => $sucursal['direccion']
        ]
      ]);
    } else {
      echo json_encode(['success' => false, 'message' => 'Clave de sucursal inválida']);
    }

    exit;
  }

  private function generarClaveSucursal()
  {
    // Generar clave alfanumérica de 8 caracteres
    $caracteres = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
    $clave = '';
    for ($i = 0; $i < 8; $i++) {
      $clave .= $caracteres[rand(0, strlen($caracteres) - 1)];
    }
    return $clave;
  }

  public function verificarClaveSucursal()
  {
    header('Content-Type: application/json');

    $data = json_decode(file_get_contents('php://input'), true);
    $clave_sucursal = $data['clave_sucursal'] ?? '';

    if (empty($clave_sucursal)) {
      echo json_encode(['success' => false, 'message' => 'Clave de sucursal requerida']);
      exit;
    }

    $sucursal = $this->sucursalModel->findByClave($clave_sucursal);

    if ($sucursal && $sucursal['estado'] === 'activa') {
      echo json_encode([
        'success' => true,
        'sucursal' => [
          'id' => $sucursal['id'],
          'nombre' => $sucursal['nombre'],
          'direccion' => $sucursal['direccion']
        ]
      ]);
    } else {
      echo json_encode(['success' => false, 'message' => 'Clave de sucursal inválida o sucursal inactiva']);
    }
    exit;
  }
}
