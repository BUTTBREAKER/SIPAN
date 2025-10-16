<?php

namespace SIPAN\Controllers;

use SIPAN\Models\Receta;
use SIPAN\Models\Producto;
use SIPAN\Models\Insumo;
use SIPAN\Middlewares\AuthMiddleware;

class RecetasController
{
  private $recetaModel;
  private $productoModel;
  private $insumoModel;

  public function __construct()
  {
    $this->recetaModel = new Receta();
    $this->productoModel = new Producto();
    $this->insumoModel = new Insumo();
  }

  public function index()
  {
    AuthMiddleware::check();

    $user = AuthMiddleware::getUser();
    $sucursal_id = $user['sucursal_id'];

    $recetas = $this->recetaModel->getWithDetails($sucursal_id);

    require_once __DIR__ . '/../Views/recetas/index.php';
  }

  public function create()
  {
    AuthMiddleware::checkRole(['administrador', 'empleado']);

    $user = AuthMiddleware::getUser();
    $sucursal_id = $user['sucursal_id'];

    $productos = $this->productoModel->all($sucursal_id);
    $insumos = $this->insumoModel->all($sucursal_id);

    require_once __DIR__ . '/../Views/recetas/create.php';
  }

  public function store()
  {
    AuthMiddleware::checkRole(['administrador', 'empleado']);
    header('Content-Type: application/json');

    $id_producto = $_POST['id_producto'] ?? null;
    $rendimiento = $_POST['rendimiento'] ?? null;
    $instrucciones = $_POST['instrucciones'] ?? '';
    $insumos = json_decode($_POST['insumos'] ?? '[]', true);
    $user = AuthMiddleware::getUser();
    $sucursal_id = $user['sucursal_id'];

    if (!$id_producto || !$rendimiento || empty($insumos)) {
      echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
      return;
    }

    try {
      $this->recetaModel->createWithInsumos($id_producto, $rendimiento, $instrucciones, $sucursal_id, $insumos);
      echo json_encode(['success' => true, 'message' => 'Receta guardada correctamente']);
    } catch (\Exception $e) {
      echo json_encode(['success' => false, 'message' => 'Error al guardar receta: ' . $e->getMessage()]);
    }
  }



  public function edit($id)
  {
    AuthMiddleware::checkRole(['administrador', 'empleado']);

    $user = AuthMiddleware::getUser();
    $sucursal_id = $user['sucursal_id'];

    $receta = $this->recetaModel->find($id);

    if (!$receta) {
      header('Location: /recetas');
      exit;
    }

    // Nombres corregidos
    $receta_insumos = $this->recetaModel->getInsumos($id);
    $insumos_disponibles = $this->insumoModel->all($sucursal_id);

    require_once __DIR__ . '/../Views/recetas/edit.php';
  }


  public function update($id)
  {
    AuthMiddleware::checkRole(['administrador', 'empleado']);

    header('Content-Type: application/json');

    $data = [
      'id_producto' => $_POST['id_producto'] ?? 0,
      'nombre' => $_POST['nombre'] ?? '',
      'descripcion' => $_POST['descripcion'] ?? '',
      'rendimiento' => $_POST['rendimiento'] ?? 1,
      'tiempo_preparacion' => $_POST['tiempo_preparacion'] ?? 0,
      'instrucciones' => $_POST['instrucciones'] ?? ''
    ];

    try {
      $this->recetaModel->update($id, $data);
      echo json_encode(['success' => true, 'message' => 'Receta actualizada correctamente']);
    } catch (\Exception $e) {
      echo json_encode(['success' => false, 'message' => 'Error al actualizar receta: ' . $e->getMessage()]);
    }
    exit;
  }

  public function delete($id)
  {
    AuthMiddleware::checkRole(['administrador']);

    header('Content-Type: application/json');

    try {
      $this->recetaModel->delete($id);
      echo json_encode(['success' => true, 'message' => 'Receta eliminada correctamente']);
    } catch (\Exception $e) {
      echo json_encode(['success' => false, 'message' => 'Error al eliminar receta: ' . $e->getMessage()]);
    }
    exit;
  }

  public function calcular()
  {
    AuthMiddleware::check();

    header('Content-Type: application/json');

    $producto_id = $_POST['producto_id'] ?? 0;
    $cantidad = $_POST['cantidad'] ?? 0;

    if (!$producto_id || !$cantidad) {
      echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
      exit;
    }

    try {
      $insumos = $this->recetaModel->calcularInsumos($producto_id, $cantidad);
      echo json_encode(['success' => true, 'insumos' => $insumos]);
    } catch (\Exception $e) {
      echo json_encode(['success' => false, 'message' => 'Error al calcular insumos: ' . $e->getMessage()]);
    }
    exit;
  }

  public function addInsumo()
  {
    AuthMiddleware::checkRole(['administrador', 'empleado']);

    header('Content-Type: application/json');

    $receta_id = $_POST['receta_id'] ?? 0;
    $insumo_id = $_POST['insumo_id'] ?? 0;
    $cantidad = $_POST['cantidad'] ?? 0;
    $unidad_medida = $_POST['unidad_medida'] ?? 'kg';

    try {
      $this->recetaModel->addInsumo($receta_id, $insumo_id, $cantidad, $unidad_medida);
      echo json_encode(['success' => true, 'message' => 'Insumo agregado a la receta']);
    } catch (\Exception $e) {
      echo json_encode(['success' => false, 'message' => 'Error al agregar insumo: ' . $e->getMessage()]);
    }
    exit;
  }

  public function removeInsumo()
  {
    AuthMiddleware::checkRole(['administrador', 'empleado']);

    header('Content-Type: application/json');

    $receta_id = $_POST['receta_id'] ?? 0;
    $insumo_id = $_POST['insumo_id'] ?? 0;

    try {
      $this->recetaModel->removeInsumo($receta_id, $insumo_id);
      echo json_encode(['success' => true, 'message' => 'Insumo eliminado de la receta']);
    } catch (\Exception $e) {
      echo json_encode(['success' => false, 'message' => 'Error al eliminar insumo: ' . $e->getMessage()]);
    }
    exit;
  }

  /**
   * Listar recetas para el modal de cálculo de insumos
   */
  public function list()
  {
    AuthMiddleware::check();

    header('Content-Type: application/json');

    $user = AuthMiddleware::getUser();
    $sucursal_id = $user['sucursal_id'];

    try {
      $recetas = $this->recetaModel->all($sucursal_id);

      echo json_encode([
        'success' => true,
        'recetas' => $recetas
      ]);
    } catch (\Exception $e) {
      echo json_encode([
        'success' => false,
        'message' => 'Error al obtener recetas: ' . $e->getMessage()
      ]);
    }
    exit;
  }
}
