<?php

namespace Delivery\Controllers;

use App\Models\Pedido;
use Delivery\Middleware\AuthMiddleware;

class PedidosController
{
    private $pedidoModel;

    public function __construct()
    {
        require_once __DIR__ . '/../../app/Helpers/CSRF.php';
        $this->pedidoModel = new Pedido();
    }

    public function dashboard()
    {
        AuthMiddleware::check();

        $sucursal_id = $_SESSION['sucursal_id'];

        // Bolt Optimization: Fetch only active orders (pending, processing, en route)
        // This avoids loading the entire branch history into memory.
        $activeStatuses = ['pendiente', 'en_proceso', 'en_camino'];
        $pedidos = $this->pedidoModel->getWithDetails($sucursal_id, $activeStatuses);
        
        // Fetch counts separately to update all tabs correctly
        $counts = $this->pedidoModel->getCountsBySucursal($sucursal_id);
        
        $total_pedidos = array_sum($counts);
        $total_pendientes = ($counts['pendiente'] ?? 0) + ($counts['en_proceso'] ?? 0);
        $total_en_camino = $counts['en_camino'] ?? 0;

        // Compatibility for view: split active orders into categories
        $pendientes = [];
        $en_camino = [];
        
        foreach ($pedidos as $p) {
            if ($p['estado_pedido'] === 'en_proceso' || $p['estado_pedido'] === 'pendiente') {
                $pendientes[] = $p;
            } elseif ($p['estado_pedido'] === 'en_camino') {
                $en_camino[] = $p;
            }
        }

        require_once __DIR__ . '/../views/dashboard.php';
    }

    /**
     * API endpoint para auto-refresh del dashboard (devuelve JSON con conteos)
     */
    public function apiDashboard()
    {
        AuthMiddleware::check();
        header('Content-Type: application/json');

        $sucursal_id = $_SESSION['sucursal_id'];
        
        // Bolt Optimization: Use efficient status counts instead of fetching all records
        $counts = $this->pedidoModel->getCountsBySucursal($sucursal_id);

        $total = array_sum($counts);
        $pendientes = ($counts['pendiente'] ?? 0) + ($counts['en_proceso'] ?? 0);
        $en_camino = $counts['en_camino'] ?? 0;

        echo json_encode([
            'total'      => $total,
            'pendientes' => $pendientes,
            'en_camino'  => $en_camino,
            'timestamp'  => date('H:i:s')
        ]);
        exit;
    }

    public function show($id)
    {
        AuthMiddleware::check();

        $pedido = $this->pedidoModel->find($id);

        if (!$pedido) {
            header('Location: /delivery/dashboard');
            exit;
        }

        // Seguridad: Verificar que el pedido pertenece al repartidor actual
        if ($pedido['id_repartidor'] != $_SESSION['user_id'] && $_SESSION['user_rol'] !== 'administrador') {
            header('Location: /delivery/dashboard?error=unauthorized');
            exit;
        }

        require_once __DIR__ . '/../../app/Models/Cliente.php';
        $clienteModel = new \App\Models\Cliente();
        $cliente = $clienteModel->find($pedido['id_cliente']);
        
        $productos = $this->pedidoModel->getProductos($id);
        $pagos = $this->pedidoModel->getPagos($id);

        require_once __DIR__ . '/../views/show.php';
    }

    public function updateEstado($id)
    {
        AuthMiddleware::check();
        
        header('Content-Type: application/json');

        if (!\App\Helpers\CSRF::validateToken($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '')) {
            echo json_encode(['success' => false, 'message' => 'Token de seguridad inválido']);
            exit;
        }
        
        $pedido = $this->pedidoModel->find($id);

        if (!$pedido) {
            echo json_encode(['success' => false, 'message' => 'Pedido no encontrado']);
            exit;
        }

        // Seguridad: Verificar que el pedido pertenece a la misma sucursal
        if ($pedido['id_sucursal'] != $_SESSION['sucursal_id'] && $_SESSION['user_rol'] !== 'administrador') {
            echo json_encode(['success' => false, 'message' => 'Acceso no autorizado a este pedido']);
            exit;
        }

        $nuevo_estado = $_POST['estado'] ?? '';
        $observaciones = $_POST['observaciones'] ?? null;
        
        $estados_validos = ['en_camino', 'entregado', 'no_entregado'];
        
        if (!in_array($nuevo_estado, $estados_validos)) {
            echo json_encode(['success' => false, 'message' => 'Estado inválido']);
            exit;
        }
        
        try {
            // Si el repartidor cambia el estado a 'en_camino', se lo asignamos temporalmente para control
            if ($nuevo_estado === 'en_camino' && empty($pedido['id_repartidor'])) {
                $this->pedidoModel->update($id, ['id_repartidor' => $_SESSION['user_id']]);
            }

            $this->pedidoModel->updateEstadoEntrega($id, $nuevo_estado, $observaciones);
            echo json_encode(['success' => true, 'message' => 'Estado actualizado correctamente']);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error al actualizar: ' . $e->getMessage()]);
        }
        exit;
    }

