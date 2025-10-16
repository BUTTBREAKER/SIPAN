<?php

namespace SIPAN\Controllers;

use SIPAN\Models\Insumo;
use SIPAN\Models\Negocio;
use SIPAN\Middlewares\AuthMiddleware;

class InsumosController
{
  private $insumoModel;
  private $negocioModel;

  public function __construct()
  {
    $this->insumoModel = new Insumo();
    $this->negocioModel = new Negocio();
  }

  public function index()
  {
    AuthMiddleware::check();

    $user = AuthMiddleware::getUser();
    $sucursal_id = $user['sucursal_id'];

    $insumos = $this->insumoModel->all($sucursal_id);

    require_once __DIR__ . '/../Views/insumos/index.php';
  }

  public function create()
  {
    AuthMiddleware::checkRole(['administrador', 'empleado']);

    require_once __DIR__ . '/../Views/insumos/create.php';
  }

  public function store()
  {
    AuthMiddleware::checkRole(['administrador', 'empleado']);

    header('Content-Type: application/json');

    $user = AuthMiddleware::getUser();
    $sucursal_id = $user['sucursal_id'];

    $negocio = $this->negocioModel->getBySucursal($sucursal_id);

    if (!$negocio) {
      echo json_encode(['success' => false, 'message' => 'No se encontró negocio para esta sucursal']);
      exit;
    }

    $data = [
      'id_negocio' => $negocio['id'],
      'id_sucursal' => $sucursal_id,
      'id_usuario' => $user['id'],
      'nombre' => $_POST['nombre'] ?? '',
      'descripcion' => $_POST['descripcion'] ?? '',
      'unidad_medida' => $_POST['unidad_medida'] ?? 'kg',
      'stock_actual' => $_POST['stock_actual'] ?? 0,
      'stock_minimo' => $_POST['stock_minimo'] ?? 0,
      'precio_unitario' => $_POST['precio_unitario'] ?? 0
    ];

    try {
      $id = $this->insumoModel->create($data);
      echo json_encode(['success' => true, 'message' => 'Insumo creado correctamente', 'id' => $id]);
    } catch (\Exception $e) {
      echo json_encode(['success' => false, 'message' => 'Error al crear insumo: ' . $e->getMessage()]);
    }
    exit;
  }

  public function edit($id)
  {
    AuthMiddleware::checkRole(['administrador', 'empleado']);

    $insumo = $this->insumoModel->find($id);

    if (!$insumo) {
      header('Location: /insumos');
      exit;
    }

    require_once __DIR__ . '/../Views/insumos/edit.php';
  }

  public function update($id)
  {
    AuthMiddleware::checkRole(['administrador', 'empleado']);

    header('Content-Type: application/json');

    $data = [
      'nombre' => $_POST['nombre'] ?? '',
      'descripcion' => $_POST['descripcion'] ?? '',
      'unidad_medida' => $_POST['unidad_medida'] ?? 'kg',
      'stock_actual' => $_POST['stock_actual'] ?? 0,
      'stock_minimo' => $_POST['stock_minimo'] ?? 0,
      'precio_unitario' => $_POST['precio_unitario'] ?? 0
    ];

    try {
      $this->insumoModel->update($id, $data);
      echo json_encode(['success' => true, 'message' => 'Insumo actualizado correctamente']);
    } catch (\Exception $e) {
      echo json_encode(['success' => false, 'message' => 'Error al actualizar insumo: ' . $e->getMessage()]);
    }
    exit;
  }

  public function delete($id)
  {
    AuthMiddleware::checkRole(['administrador']);

    header('Content-Type: application/json');

    try {
      $this->insumoModel->delete($id);
      echo json_encode(['success' => true, 'message' => 'Insumo eliminado correctamente']);
    } catch (\Exception $e) {
      echo json_encode(['success' => false, 'message' => 'Error al eliminar insumo: ' . $e->getMessage()]);
    }
    exit;
  }

  public function search()
  {
    AuthMiddleware::check();

    header('Content-Type: application/json');

    $user = AuthMiddleware::getUser();
    $sucursal_id = $user['sucursal_id'];
    $search = $_GET['q'] ?? '';

    if (empty($search)) {
      $insumos = $this->insumoModel->all($sucursal_id);
    } else {
      $insumos = $this->insumoModel->search($search, $sucursal_id);
    }

    echo json_encode(['success' => true, 'insumos' => $insumos]);
    exit;
  }

  public function stockBajo()
  {
    AuthMiddleware::check();

    header('Content-Type: application/json');

    $user = AuthMiddleware::getUser();
    $sucursal_id = $user['sucursal_id'];

    $insumos = $this->insumoModel->getWithStockBajo($sucursal_id);

    echo json_encode(['success' => true, 'insumos' => $insumos]);
    exit;
  }
}
