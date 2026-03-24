<?php

namespace App\Controllers;

use App\Models\Producto;
use App\Models\Insumo;
use App\Models\Venta;
use App\Models\Notificacion;
use App\Middlewares\AuthMiddleware;

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
        $user_rol = isset($_SESSION['user_rol']) ? $_SESSION['user_rol'] : 'empleado';

        // Verificar stock bajo y crear notificaciones
        $this->notificacionModel->verificarStockBajo();

        // Cargar modelo Lotes para vencimientos
        $loteModel = new \App\Models\Lote();

        // Bolt: Consolidación de métricas de ventas en una sola consulta
        // Obtenemos los últimos 31 días para cubrir el mes actual completo (o últimos 30 días)
        $ventas_data = $this->ventaModel->getVentasUltimosDias($sucursal_id, 31);

        $ventas_hoy = 0;
        $ventas_semana = 0;
        $ventas_mes = 0;
        $hoy = date('Y-m-d');
        $hace_7_dias = date('Y-m-d', strtotime('-7 days'));
        $primer_dia_mes = date('Y-m-01');

        foreach ($ventas_data as $v) {
            $fecha = $v['fecha_full'];
            $total = $v['total'];

            if ($fecha === $hoy) {
                $ventas_hoy = $total;
            }
            if ($fecha >= $hace_7_dias) {
                $ventas_semana += $total;
            }
            if ($fecha >= $primer_dia_mes) {
                $ventas_mes += $total;
            }
        }

        // Obtener datos del dashboard
        $data = [
            'productos_stock_bajo' => $this->productoModel->getWithStockBajo($sucursal_id),
            'insumos_stock_bajo' => $this->insumoModel->getWithStockBajo($sucursal_id),
            'ventas_hoy' => $ventas_hoy,
            'ventas_semana' => $ventas_semana,
            'ventas_mes' => $ventas_mes,
            'ventas_ultimos_dias' => array_slice($ventas_data, -7), // Solo últimos 7 para el gráfico inicial
            'productos_mas_vendidos' => $this->ventaModel->getProductosMasVendidos($sucursal_id, 5),
            'lotes_por_vencer' => $loteModel->getPorVencer($sucursal_id, 30) // Próximos 30 días
        ];

        require_once __DIR__ . '/../Views/dashboard/index.php';
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

    public function getVentasChartData()
    {
        AuthMiddleware::check();
        header('Content-Type: application/json');

        $user = AuthMiddleware::getUser();
        $sucursal_id = $user['sucursal_id'];
        $dias = $_GET['dias'] ?? 7;

        $ventas = $this->ventaModel->getVentasUltimosDias($sucursal_id, $dias);

        echo json_encode([
            'success' => true,
            'data' => $ventas
        ]);
        exit;
    }
}
