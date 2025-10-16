<?php

namespace SIPAN\Controllers;

use SIPAN\Models\Produccion;
use SIPAN\Models\Producto;
use SIPAN\Models\Insumo;
use SIPAN\Models\Receta;
use SIPAN\Models\Negocio;
use SIPAN\Middlewares\AuthMiddleware;

class ProduccionesController
{
  private $produccionModel;
  private $productoModel;
  private $insumoModel;
  private $recetaModel;
  private $negocioModel;

  public function __construct()
  {
    $this->produccionModel = new Produccion();
    $this->productoModel = new Producto();
    $this->insumoModel = new Insumo();
    $this->recetaModel = new Receta();
    $this->negocioModel = new Negocio();
  }

  public function index()
  {
    AuthMiddleware::check();

    $user = AuthMiddleware::getUser();
    $sucursal_id = $user['sucursal_id'];

    $producciones = $this->produccionModel->getWithDetails($sucursal_id);

    require_once __DIR__ . '/../Views/producciones/index.php';
  }

  public function create()
  {
    AuthMiddleware::checkRole(['administrador', 'empleado']);

    $user = AuthMiddleware::getUser();
    $sucursal_id = $user['sucursal_id'];

    $productos = $this->productoModel->getConReceta($sucursal_id);

    require_once __DIR__ . '/../Views/producciones/create.php';
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

    $produccion_data = [
      'id_negocio' => $negocio['id'],
      'id_sucursal' => $sucursal_id,
      'id_usuario' => $user['id'],
      'id_producto' => $_POST['id_producto'] ?? 0,
      'cantidad_producida' => $_POST['cantidad_producida'] ?? 0,
      'costo_total' => $_POST['costo_total'] ?? 0
    ];

    $insumos = json_decode($_POST['insumos'] ?? '[]', true);

    if (empty($insumos)) {
      echo json_encode(['success' => false, 'message' => 'Debe especificar los insumos utilizados']);
      exit;
    }

    try {
      $produccion_id = $this->produccionModel->createWithInsumos($produccion_data, $insumos);
      echo json_encode(['success' => true, 'message' => 'Producción registrada correctamente', 'id' => $produccion_id]);
    } catch (\Exception $e) {
      echo json_encode(['success' => false, 'message' => 'Error al registrar producción: ' . $e->getMessage()]);
    }
    exit;
  }

  public function show($id)
  {
    AuthMiddleware::check();

    $produccion = $this->produccionModel->find($id);

    if (!$produccion) {
      header('Location: /producciones');
      exit;
    }

    $producto = $this->productoModel->find($produccion['id_producto']);
    $insumos = $this->produccionModel->getInsumos($id);

    require_once __DIR__ . '/../Views/producciones/show.php';
  }
}
