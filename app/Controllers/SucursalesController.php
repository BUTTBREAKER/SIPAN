<?php

namespace SIPAN\Controllers;

use SIPAN\Models\Sucursal;
use SIPAN\Models\Negocio;
use SIPAN\Middlewares\AuthMiddleware;

class SucursalesController
{
  private $sucursalModel;
  private $negocioModel;

  public function __construct()
  {
    AuthMiddleware::checkAuth();
    AuthMiddleware::checkRole(['administrador']);

    $this->sucursalModel = new Sucursal();
    $this->negocioModel = new Negocio();
  }

  public function index()
  {
    $user = AuthMiddleware::getUser();
    $negocio_id = $user['negocio_id'] ?? 1;

    $sucursales = $this->sucursalModel->getByNegocio($negocio_id);

    require_once __DIR__ . '/../../views/pages/sucursales/index.php';
  }

  public function create()
  {
    $user = AuthMiddleware::getUser();
    $negocio_id = $user['negocio_id'] ?? 1;

    $negocio = $this->negocioModel->find($negocio_id);

    require_once __DIR__ . '/../../views/pages/sucursales/create.php';
  }

  public function store()
  {
    header('Content-Type: application/json');

    try {
      $user = AuthMiddleware::getUser();
      $negocio_id = $user['negocio_id'] ?? 1;

      $input = json_decode(file_get_contents('php://input'), true);

      // Validar datos requeridos
      if (empty($input['nombre']) || empty($input['direccion'])) {
        echo json_encode(['success' => false, 'message' => 'Nombre y dirección son requeridos']);
        exit;
      }

      // Generar clave_sucursal única
      $clave_sucursal = $this->generarClaveSucursal();

      $data = [
        'id_negocio' => $negocio_id,
        'nombre' => $input['nombre'],
        'direccion' => $input['direccion'],
        'telefono' => $input['telefono'] ?? null,
        'correo' => $input['correo'] ?? null,
        'clave_sucursal' => $clave_sucursal,
        'estado' => 'activo'
      ];

      $sucursal_id = $this->sucursalModel->create($data);

      echo json_encode([
        'success' => true,
        'message' => 'Sucursal creada exitosamente',
        'sucursal_id' => $sucursal_id,
        'clave_sucursal' => $clave_sucursal
      ]);
    } catch (\Exception $e) {
      echo json_encode(['success' => false, 'message' => 'Error al crear sucursal: ' . $e->getMessage()]);
    }

    exit;
  }

  public function show($id)
  {
    $sucursal = $this->sucursalModel->find($id);

    if (!$sucursal) {
      header('Location: /sucursales');
      exit;
    }

    // Obtener estadísticas de la sucursal
    $stats = $this->sucursalModel->getStats($id);

    require_once __DIR__ . '/../Views/sucursales/show.php';
  }

  public function edit($id)
  {
    $sucursal = $this->sucursalModel->find($id);

    if (!$sucursal) {
      header('Location: /sucursales');
      exit;
    }

    require_once __DIR__ . '/../Views/sucursales/edit.php';
  }

  public function update($id)
  {
    header('Content-Type: application/json');

    try {
      $input = json_decode(file_get_contents('php://input'), true);

      $data = [
        'nombre' => $input['nombre'],
        'direccion' => $input['direccion'],
        'telefono' => $input['telefono'] ?? null,
        'correo' => $input['correo'] ?? null
      ];

      // No permitir cambiar clave_sucursal una vez creada

      $this->sucursalModel->update($id, $data);

      echo json_encode(['success' => true, 'message' => 'Sucursal actualizada exitosamente']);
    } catch (\Exception $e) {
      echo json_encode(['success' => false, 'message' => 'Error al actualizar sucursal: ' . $e->getMessage()]);
    }

    exit;
  }

  public function cambiarEstado()
  {
    header('Content-Type: application/json');

    try {
      $input = json_decode(file_get_contents('php://input'), true);
      $sucursal_id = $input['sucursal_id'] ?? null;
      $estado = $input['estado'] ?? null;

      if (!$sucursal_id || !$estado) {
        echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
        exit;
      }

      $this->sucursalModel->update($sucursal_id, ['estado' => $estado]);

      echo json_encode(['success' => true, 'message' => 'Estado actualizado correctamente']);
    } catch (\Exception $e) {
      echo json_encode(['success' => false, 'message' => 'Error al cambiar estado']);
    }

    exit;
  }

  public function regenerarClave($id)
  {
    header('Content-Type: application/json');

    try {
      $nueva_clave = $this->generarClaveSucursal();

      $this->sucursalModel->update($id, ['clave_sucursal' => $nueva_clave]);

      echo json_encode([
        'success' => true,
        'message' => 'Clave regenerada exitosamente',
        'clave_sucursal' => $nueva_clave
      ]);
    } catch (\Exception $e) {
      echo json_encode(['success' => false, 'message' => 'Error al regenerar clave']);
    }

    exit;
  }

  private function generarClaveSucursal()
  {
    // Generar clave alfanumérica de 8 caracteres
    $caracteres = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789'; // Sin caracteres ambiguos
    $clave = '';

    for ($i = 0; $i < 8; $i++) {
      $clave .= $caracteres[rand(0, strlen($caracteres) - 1)];
    }

    // Verificar que no exista
    $existe = $this->sucursalModel->findByClave($clave);

    if ($existe) {
      return $this->generarClaveSucursal(); // Recursivo si ya existe
    }

    return $clave;
  }
}
