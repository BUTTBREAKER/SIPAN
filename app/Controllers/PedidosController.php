<?php

namespace App\Controllers;

use App\Models\Pedido;
use App\Models\Cliente;
use App\Models\Producto;
use App\Middlewares\AuthMiddleware;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

class PedidosController implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    private $pedidoModel;
    private $clienteModel;
    private $productoModel;

    public function __construct()
    {
        require_once __DIR__ . '/../Helpers/CSRF.php';
        $this->pedidoModel = new Pedido();
        $this->clienteModel = new Cliente();
        $this->productoModel = new Producto();
    }

    public function index()
    {
        AuthMiddleware::check();

        $user = AuthMiddleware::getUser();
        $sucursal_id = $user['sucursal_id'];

        $pedidos = $this->pedidoModel->getWithDetails($sucursal_id);

        require_once __DIR__ . '/../Views/pedidos/index.php';
    }

    public function create()
    {
        AuthMiddleware::checkRole(['administrador', 'cajero', 'empleado']);

        $user = AuthMiddleware::getUser();
        $sucursal_id = $user['sucursal_id'];

        $clientes = $this->clienteModel->all($sucursal_id);
        $productos = $this->productoModel->all($sucursal_id);

        require_once __DIR__ . '/../Views/pedidos/create.php';
    }

    public function store()
    {
        AuthMiddleware::checkRole(['administrador', 'cajero', 'empleado']);

        header('Content-Type: application/json');

        // Validar CSRF
        if (!\App\Helpers\CSRF::validateToken($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '')) {
            echo json_encode(['success' => false, 'message' => 'Token de seguridad inválido']);
            exit;
        }

        $user = AuthMiddleware::getUser();
        $sucursal_id = $user['sucursal_id'];


        $pedido_data = [
            'id_cliente' => $_POST['id_cliente'] ?? 0,
            'id_repartidor' => !empty($_POST['id_repartidor']) ? $_POST['id_repartidor'] : null,
            'id_sucursal' => $sucursal_id,
            'id_usuario' => $user['id'],
            'fecha_entrega' => $_POST['fecha_entrega'] ?? null,
            'estado_pedido' => $_POST['estado_pedido'] ?? 'pendiente',
            'estado_pago' => 'pendiente',
            'subtotal' => $_POST['subtotal'] ?? 0,
            'descuento' => $_POST['descuento'] ?? 0,
            'total' => $_POST['total'] ?? 0,
            'monto_pagado' => 0,
            'monto_deuda' => $_POST['total'] ?? 0,
            'observaciones' => $_POST['observaciones'] ?? ''
        ];


        $productos = json_decode($_POST['productos'] ?? '[]', true);

        if (empty($productos)) {
            echo json_encode(['success' => false, 'message' => 'Debe agregar al menos un producto']);
            exit;
        }

        try {
            $pedido_id = $this->pedidoModel->createWithProducts($pedido_data, $productos);
            echo json_encode(['success' => true, 'message' => 'Pedido creado correctamente', 'id' => $pedido_id]);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error al crear pedido: ' . $e->getMessage()]);
        }
        exit;
    }

    public function show($id)
    {
        AuthMiddleware::check();

        $pedido = $this->pedidoModel->find($id);

        if (!$pedido) {
            header('Location: /pedidos');
            exit;
        }

        $cliente = $this->clienteModel->find($pedido['id_cliente']);
        $productos = $this->pedidoModel->getProductos($id);
        $pagos = $this->pedidoModel->getPagos($id);

        // Obtener repartidores de la sucursal
        require_once __DIR__ . '/../Models/Usuario.php';
        $usuarioModel = new \App\Models\Usuario();
        $sucursal_id = $_SESSION['sucursal_id'] ?? $pedido['id_sucursal'];

        $repartidores = $usuarioModel->getRepartidoresBySucursal($sucursal_id);

        require_once __DIR__ . '/../Views/pedidos/show.php';
    }

    public function update($id)
    {
        AuthMiddleware::checkRole(['administrador', 'empleado']);

        header('Content-Type: application/json');

        // Validar CSRF
        if (!\App\Helpers\CSRF::validateToken($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '')) {
            echo json_encode(['success' => false, 'message' => 'Token de seguridad inválido']);
            exit;
        }

        $data = [
            'estado_pedido' => $_POST['estado_pedido'] ?? 'pendiente',  // Cambiado a 'estado_pedido' para consistencia
            'fecha_entrega' => $_POST['fecha_entrega'] ?? null,
            'observaciones' => $_POST['observaciones'] ?? ''
        ];

        try {
            // Si el nuevo estado es 'completado' y no hay fecha_entrega, setearla a ahora
            $pedido = $this->pedidoModel->find($id);
            if ($data['estado_pedido'] === 'completado' && empty($pedido['fecha_entrega'])) {
                $data['fecha_entrega'] = date('Y-m-d H:i:s');  // Fecha y hora actual
            }

            $this->pedidoModel->update($id, $data);
            echo json_encode(['success' => true, 'message' => 'Pedido actualizado correctamente']);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error al actualizar pedido: ' . $e->getMessage()]);
        }
        exit;
    }

    public function registrarPago()
    {
        // Forzar que siempre devuelva JSON
        header('Content-Type: application/json; charset=utf-8');

        // Validar CSRF
        if (!\App\Helpers\CSRF::validateToken($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '')) {
            echo json_encode(['success' => false, 'message' => 'Token de seguridad inválido']);
            exit;
        }

        // Log inicial
        $this->logger?->debug('=== REGISTRAR PAGO INICIADO ===');
        $this->logger?->debug('Método HTTP: {method}', ['method' => $_SERVER['REQUEST_METHOD']]);

        $this->logger?->debug('Content-Type recibido: {content_type}', [
            'content_type' => $_SERVER['CONTENT_TYPE'] ?? 'no definido'
        ]);

        $this->logger?->debug('POST data: {data}', ['data' => print_r($_POST, true)]);

        try {
            AuthMiddleware::checkRole(['administrador', 'cajero', 'empleado']);

            $user = AuthMiddleware::getUser();

            if (!$user) {
                $this->logger?->error('Usuario no autenticado');
                echo json_encode(['success' => false, 'message' => 'Usuario no autenticado']);
                exit;
            }

            $this->logger?->info('Usuario autenticado: ID={id}', ['id' => $user['id']]);

            $pedido_id = $_POST['pedido_id'] ?? 0;
            $monto = (float) ($_POST['monto'] ?? 0);
            $metodo_pago = $_POST['metodo_pago'] ?? 'efectivo';
            $referencia = $_POST['referencia'] ?? null;
            $observaciones = $_POST['observaciones'] ?? null;

            $this->logger?->debug('Parámetros procesados:');
            $this->logger?->debug('- pedido_id: {id}', ['id' => $pedido_id]);
            $this->logger?->debug('- monto: {amount}', ['amount' => $monto]);
            $this->logger?->debug('- metodo_pago: {method}', ['method' => $metodo_pago]);

            if (!$pedido_id || $monto <= 0) {
                $this->logger?->error('Validación fallida - pedido_id o monto inválidos');
                echo json_encode([
                    'success' => false,
                    'message' => 'Datos incompletos o inválidos',
                    'debug' => [
                        'pedido_id' => $pedido_id,
                        'monto' => $monto
                    ]
                ]);
                exit;
            }

            // Registrar el pago
            $this->logger?->debug('Llamando a registrarPago del modelo...');
            $this->pedidoModel->registrarPago($pedido_id, $monto, $metodo_pago, $user['id'], $referencia, $observaciones);
            $this->logger?->debug('✓ Pago registrado en BD');

            // Actualizar el pedido
            $this->logger?->debug('Buscando pedido...');
            $pedido = $this->pedidoModel->find($pedido_id);

            if (!$pedido) {
                $this->logger?->error('Pedido no encontrado después de registrar pago');
                throw new \Exception('Pedido no encontrado');
            }

            $this->logger?->debug('Pedido encontrado. Total: {total}, Pagado: {paid}', [
                'total' => $pedido['total'],
                'paid' => $pedido['monto_pagado'],
            ]);

            $new_monto_pagado = $pedido['monto_pagado'] + $monto;
            $new_monto_deuda = $pedido['total'] - $new_monto_pagado;
            $new_estado_pago = ($new_monto_deuda <= 0) ? 'pagado' : ($new_monto_pagado > 0 ? 'abonado' : 'pendiente');

            $this->logger?->debug('Nuevos valores calculados:');
            $this->logger?->debug('- new_monto_pagado: {new_paid_amount}', ['new_paid_amount' => $new_monto_pagado]);
            $this->logger?->debug('- new_monto_deuda: {new_due_amount}', ['new_due_amount' => $new_monto_deuda]);
            $this->logger?->debug('- new_estado_pago: {new_paid_status}', ['new_paid_status' => $new_estado_pago]);

            $update_data = [
                'monto_pagado' => $new_monto_pagado,
                'monto_deuda' => $new_monto_deuda,
                'estado_pago' => $new_estado_pago
            ];

            $this->logger?->debug('Actualizando pedido...');
            $this->pedidoModel->update($pedido_id, $update_data);
            $this->logger?->debug('✓ Pedido actualizado');
            $this->logger?->debug('=== PAGO REGISTRADO EXITOSAMENTE ===');

            echo json_encode([
                'success' => true,
                'message' => 'Pago registrado correctamente',
                'data' => [
                    'nuevo_monto_pagado' => $new_monto_pagado,
                    'nueva_deuda' => $new_monto_deuda,
                    'nuevo_estado' => $new_estado_pago
                ]
            ]);
        } catch (\Exception $e) {
            $this->logger?->error('=== ERROR EXCEPTION ===');
            $this->logger?->error('Mensaje: {message}', ['message' => $e->getMessage()]);
            $this->logger?->error('Archivo: {file}', ['file' => $e->getFile()]);
            $this->logger?->error('Línea: {line}', ['line' => $e->getLine()]);
            $this->logger?->error('Trace: {trace}', ['trace' => $e->getTraceAsString()]);

            echo json_encode([
                'success' => false,
                'message' => 'Error al registrar pago: ' . $e->getMessage(),
                'debug' => [
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]
            ]);
        }

        exit;
    }

    // ==========================================
    // MÉTODOS PARA REPARTIDORES (SIPAN DELIVERY)
    // ==========================================

    public function asignarRepartidor($id)
    {
        AuthMiddleware::checkRole(['administrador', 'empleado']);

        header('Content-Type: application/json');

        // Validar CSRF
        if (!\App\Helpers\CSRF::validateToken($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '')) {
            echo json_encode(['success' => false, 'message' => 'Token de seguridad inválido']);
            exit;
        }

        $id_repartidor = $_POST['id_repartidor'] ?? null;

        try {
            // Actualizar solo el id_repartidor
            $this->pedidoModel->update($id, ['id_repartidor' => $id_repartidor]);

            echo json_encode(['success' => true, 'message' => 'Repartidor asignado correctamente']);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error al asignar repartidor: ' . $e->getMessage()]);
        }
        exit;
    }
}
