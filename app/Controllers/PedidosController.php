<?php

namespace App\Controllers;

use App\Models\Pedido;
use App\Models\Cliente;
use App\Models\Producto;
use App\Middlewares\AuthMiddleware;

class PedidosController
{
    private $pedidoModel;
    private $clienteModel;
    private $productoModel;

    public function __construct()
    {
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

        $user = AuthMiddleware::getUser();
        $sucursal_id = $user['sucursal_id'];


        $pedido_data = [
            'id_cliente' => $_POST['id_cliente'] ?? 0,
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

        require_once __DIR__ . '/../Views/pedidos/show.php';
    }

    public function update($id)
    {
        AuthMiddleware::checkRole(['administrador', 'empleado']);

        header('Content-Type: application/json');

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

        // Log inicial
        error_log("=== REGISTRAR PAGO INICIADO ===");
        error_log("Método HTTP: " . $_SERVER['REQUEST_METHOD']);
        error_log("Content-Type recibido: " . ($_SERVER['CONTENT_TYPE'] ?? 'no definido'));
        error_log("POST data: " . print_r($_POST, true));

        try {
            AuthMiddleware::checkRole(['administrador', 'cajero', 'empleado']);

            $user = AuthMiddleware::getUser();

            if (!$user) {
                error_log("ERROR: Usuario no autenticado");
                echo json_encode(['success' => false, 'message' => 'Usuario no autenticado']);
                exit;
            }

            error_log("Usuario autenticado: ID=" . $user['id']);

            $pedido_id = $_POST['pedido_id'] ?? 0;
            $monto = (float) ($_POST['monto'] ?? 0);
            $metodo_pago = $_POST['metodo_pago'] ?? 'efectivo';
            $referencia = $_POST['referencia'] ?? null;
            $observaciones = $_POST['observaciones'] ?? null;

            error_log("Parámetros procesados:");
            error_log("- pedido_id: $pedido_id");
            error_log("- monto: $monto");
            error_log("- metodo_pago: $metodo_pago");

            if (!$pedido_id || $monto <= 0) {
                error_log("ERROR: Validación fallida - pedido_id o monto inválidos");
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
            error_log("Llamando a registrarPago del modelo...");
            $this->pedidoModel->registrarPago($pedido_id, $monto, $metodo_pago, $user['id'], $referencia, $observaciones);
            error_log("✓ Pago registrado en BD");

            // Actualizar el pedido
            error_log("Buscando pedido...");
            $pedido = $this->pedidoModel->find($pedido_id);

            if (!$pedido) {
                error_log("ERROR: Pedido no encontrado después de registrar pago");
                throw new \Exception('Pedido no encontrado');
            }

            error_log("Pedido encontrado. Total: {$pedido['total']}, Pagado: {$pedido['monto_pagado']}");

            $new_monto_pagado = $pedido['monto_pagado'] + $monto;
            $new_monto_deuda = $pedido['total'] - $new_monto_pagado;
            $new_estado_pago = ($new_monto_deuda <= 0) ? 'pagado' : ($new_monto_pagado > 0 ? 'abonado' : 'pendiente');

            error_log("Nuevos valores calculados:");
            error_log("- new_monto_pagado: $new_monto_pagado");
            error_log("- new_monto_deuda: $new_monto_deuda");
            error_log("- new_estado_pago: $new_estado_pago");

            $update_data = [
                'monto_pagado' => $new_monto_pagado,
                'monto_deuda' => $new_monto_deuda,
                'estado_pago' => $new_estado_pago
            ];

            error_log("Actualizando pedido...");
            $this->pedidoModel->update($pedido_id, $update_data);
            error_log("✓ Pedido actualizado");

            error_log("=== PAGO REGISTRADO EXITOSAMENTE ===");

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
            error_log("=== ERROR EXCEPTION ===");
            error_log("Mensaje: " . $e->getMessage());
            error_log("Archivo: " . $e->getFile());
            error_log("Línea: " . $e->getLine());
            error_log("Trace: " . $e->getTraceAsString());

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
}
