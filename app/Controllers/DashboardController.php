<?php

namespace SIPAN\Controllers;

use SIPAN\App;
use SIPAN\Models\Producto;
use SIPAN\Models\Insumo;
use SIPAN\Models\Venta;
use SIPAN\Models\Notificacion;
use SIPAN\Middlewares\AuthMiddleware;

class DashboardController
{
  private $productoModel;
  private $insumoModel;
  private $ventaModel;
  private $notificacionModel;

  public function __construct()
  {
    $this->productoModel = new Producto();
    $this->insumoModel = new Insumo();
    $this->ventaModel = new Venta();
    $this->notificacionModel = new Notificacion();
  }

  public function index()
  {
    AuthMiddleware::check();

    $user = AuthMiddleware::getUser();
    $sucursal_id = $user['sucursal_id'];

    // Verificar stock bajo y crear notificaciones
    $this->notificacionModel->verificarStockBajo();

    // Obtener datos del dashboard
    $data = [
      'productos_stock_bajo' => $this->productoModel->getWithStockBajo($sucursal_id),
      'insumos_stock_bajo' => $this->insumoModel->getWithStockBajo($sucursal_id),
      'ventas_hoy' => $this->ventaModel->getTotalVentas($sucursal_id, date('Y-m-d'), date('Y-m-d')),
      'ventas_semana' => $this->ventaModel->getTotalVentas($sucursal_id, date('Y-m-d', strtotime('-7 days')), date('Y-m-d')),
      'ventas_mes' => $this->ventaModel->getTotalVentas($sucursal_id, date('Y-m-01'), date('Y-m-d')),
      'notificaciones' => $this->notificacionModel->getNoLeidas($sucursal_id, $user['id']),
      'total_notificaciones' => $this->notificacionModel->countNoLeidas($sucursal_id, $user['id']),
      'ventas_ultimos_dias' => $this->ventaModel->getVentasUltimosDias($sucursal_id, 7),
      'productos_mas_vendidos' => $this->ventaModel->getProductosMasVendidos($sucursal_id, 5)
    ];

    App::render('pages/dashboard/index.php', $data);
  }

  public function getNotificaciones()
  {
    AuthMiddleware::check();

    header('Content-Type: application/json');

    $user = AuthMiddleware::getUser();
    $sucursal_id = $user['sucursal_id'];

    $notificaciones = $this->notificacionModel->getNoLeidas($sucursal_id, $user['id']);

    echo json_encode([
      'success' => true,
      'notificaciones' => $notificaciones,
      'total' => count($notificaciones)
    ]);
    exit;
  }

  public function marcarNotificacionLeida()
  {
    AuthMiddleware::check();

    header('Content-Type: application/json');

    $id = $_POST['id'] ?? null;

    if ($id) {
      $this->notificacionModel->marcarComoLeida($id);
      echo json_encode(['success' => true]);
    } else {
      echo json_encode(['success' => false, 'message' => 'ID no válido']);
    }
    exit;
  }
}