    /**
     * Registrar cobro del repartidor al cliente
     */
    public function registrarCobro($id)
    {
        AuthMiddleware::check();
        header('Content-Type: application/json');

        if (!\App\Helpers\CSRF::validateToken($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '')) {
            echo json_encode(['success' => false, 'message' => 'Token de seguridad inválido']);
            exit;
        }

        $pedido = $this->pedidoModel->find($id);

        if (!$pedido) {
            echo json_encode(['success' => false, 'message' => 'Pedido no encontrado']);
            exit;
        }

        if ($pedido['id_sucursal'] != $_SESSION['sucursal_id'] && $_SESSION['user_rol'] !== 'administrador') {
            echo json_encode(['success' => false, 'message' => 'Acceso no autorizado']);
            exit;
        }

        $monto = floatval($_POST['monto'] ?? 0);
        $metodo = $_POST['metodo_pago'] ?? 'efectivo';

        if ($monto <= 0) {
            echo json_encode(['success' => false, 'message' => 'El monto debe ser mayor a 0']);
            exit;
        }

        $metodos_validos = ['efectivo', 'yape', 'plin', 'transferencia', 'tarjeta', 'otro'];
        if (!in_array($metodo, $metodos_validos)) {
            echo json_encode(['success' => false, 'message' => 'Método de pago inválido']);
            exit;
        }

        // Validar que no se cobre más de la deuda
        $deuda_actual = floatval($pedido['monto_deuda'] ?? 0);
        if ($monto > $deuda_actual + 0.01) { // +0.01 para evitar problemas de precisión
            echo json_encode(['success' => false, 'message' => 'El monto excede la deuda pendiente (' . number_format($deuda_actual, 2) . ')']);
            exit;
        }

        try {
            require_once __DIR__ . '/../../app/Core/Database.php';
            $db = \App\Core\Database::getInstance();
            $db->beginTransaction();

            // 1. Registrar el pago
            $this->pedidoModel->registrarPago(
                $id,
                $monto,
                $metodo,
                $_SESSION['user_id'],
                null, // referencia
                'Cobro en campo por repartidor'
            );

            // 2. Actualizar montos del pedido
            $nuevo_pagado = floatval($pedido['monto_pagado']) + $monto;
            $nueva_deuda  = floatval($pedido['total']) - $nuevo_pagado;
            if ($nueva_deuda < 0) $nueva_deuda = 0;

            $nuevo_estado_pago = 'pendiente';
            if ($nueva_deuda <= 0.01) {
                $nuevo_estado_pago = 'pagado';
            } elseif ($nuevo_pagado > 0) {
                $nuevo_estado_pago = 'abonado';
            }

            $db->execute(
                "UPDATE pedidos SET monto_pagado = ?, monto_deuda = ?, estado_pago = ? WHERE id = ?",
                [$nuevo_pagado, $nueva_deuda, $nuevo_estado_pago, $id]
            );

            $db->commit();

            $moneda = htmlspecialchars($_ENV['moneda_principal'] ?? 'S/');
            echo json_encode([
                'success' => true,
                'message' => 'Cobro de ' . $moneda . number_format($monto, 2) . ' registrado exitosamente',
                'nuevo_estado_pago' => $nuevo_estado_pago,
                'monto_pagado' => $nuevo_pagado,
                'monto_deuda' => $nueva_deuda,
            ]);
        } catch (\Exception $e) {
            if (isset($db)) $db->rollback();
            echo json_encode(['success' => false, 'message' => 'Error al registrar cobro: ' . $e->getMessage()]);
        }
        exit;
    }

    public function historial()
    {
        AuthMiddleware::check();

        $user_id = $_SESSION['user_id'];
        
        // Filtros de fecha
        $fecha_desde = $_GET['desde'] ?? date('Y-m-01'); // Primer día del mes
        $fecha_hasta = $_GET['hasta'] ?? date('Y-m-d');

        require_once __DIR__ . '/../../app/Core/Database.php';
        $db = \App\Core\Database::getInstance();

        // Bolt Optimization: SARGable date comparison
        $sql = "SELECT p.*, c.nombre as cliente_nombre, c.apellido as cliente_apellido, 
                       c.direccion as cliente_direccion 
                FROM pedidos p
                INNER JOIN clientes c ON p.id_cliente = c.id
                WHERE p.id_repartidor = ? 
                  AND p.estado_pedido IN ('entregado', 'completado')
                  AND p.fecha_pedido >= ? AND p.fecha_pedido <= ?
                ORDER BY p.fecha_pedido DESC 
                LIMIT 100";
                
        $pedidos = $db->fetchAll($sql, [$user_id, $fecha_desde . ' 00:00:00', $fecha_hasta . ' 23:59:59']);

        // Resumen del período
        // Bolt Optimization: SARGable date comparison
        $sql_resumen = "SELECT COUNT(*) as total_entregas, 
                               COALESCE(SUM(p.total), 0) as total_monto,
                               COALESCE(SUM(p.monto_pagado), 0) as total_cobrado
                        FROM pedidos p
                        WHERE p.id_repartidor = ? 
                          AND p.estado_pedido IN ('entregado', 'completado')
                          AND p.fecha_pedido >= ? AND p.fecha_pedido <= ?";
        $resumen = $db->fetchOne($sql_resumen, [$user_id, $fecha_desde . ' 00:00:00', $fecha_hasta . ' 23:59:59']);

        require_once __DIR__ . '/../views/historial.php';
    }

