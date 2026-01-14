<?php

namespace App\Controllers;

use App\Models\Venta;
use App\Models\Producto;
use App\Models\Negocio;
use App\Middlewares\AuthMiddleware;

class VentasController
{
    private $ventaModel;
    private $productoModel;
    private $negocioModel;

    public function __construct()
    {
        $this->ventaModel = new Venta();
        $this->productoModel = new Producto();
        $this->negocioModel = new Negocio();
    }

    public function index()
    {
        AuthMiddleware::check();

        $user = AuthMiddleware::getUser();
        $sucursal_id = $user['sucursal_id'];

        $ventas = $this->ventaModel->getWithDetails($sucursal_id);

        require_once __DIR__ . '/../Views/ventas/index.php';
    }

    public function create()
    {
        ob_start();
        AuthMiddleware::checkRole(['administrador', 'cajero', 'empleado']);

        ob_clean();
        $user = AuthMiddleware::getUser();
        $sucursal_id = $user['sucursal_id'];

        $productos = $this->productoModel->all($sucursal_id);

        // Obtener tasa BCV
        $configModel = new \App\Models\Configuracion();
        $tasa_bcv = $configModel->getTasaBCV();

        require_once __DIR__ . '/../Views/ventas/create.php';
    }

    public function store()
    {
        ob_start(); // Prevent accidental output
        AuthMiddleware::checkRole(['administrador', 'cajero', 'empleado']);

        // Clean any previous output (warnings, spaces before php tag)
        ob_clean();
        header('Content-Type: application/json');

        try {
            $user = AuthMiddleware::getUser();
            $sucursal_id = $user['sucursal_id'];

            $negocio = $this->negocioModel->getBySucursal($sucursal_id);

            if (!$negocio) {
                echo json_encode(['success' => false, 'message' => 'No se encontró negocio para esta sucursal']);
                exit;
            }

            // Obtener datos del POST
            $id_cliente = !empty($_POST['id_cliente']) ? intval($_POST['id_cliente']) : null;
            $metodo_pago = $_POST['metodo_pago'] ?? '';
            $total = floatval($_POST['total'] ?? 0);
            $productos_raw = json_decode($_POST['productos'] ?? '[]', true);
            $productos = [];

            // Normalizar datos para el modelo
            foreach ($productos_raw as $p) {
                $productos[] = [
                    'id_producto' => $p['id'],
                    'cantidad' => $p['cantidad'],
                    'precio_unitario' => $p['precio'],
                    'subtotal' => $p['cantidad'] * $p['precio']
                ];
            }

            // Log para debug
            error_log("=== INICIO PROCESO VENTA ===");
            error_log("POST count: " . count($_POST));
            error_log("POST raw: " . print_r($_POST, true));

            // Validaciones
            if (empty($productos)) {
                error_log("Error: Sin productos");
                echo json_encode(['success' => false, 'message' => 'Debe agregar al menos un producto']);
                exit;
            }

            $pagos = json_decode($_POST['pagos'] ?? '[]', true);
            error_log("Pagos decoding: " . print_r($pagos, true));

            // Si vienen pagos, validar que sumen el total
            $totalPagado = 0;
            if (!empty($pagos)) {
                foreach ($pagos as $p) {
                    $totalPagado += (float)$p['monto'];
                }
                error_log("Total pagado calc: $totalPagado vs Total req: $total");

                // Permitir pequeña diferencia por redondeo
                if (abs($total - $totalPagado) > 0.05) {
                    error_log("Error: Diferencia montos. Total: $total, Pagado: $totalPagado");
                    echo json_encode(['success' => false, 'message' => 'El total de los pagos no coincide con el total de la venta']);
                    exit;
                }
                $metodo_pago = 'mixto';
                error_log("Metodo set to 'mixto'");
            } else {
                if (empty($metodo_pago)) {
                    error_log("Error: Metodo pago vacio");
                    echo json_encode(['success' => false, 'message' => 'Debe seleccionar un método de pago']);
                    exit;
                }
            }

            // Preparar datos de la venta CON id_cliente
            $venta_data = [
                'id_negocio' => $negocio['id'],
                'id_sucursal' => $sucursal_id,
                'id_usuario' => $user['id'],
                'id_cliente' => $id_cliente,
                'total' => $total,
                'metodo_pago' => $metodo_pago,
                'estado' => 'completada',
                'fecha_venta' => date('Y-m-d H:i:s')
            ];

            // Crear venta con productos y pagos
            $venta_id = $this->ventaModel->createWithProducts($venta_data, $productos, $pagos);

            // INTEGRACIÓN CAJA CHICA: Registrar ingreso si hay caja abierta
            $cajaModel = new \App\Models\Caja();
            $cajaActiva = $cajaModel->getActiva($sucursal_id);
            if ($cajaActiva) {
                $cajaModel->addMovimiento(
                    $cajaActiva['id'],
                    'ingreso',
                    $total,
                    "Venta #$venta_id",
                    $metodo_pago,
                    $venta_id
                );
            }

            echo json_encode([
                'success' => true,
                'message' => 'Venta registrada correctamente',
                'venta_id' => $venta_id
            ]);
        } catch (\Exception $e) {
            error_log("Error en venta: " . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Error al registrar venta: ' . $e->getMessage()
            ]);
        }
        exit;
    }

    public function show($id)
    {
        AuthMiddleware::check();

        $user = AuthMiddleware::getUser();
        $sucursal_id = $user['sucursal_id'];

        // Obtener venta básica
        $venta = $this->ventaModel->find($id);

        if (!$venta || $venta['id_sucursal'] != $sucursal_id) {
            header('Location: /ventas');
            exit;
        }

        // Obtener información adicional usando el modelo Venta con el nuevo método
        $ventaCompleta = $this->ventaModel->getVentaConDetalles($id, $sucursal_id);
        $venta = $ventaCompleta ?: $venta; // Usar la completa si existe

        // Limpiar cliente_nombre si está vacío
        if (isset($venta['cliente_nombre']) && trim($venta['cliente_nombre']) == '') {
            $venta['cliente_nombre'] = null;
        }

        // Obtener detalles de productos
        $detalles = $this->ventaModel->getProductos($id);

        // Obtener pagos
        $pagos = $this->ventaModel->getPagos($id);

        require_once __DIR__ . '/../Views/ventas/show.php';
    }

    public function ticket($id)
    {
        AuthMiddleware::check();

        $user = AuthMiddleware::getUser();
        $sucursal_id = $user['sucursal_id'];

        // Obtener venta básica primero
        $venta = $this->ventaModel->find($id);

        if (!$venta || $venta['id_sucursal'] != $sucursal_id) {
            header('Location: /ventas');
            exit;
        }

        // Obtener información completa
        $ventaCompleta = $this->ventaModel->getVentaConDetalles($id, $sucursal_id);
        $venta = $ventaCompleta ?: $venta;

        // Limpiar cliente_nombre si está vacío
        if (isset($venta['cliente_nombre']) && trim($venta['cliente_nombre']) == '') {
            $venta['cliente_nombre'] = null;
        }

        // Obtener productos de la venta - CAMBIAR $productos por $detalles
        $detalles = $this->ventaModel->getProductos($id);

        // Obtener pagos
        $pagos = $this->ventaModel->getPagos($id);

        // Separar datos para la vista
        $negocio = [
            'nombre' => $venta['negocio_nombre'] ?? 'SIPAN',
            'ruc' => $venta['negocio_ruc'] ?? '',
            'telefono' => $venta['negocio_telefono'] ?? ''
        ];

        $sucursal = [
            'nombre' => $venta['sucursal_nombre'] ?? 'Sucursal Principal',
            'direccion' => $venta['sucursal_direccion'] ?? '',
            'telefono' => $venta['sucursal_telefono'] ?? ''
        ];

        require_once __DIR__ . '/../Views/ventas/ticket.php';
    }
}
