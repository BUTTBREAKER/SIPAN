<?php

namespace SIPAN\Controllers;

use SIPAN\Models\Venta;
use SIPAN\Models\Producto;
use SIPAN\Models\Cliente;
use SIPAN\Models\Produccion;

class ReportesController
{
    private $ventaModel;
    private $productoModel;
    private $clienteModel;
    private $produccionModel;
    
    public function __construct()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
        
        $this->ventaModel = new Venta();
        $this->productoModel = new Producto();
        $this->clienteModel = new Cliente();
        $this->produccionModel = new Produccion();
    }
    
    public function index()
    {
        $data = [
            'pageTitle' => 'Reportes',
            'currentPage' => 'reportes'
        ];
        
        require_once __DIR__ . '/../Views/reportes/index.php';
    }
    
    public function ventas()
    {
        $fecha_inicio = $_GET['fecha_inicio'] ?? date('Y-m-01');
        $fecha_fin = $_GET['fecha_fin'] ?? date('Y-m-d');
        $formato = $_GET['formato'] ?? 'html';
        
        $ventas = $this->ventaModel->getByDateRange($_SESSION['sucursal_id'], $fecha_inicio, $fecha_fin);
        
        $total_ventas = array_sum(array_column($ventas, 'total'));
        $cantidad_ventas = count($ventas);
        $promedio = $cantidad_ventas > 0 ? $total_ventas / $cantidad_ventas : 0;
        
        $data = [
            'ventas' => $ventas,
            'fecha_inicio' => $fecha_inicio,
            'fecha_fin' => $fecha_fin,
            'total_ventas' => $total_ventas,
            'cantidad_ventas' => $cantidad_ventas,
            'promedio' => $promedio
        ];
        
        if ($formato === 'pdf') {
            $this->generarPDFVentas($data);
        } else {
            $data['pageTitle'] = 'Reporte de Ventas';
            $data['currentPage'] = 'reportes';
            require_once __DIR__ . '/../Views/reportes/ventas.php';
        }
    }
    
    public function productos()
    {
        $productos = $this->productoModel->getBySucursal($_SESSION['sucursal_id']);
        $formato = $_GET['formato'] ?? 'html';
        
        // Calcular valor del inventario
        $valor_total = 0;
        foreach ($productos as &$producto) {
            $producto['valor_stock'] = $producto['stock_actual'] * $producto['precio_actual'];
            $valor_total += $producto['valor_stock'];
        }
        
        $data = [
            'productos' => $productos,
            'valor_total' => $valor_total
        ];
        
        if ($formato === 'pdf') {
            $this->generarPDFProductos($data);
        } else {
            $data['pageTitle'] = 'Reporte de Productos';
            $data['currentPage'] = 'reportes';
            require_once __DIR__ . '/../Views/reportes/productos.php';
        }
    }
    
    public function clientes()
    {
        $clientes = $this->clienteModel->getBySucursal($_SESSION['sucursal_id']);
        $formato = $_GET['formato'] ?? 'html';
        
        // Obtener estadísticas de cada cliente
        foreach ($clientes as &$cliente) {
            $stats = $this->ventaModel->getClienteStats($cliente['id']);
            $cliente['total_compras'] = $stats['total_compras'] ?? 0;
            $cliente['monto_total'] = $stats['monto_total'] ?? 0;
        }
        
        $data = [
            'clientes' => $clientes
        ];
        
        if ($formato === 'pdf') {
            $this->generarPDFClientes($data);
        } else {
            $data['pageTitle'] = 'Reporte de Clientes';
            $data['currentPage'] = 'reportes';
            require_once __DIR__ . '/../Views/reportes/clientes.php';
        }
    }
    
    private function generarPDFVentas($data)
    {
        require_once __DIR__ . '/../../vendor/autoload.php';
        
        $html = $this->getHTMLVentas($data);
        
        $mpdf = new \Mpdf\Mpdf([
            'format' => 'A4',
            'margin_top' => 10,
            'margin_bottom' => 10,
            'margin_left' => 10,
            'margin_right' => 10
        ]);
        
        $mpdf->WriteHTML($html);
        $mpdf->Output('reporte_ventas_' . date('Y-m-d') . '.pdf', 'D');
    }
    
    private function getHTMLVentas($data)
    {
        ob_start();
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                body { font-family: Arial, sans-serif; font-size: 12px; }
                h1 { text-align: center; color: #8B6F47; }
                table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                th { background-color: #D4A574; color: white; }
                .totales { background-color: #f9f9f9; font-weight: bold; }
            </style>
        </head>
        <body>
            <h1>Reporte de Ventas</h1>
            <p><strong>Período:</strong> <?= date('d/m/Y', strtotime($data['fecha_inicio'])) ?> - <?= date('d/m/Y', strtotime($data['fecha_fin'])) ?></p>
            
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Fecha</th>
                        <th>Cliente</th>
                        <th>Total</th>
                        <th>Método de Pago</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data['ventas'] as $venta): ?>
                    <tr>
                        <td>#<?= str_pad($venta['id'], 6, '0', STR_PAD_LEFT) ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($venta['fecha_venta'])) ?></td>
                        <td><?= htmlspecialchars($venta['cliente_nombre'] ?? 'Cliente General') ?></td>
                        <td>S/ <?= number_format($venta['total'], 2) ?></td>
                        <td><?= ucfirst($venta['metodo_pago']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr class="totales">
                        <td colspan="3">TOTALES</td>
                        <td>S/ <?= number_format($data['total_ventas'], 2) ?></td>
                        <td><?= $data['cantidad_ventas'] ?> ventas</td>
                    </tr>
                    <tr class="totales">
                        <td colspan="3">PROMEDIO POR VENTA</td>
                        <td colspan="2">S/ <?= number_format($data['promedio'], 2) ?></td>
                    </tr>
                </tfoot>
            </table>
            
            <p style="margin-top: 30px; text-align: center; color: #666;">
                Generado el <?= date('d/m/Y H:i') ?> - Sistema SIPAN
            </p>
        </body>
        </html>
        <?php
        return ob_get_clean();
    }
    
    private function generarPDFProductos($data)
    {
        // Similar implementation for products PDF
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'message' => 'Funcionalidad de PDF en desarrollo']);
    }
    
    private function generarPDFClientes($data)
    {
        // Similar implementation for clients PDF
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'message' => 'Funcionalidad de PDF en desarrollo']);
    }
}