    /**
     * Estadísticas del repartidor
     */
    public function estadisticas()
    {
        AuthMiddleware::check();

        $user_id = $_SESSION['user_id'];
        
        require_once __DIR__ . '/../../app/Core/Database.php';
        $db = \App\Core\Database::getInstance();

        // — Entregas de HOY —
        // Bolt Optimization: SARGable comparison
        $hoy = $db->fetchOne(
            "SELECT COUNT(*) as entregas, COALESCE(SUM(total), 0) as monto, COALESCE(SUM(monto_pagado), 0) as cobrado
             FROM pedidos WHERE id_repartidor = ? AND estado_pedido = 'entregado' AND fecha_entrega >= CURDATE()",
            [$user_id]
        );

        // — Esta semana —
        // Bolt Optimization: SARGable range comparison
        $startOfWeek = date('Y-m-d', strtotime('monday this week')) . ' 00:00:00';
        $semana = $db->fetchOne(
            "SELECT COUNT(*) as entregas, COALESCE(SUM(total), 0) as monto, COALESCE(SUM(monto_pagado), 0) as cobrado
             FROM pedidos WHERE id_repartidor = ? AND estado_pedido = 'entregado' 
             AND fecha_entrega >= ?",
            [$user_id, $startOfWeek]
        );

        // — Este mes —
        // Bolt Optimization: SARGable range comparison
        $startOfMonth = date('Y-m-01') . ' 00:00:00';
        $mes = $db->fetchOne(
            "SELECT COUNT(*) as entregas, COALESCE(SUM(total), 0) as monto, COALESCE(SUM(monto_pagado), 0) as cobrado
             FROM pedidos WHERE id_repartidor = ? AND estado_pedido = 'entregado' 
             AND fecha_entrega >= ?",
            [$user_id, $startOfMonth]
        );

        // — Pedidos activos ahora —
        $activos = $db->fetchOne(
            "SELECT COUNT(*) as total,
                    SUM(CASE WHEN estado_pedido IN ('pendiente','en_proceso') THEN 1 ELSE 0 END) as pendientes,
                    SUM(CASE WHEN estado_pedido = 'en_camino' THEN 1 ELSE 0 END) as en_camino
             FROM pedidos WHERE id_repartidor = ? AND estado_pedido NOT IN ('entregado','completado','cancelado','no_entregado')",
            [$user_id]
        );

        // — Últimos 7 días para gráfico —
        // Bolt Optimization: Use grouped index if possible, DATE() in GROUP BY is often unavoidable for reports
        // but we keep WHERE SARGable.
        $ultimos7 = $db->fetchAll(
            "SELECT DATE(fecha_entrega) as dia, COUNT(*) as entregas, COALESCE(SUM(total), 0) as monto
             FROM pedidos WHERE id_repartidor = ? AND estado_pedido = 'entregado' 
             AND fecha_entrega >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
             GROUP BY dia ORDER BY dia ASC",
            [$user_id]
        );

        // — Ratio de éxito (entregados vs no_entregados este mes) —
        // Bolt Optimization: SARGable range comparison
        $ratio_data = $db->fetchOne(
            "SELECT 
                SUM(CASE WHEN estado_pedido = 'entregado' THEN 1 ELSE 0 END) as exitosos,
                SUM(CASE WHEN estado_pedido = 'no_entregado' THEN 1 ELSE 0 END) as fallidos
             FROM pedidos WHERE id_repartidor = ? 
             AND fecha_pedido >= ?",
            [$user_id, $startOfMonth]
        );
        $total_ratio = ($ratio_data['exitosos'] ?? 0) + ($ratio_data['fallidos'] ?? 0);
        $ratio = $total_ratio > 0 ? round(($ratio_data['exitosos'] / $total_ratio) * 100) : 100;

        $stats = [
            'hoy'     => $hoy,
            'semana'  => $semana,
            'mes'     => $mes,
            'activos' => $activos,
            'chart'   => $ultimos7,
            'ratio'   => $ratio,
        ];

        require_once __DIR__ . '/../views/estadisticas.php';
    }
}
