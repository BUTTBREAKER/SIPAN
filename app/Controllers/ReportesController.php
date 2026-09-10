<?php

namespace App\Controllers;

use App\Models\Venta;
use App\Models\Producto;
use App\Models\Cliente;
use App\Models\Produccion;
use App\Models\Insumo;
use App\Models\Pedido;
use App\Models\Lote;
use App\Models\Compra;
use App\Models\Proveedor;

class ReportesController
{
    private $ventaModel;
    private $productoModel;
    private $clienteModel;
    private $produccionModel;
    private $insumoModel;
    private $pedidoModel;
    private $loteModel;
    private $compraModel;
    private $proveedorModel;

    public function __construct()
    {
        $this->ventaModel = new Venta();
        $this->productoModel = new Producto();
        $this->clienteModel = new Cliente();
        $this->produccionModel = new Produccion();
        $this->insumoModel = new Insumo();
        $this->pedidoModel = new Pedido();
        $this->loteModel = new Lote();
        $this->compraModel = new Compra();
        $this->proveedorModel = new Proveedor();
    }

    public function index()
    {
        $data = [
            'pageTitle' => 'Reportes',
            'currentPage' => 'reportes'
        ];

        require_once dirname(__DIR__) . '/Views/reportes/index.php';
    }

    public function ventas()
    {
        $fecha_inicio = $_GET['fecha_inicio'] ?? date('Y-m-01');
        $fecha_fin = $_GET['fecha_fin'] ?? date('Y-m-d');
        $formato = $_GET['formato'] ?? 'html';

        $ventas = $this->ventaModel->getByDateRange($_SESSION['sucursal_id'], $fecha_inicio, $fecha_fin);
        $ventaIds = array_column($ventas, 'id');

        // Optimización Bolt: Batch fetch de pagos para evitar N+1 queries
        $pagos_agrupados = [];
        if (!empty($ventaIds)) {
            $todos_los_pagos = $this->ventaModel->getPagosPorVentas($ventaIds);
            foreach ($todos_los_pagos as $p) {
                $pagos_agrupados[$p['id_venta']][] = $p;
            }
        }

        $desglose_medios = [
            'efectivo_bs' => 0,
            'efectivo_usd' => 0,
            'pago_movil' => 0,
            'tarjeta' => 0,
            'transferencia' => 0,
            'zelle' => 0,
            'biopago' => 0
        ];

        $total_ventas = 0;

        foreach ($ventas as &$venta) {
            $total_ventas += $venta['total'];

            $pagos = $pagos_agrupados[$venta['id']] ?? [];

            if (!empty($pagos)) {
                // Sumar del detalle
                $lista_pagos = [];
                foreach ($pagos as $p) {
                    $m = $p['metodo_pago'];
                    $v = $p['monto'];
                    if (isset($desglose_medios[$m])) {
                        $desglose_medios[$m] += $v;
                    } else {
                        // Fallback por si hay metodo viejo
                        if (!isset($desglose_medios['otros'])) {
                            $desglose_medios['otros'] = 0;
                        }
                        $desglose_medios['otros'] += $v;
                    }
                    $lista_pagos[] = ucfirst(str_replace('_', ' ', $m)) . ': ' . number_format($v, 2);
                }
                $venta['detalle_pagos_str'] = implode('<br>', $lista_pagos);
            } else {
                // Usar el metodo principal (compatibilidad anterior)
                $m = $venta['metodo_pago'];
                if ($m !== 'mixto') {
                    if (isset($desglose_medios[$m])) {
                        $desglose_medios[$m] += $venta['total'];
                    }
                }
                $venta['detalle_pagos_str'] = ucfirst(str_replace('_', ' ', $m));
            }
        }

        $cantidad_ventas = count($ventas);
        $promedio = $cantidad_ventas > 0 ? $total_ventas / $cantidad_ventas : 0;

        $data = [
            'ventas' => $ventas,
            'fecha_inicio' => $fecha_inicio,
            'fecha_fin' => $fecha_fin,
            'total_ventas' => $total_ventas,
            'cantidad_ventas' => $cantidad_ventas,
            'promedio' => $promedio,
            'desglose_medios' => $desglose_medios
        ];

        if ($formato === 'pdf') {
            // TODO: $this->generarPDFVentas($data);
        } elseif ($formato === 'excel') {
            $this->generarExcelVentas($data);
        } else {
            $data['pageTitle'] = 'Reporte de Ventas';
            $data['currentPage'] = 'reportes';
            require_once dirname(__DIR__) . '/Views/reportes/ventas.php';
        }
    }

    private function generarExcelVentas($data)
    {
        require_once dirname(__DIR__, 2) . '/vendor/autoload.php';

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Propiedades
        $spreadsheet->getProperties()->setCreator("SIPAN")
            ->setTitle("Reporte de Ventas")
            ->setSubject("Ventas " . $data['fecha_inicio'] . " al " . $data['fecha_fin']);

        // Encabezados
        $sheet->setCellValue('A1', 'Reporte de Ventas');
        $sheet->setCellValue('A2', 'Fecha Inicio: ' . $data['fecha_inicio']);
        $sheet->setCellValue('A3', 'Fecha Fin: ' . $data['fecha_fin']);

        $headers = ['ID', 'Fecha', 'Cliente', 'Usuario', 'Método Pago', 'Total'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '5', $header);
            $sheet->getStyle($col . '5')->getFont()->setBold(true);
            $col++;
        }

        // Datos
        $row = 6;
        foreach ($data['ventas'] as $venta) {
            $sheet->setCellValue('A' . $row, $venta['id']);
            $sheet->setCellValue('B' . $row, date('d/m/Y H:i', strtotime($venta['fecha_venta'])));
            $sheet->setCellValue('C' . $row, $venta['cliente_nombre'] ?? 'Cliente General');
            $sheet->setCellValue('D' . $row, $venta['usuario_nombre']);

            // Limpiar HTML de método de pago
            $metodo = strip_tags(str_replace('<br>', ', ', $venta['detalle_pagos_str']));
            $sheet->setCellValue('E' . $row, $metodo);

            $sheet->setCellValue('F' . $row, $venta['total']);
            $sheet->getStyle('F' . $row)->getNumberFormat()->setFormatCode('#,##0.00');

            $row++;
        }

        // Totales
        $sheet->setCellValue('E' . $row, 'TOTAL GENERAL');
        $sheet->setCellValue('F' . $row, $data['total_ventas']);
        $sheet->getStyle('E' . $row)->getFont()->setBold(true);
        $sheet->getStyle('F' . $row)->getFont()->setBold(true);
        $sheet->getStyle('F' . $row)->getNumberFormat()->setFormatCode('#,##0.00');

        // Ajuste automático de columnas
        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Descarga
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="reporte_ventas_' . date('Y-m-d') . '.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function productos()
    {
        $productos = $this->productoModel->getBySucursal($_SESSION['sucursal_id']);
        $formato = $_GET['formato'] ?? 'html';

        // Optimización Bolt: El valor_stock ya viene calculado desde el modelo (SQL)
        $valor_total = array_sum(array_column($productos, 'valor_stock'));

        $data = [
            'productos' => $productos,
            'valor_total' => $valor_total
        ];

        if ($formato === 'pdf') {
            //TODO: $this->generarPDFProductos($data);
        } elseif ($formato === 'excel') {
            $this->generarExcelProductos($data);
        } else {
            $data['pageTitle'] = 'Reporte de Productos';
            $data['currentPage'] = 'reportes';
            require_once dirname(__DIR__) . '/Views/reportes/productos.php';
        }
    }

    private function generarExcelProductos($data)
    {
        require_once dirname(__DIR__, 2) . '/vendor/autoload.php';

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Propiedades
        $spreadsheet->getProperties()->setCreator("SIPAN")
            ->setTitle("Reporte de Inventario")
            ->setSubject("Inventario al " . date('d/m/Y'));

        // Encabezados
        $sheet->setCellValue('A1', 'Reporte de Inventario de Productos');
        $sheet->setCellValue('A2', 'Fecha: ' . date('d/m/Y H:i'));

        $headers = ['Producto', 'Categoría', 'Stock Actual', 'Precio Unit.', 'Valor Stock'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '4', $header);
            $sheet->getStyle($col . '4')->getFont()->setBold(true);
            $col++;
        }

        // Datos
        $row = 5;
        foreach ($data['productos'] as $prod) {
            $sheet->setCellValue('A' . $row, $prod['nombre']);
            $sheet->setCellValue('B' . $row, $prod['categoria_nombre'] ?? '-');
            $sheet->setCellValue('C' . $row, $prod['stock_actual']);
            $sheet->setCellValue('D' . $row, $prod['precio_actual']);
            $sheet->setCellValue('E' . $row, $prod['valor_stock']);

            $sheet->getStyle('D' . $row)->getNumberFormat()->setFormatCode('#,##0.00');
            $sheet->getStyle('E' . $row)->getNumberFormat()->setFormatCode('#,##0.00');

            $row++;
        }

        // Totales
        $sheet->setCellValue('D' . $row, 'VALOR TOTAL');
        $sheet->setCellValue('E' . $row, $data['valor_total']);
        $sheet->getStyle('D' . $row)->getFont()->setBold(true);
        $sheet->getStyle('E' . $row)->getFont()->setBold(true);
        $sheet->getStyle('E' . $row)->getNumberFormat()->setFormatCode('#,##0.00');

        // Ajuste automático
        foreach (range('A', 'E') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="reporte_inventario_' . date('Y-m-d') . '.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function clientes()
    {
        // Optimización Bolt: Usar método que trae estadísticas en una sola consulta
        $clientes = $this->clienteModel->getBySucursalWithStats($_SESSION['sucursal_id']);
        $formato = $_GET['formato'] ?? 'html';

        $data = [
            'clientes' => $clientes
        ];

        if ($formato === 'pdf') {
            // TODO: $this->generarPDFClientes($data);
        } elseif ($formato === 'excel') {
            $this->generarExcelClientes($data);
        } else {
            $data['pageTitle'] = 'Reporte de Clientes';
            $data['currentPage'] = 'reportes';
            require_once dirname(__DIR__) . '/Views/reportes/clientes.php';
        }
    }

    private function generarExcelClientes($data)
    {
        require_once dirname(__DIR__, 2) . '/vendor/autoload.php';

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $spreadsheet->getProperties()->setCreator("SIPAN")->setTitle("Reporte de Clientes");

        $sheet->setCellValue('A1', 'Reporte de Clientes');
        $sheet->setCellValue('A2', 'Fecha: ' . date('d/m/Y H:i'));

        $headers = ['Cliente', 'Documento', 'Teléfono', 'Compras', 'Monto Total'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '4', $header);
            $sheet->getStyle($col . '4')->getFont()->setBold(true);
            $col++;
        }

        $row = 5;
        foreach ($data['clientes'] as $cli) {
            $sheet->setCellValue('A' . $row, $cli['nombre']);
            $sheet->setCellValue('B' . $row, $cli['documento_numero'] ?? '-');
            $sheet->setCellValue('C' . $row, $cli['telefono'] ?? '-');
            $sheet->setCellValue('D' . $row, $cli['total_compras']);
            $sheet->setCellValue('E' . $row, $cli['monto_total']);
            $sheet->getStyle('E' . $row)->getNumberFormat()->setFormatCode('#,##0.00');
            $row++;
        }

        foreach (range('A', 'E') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="reporte_clientes_' . date('Y-m-d') . '.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    // ... (PDF Methods skipped for brevity if not modifying) ...

    public function insumos()
    {
        $insumos = $this->insumoModel->getAllBySucursal($_SESSION['sucursal_id']);
        $formato = $_GET['formato'] ?? 'html';

        $data = ['insumos' => $insumos];

        if ($formato === 'pdf') {
            $this->generarPDFInsumos($data);
        } elseif ($formato === 'excel') {
            $this->generarExcelInsumos($data);
        } else {
            $data['pageTitle'] = 'Reporte de Insumos';
            $data['currentPage'] = 'reportes';
            require_once __DIR__ . '/../Views/reportes/insumos.php';
        }
    }

    private function generarExcelInsumos($data)
    {
        require_once dirname(__DIR__, 2) . '/vendor/autoload.php';

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $spreadsheet->getProperties()->setCreator("SIPAN")->setTitle("Reporte de Insumos");

        $sheet->setCellValue('A1', 'Reporte de Insumos');
        $sheet->setCellValue('A2', 'Fecha: ' . date('d/m/Y H:i'));

        $headers = ['Código', 'Insumo', 'Unidad', 'Stock Actual'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '4', $header);
            $sheet->getStyle($col . '4')->getFont()->setBold(true);
            $col++;
        }

        $row = 5;
        foreach ($data['insumos'] as $i) {
            $sheet->setCellValue('A' . $row, $i['codigo'] ?? '-');
            $sheet->setCellValue('B' . $row, $i['nombre']);
            $sheet->setCellValue('C' . $row, $i['unidad_medida']);
            $sheet->setCellValue('D' . $row, $i['stock_actual']);
            $row++;
        }

        foreach (range('A', 'D') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="reporte_insumos_' . date('Y-m-d') . '.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function producciones()
    {
        $fecha_inicio = $_GET['fecha_inicio'] ?? date('Y-m-01');
        $fecha_fin = $_GET['fecha_fin'] ?? date('Y-m-d');
        $formato = $_GET['formato'] ?? 'html';

        $producciones = $this->produccionModel->getWithDetails($_SESSION['sucursal_id'], $fecha_inicio, $fecha_fin);

        $data = [
            'producciones' => $producciones,
            'fecha_inicio' => $fecha_inicio,
            'fecha_fin' => $fecha_fin
        ];

        if ($formato === 'pdf') {
            $this->generarPDFProducciones($data);
        } elseif ($formato === 'excel') {
            $this->generarExcelProducciones($data);
        } else {
            $data['pageTitle'] = 'Reporte de Producciones';
            $data['currentPage'] = 'reportes';
            require_once __DIR__ . '/../Views/reportes/producciones.php';
        }
    }

    private function generarExcelProducciones($data)
    {
        require_once dirname(__DIR__, 2) . '/vendor/autoload.php';

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $spreadsheet->getProperties()->setCreator("SIPAN")->setTitle("Reporte de Producciones");

        $sheet->setCellValue('A1', 'Reporte de Producciones');
        $sheet->setCellValue('A2', 'Rango: ' . $data['fecha_inicio'] . ' al ' . $data['fecha_fin']);

        $headers = ['Fecha', 'Producto', 'Cantidad', 'Responsable', 'Estado'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '4', $header);
            $sheet->getStyle($col . '4')->getFont()->setBold(true);
            $col++;
        }

        $row = 5;
        foreach ($data['producciones'] as $p) {
            $sheet->setCellValue('A' . $row, date('d/m/Y H:i', strtotime($p['fecha_produccion'])));
            $sheet->setCellValue('B' . $row, $p['producto_nombre']);
            $sheet->setCellValue('C' . $row, $p['cantidad_producida']);
            $sheet->setCellValue('D' . $row, $p['primer_nombre'] . ' ' . $p['apellido_paterno']);
            $sheet->setCellValue('E' . $row, ucfirst($p['estado']));
            $row++;
        }

        foreach (range('A', 'E') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="reporte_producciones_' . date('Y-m-d') . '.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function pedidos()
    {
        $fecha_inicio = $_GET['fecha_inicio'] ?? date('Y-m-01');
        $fecha_fin = $_GET['fecha_fin'] ?? date('Y-m-d');
        $formato = $_GET['formato'] ?? 'html';

        $pedidos = $this->pedidoModel->getWithDetails($_SESSION['sucursal_id'], null, null, $fecha_inicio, $fecha_fin);
        $pedidoIds = array_column($pedidos, 'id');

        // Optimización Bolt: Batch fetch de pagos para evitar N+1 queries
        $pagos_agrupados = [];
        if (!empty($pedidoIds)) {
            $todos_los_pagos = $this->pedidoModel->getPagosPorPedidos($pedidoIds);
            foreach ($todos_los_pagos as $p) {
                $pagos_agrupados[$p['id_pedido']][] = $p;
            }
        }

        foreach ($pedidos as &$pedido) {
            $pagos = $pagos_agrupados[$pedido['id']] ?? [];
            if (!empty($pagos)) {
                $lista_pagos = [];
                foreach ($pagos as $p) {
                    $m = $p['metodo_pago'];
                    $v = $p['monto'];
                    $lista_pagos[] = ucfirst(str_replace('_', ' ', $m)) . ': ' . number_format($v, 2);
                }
                $pedido['detalle_pagos_str'] = implode('<br>', $lista_pagos);
            } else {
                $pedido['detalle_pagos_str'] = 'Sin pagos';
            }
        }
        unset($pedido);

        $data = [
            'pedidos' => $pedidos,
            'fecha_inicio' => $fecha_inicio,
            'fecha_fin' => $fecha_fin
        ];

        if ($formato === 'pdf') {
            $this->generarPDFPedidos($data);
        } elseif ($formato === 'excel') {
            $this->generarExcelPedidos($data);
        } else {
            $data['pageTitle'] = 'Reporte de Pedidos';
            $data['currentPage'] = 'reportes';
            require_once __DIR__ . '/../Views/reportes/pedidos.php';
        }
    }

    private function generarExcelPedidos($data)
    {
        require_once dirname(__DIR__, 2) . '/vendor/autoload.php';

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $spreadsheet->getProperties()->setCreator("SIPAN")->setTitle("Reporte de Pedidos");

        $sheet->setCellValue('A1', 'Reporte de Pedidos');
        $sheet->setCellValue('A2', 'Rango: ' . $data['fecha_inicio'] . ' al ' . $data['fecha_fin']);

        $headers = ['N° Pedido', 'Fecha', 'Cliente', 'Total', 'Estado Pedido', 'Estado Pago'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '4', $header);
            $sheet->getStyle($col . '4')->getFont()->setBold(true);
            $col++;
        }

        $row = 5;
        foreach ($data['pedidos'] as $p) {
            $sheet->setCellValue('A' . $row, $p['numero_pedido']);
            $sheet->setCellValue('B' . $row, date('d/m/Y H:i', strtotime($p['fecha_pedido'])));
            $sheet->setCellValue('C' . $row, $p['cliente_nombre'] . ' ' . $p['cliente_apellido']);
            $sheet->setCellValue('D' . $row, $p['total']);
            $sheet->setCellValue('E' . $row, ucfirst($p['estado_pedido']));
            $sheet->setCellValue('F' . $row, ucfirst($p['estado_pago']));

            $sheet->getStyle('D' . $row)->getNumberFormat()->setFormatCode('#,##0.00');
            $row++;
        }

        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="reporte_pedidos_' . date('Y-m-d') . '.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    private function generarPDFInsumos($data)
    {
        require_once __DIR__ . '/../../vendor/autoload.php';
        $html = $this->getHTMLInsumos($data);
        $mpdf = new \Mpdf\Mpdf(['format' => 'A4']);
        $mpdf->WriteHTML($html);
        $mpdf->Output('reporte_insumos_' . date('Y-m-d') . '.pdf', 'D');
    }

    private function getHTMLInsumos($data)
    {
        ob_start();
        ?>
        <!DOCTYPE html>
        <html>

        <head>
            <style>
                body {
                    font-family: monospace;
                    font-size: 11px;
                }

                h1 {
                    text-align: center;
                }

                table {
                    width: 100%;
                    border-collapse: collapse;
                }

                th,
                td {
                    border-bottom: 1px solid #ddd;
                    padding: 5px;
                    text-align: left;
                }

                .num {
                    text-align: right;
                }
            </style>
        </head>

        <body>
            <h1>Reporte de Insumos</h1>
            <table>
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Insumo</th>
                        <th>Unidad</th>
                        <th class="num">Stock</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data['insumos'] as $i) : ?>
                        <tr>
                            <td><?= htmlspecialchars($i['codigo'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($i['nombre']) ?></td>
                            <td><?= htmlspecialchars($i['unidad_medida']) ?></td>
                            <td class="num"><?= $i['stock_actual'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </body>

        </html>
        <?php
        return ob_get_clean();
    }

    private function generarPDFProducciones($data)
    {
        require_once __DIR__ . '/../../vendor/autoload.php';
        $html = $this->getHTMLProducciones($data);
        $mpdf = new \Mpdf\Mpdf(['format' => 'A4']);
        $mpdf->WriteHTML($html);
        $mpdf->Output('reporte_producciones_' . date('Y-m-d') . '.pdf', 'D');
    }

    private function getHTMLProducciones($data)
    {
        ob_start();
        ?>
        <!DOCTYPE html>
        <html>

        <head>
            <style>
                body {
                    font-family: monospace;
                    font-size: 11px;
                }

                h1 {
                    text-align: center;
                }

                table {
                    width: 100%;
                    border-collapse: collapse;
                }

                th,
                td {
                    border-bottom: 1px solid #ddd;
                    padding: 5px;
                    text-align: left;
                }

                .num {
                    text-align: right;
                }
            </style>
        </head>

        <body>
            <h1>Reporte de Producciones</h1>
            <table>
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Producto</th>
                        <th class="num">Cantidad</th>
                        <th>Responsable</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data['producciones'] as $p) : ?>
                        <tr>
                            <td><?= date('d/m/Y H:i', strtotime($p['fecha_produccion'])) ?></td>
                            <td><?= htmlspecialchars($p['producto_nombre']) ?></td>
                            <td class="num"><?= $p['cantidad_producida'] ?></td>
                            <td><?= htmlspecialchars($p['primer_nombre'] . ' ' . $p['apellido_paterno']) ?></td>
                            <td><?= ucfirst($p['estado']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </body>

        </html>
        <?php
        return ob_get_clean();
    }

    private function generarPDFPedidos($data)
    {
        require_once __DIR__ . '/../../vendor/autoload.php';
        $html = $this->getHTMLPedidos($data);
        $mpdf = new \Mpdf\Mpdf(['format' => 'A4', 'orientation' => 'L']);
        $mpdf->WriteHTML($html);
        $mpdf->Output('reporte_pedidos_' . date('Y-m-d') . '.pdf', 'D');
    }

    private function getHTMLPedidos($data)
    {
        ob_start();
        ?>
        <!DOCTYPE html>
        <html>

        <head>
            <style>
                body {
                    font-family: monospace;
                    font-size: 11px;
                }

                h1 {
                    text-align: center;
                }

                table {
                    width: 100%;
                    border-collapse: collapse;
                }

                th,
                td {
                    border-bottom: 1px solid #ddd;
                    padding: 5px;
                    text-align: left;
                }

                .num {
                    text-align: right;
                }
            </style>
        </head>

        <body>
            <h1>Reporte de Pedidos</h1>
            <table>
                <thead>
                    <tr>
                        <th>N° Pedido</th>
                        <th>Fecha</th>
                        <th>Cliente</th>
                        <th class="num">Total</th>
                        <th>Estado Pedido</th>
                        <th>Estado Pago</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data['pedidos'] as $p) : ?>
                        <tr>
                            <td><?= htmlspecialchars($p['numero_pedido']) ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($p['fecha_pedido'])) ?></td>
                            <td><?= htmlspecialchars($p['cliente_nombre'] . ' ' . $p['cliente_apellido']) ?></td>
                            <td class="num"><?= number_format($p['total'], 2) ?></td>
                            <td><?= ucfirst($p['estado_pedido']) ?></td>
                            <td><?= ucfirst($p['estado_pago']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </body>

        </html>
        <?php
        return ob_get_clean();
    }

    public function vencimientos()
    {
        $dias = isset($_GET['dias']) ? (int)$_GET['dias'] : 30;
        if (!in_array($dias, [15, 30, 60, 90], true)) {
            $dias = 30;
        }
        $formato = $_GET['formato'] ?? 'html';

        $sucursal_id = $_SESSION['sucursal_id'] ?? 1;
        $lotes = $this->loteModel->getPorVencer($sucursal_id, $dias, true);

        $pageTitle = 'Reporte de Vencimientos';
        $currentPage = 'reportes';

        $data = [
            'lotes' => $lotes,
            'dias' => $dias,
            'pageTitle' => $pageTitle,
            'currentPage' => $currentPage
        ];

        if ($formato === 'excel') {
            $this->generarExcelVencimientos($data);
        } elseif ($formato === 'pdf') {
            $this->generarPDFVencimientos($data);
        } else {
            require_once dirname(__DIR__) . '/Views/reportes/vencimientos.php';
        }
    }

    private function generarExcelVencimientos($data)
    {
        require_once dirname(__DIR__, 2) . '/vendor/autoload.php';

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $spreadsheet->getProperties()->setCreator("SIPAN")
            ->setTitle("Reporte de Vencimientos")
            ->setSubject("Lotes por vencer (" . $data['dias'] . " días)");

        $sheet->setCellValue('A1', 'Reporte de Vencimientos de Lotes');
        $sheet->setCellValue('A2', 'Rango: Próximos ' . $data['dias'] . ' días | Fecha reporte: ' . date('d/m/Y H:i'));

        $headers = ['Código Lote', 'Item', 'Tipo', 'Fecha Entrada', 'Fecha Vencimiento', 'Días Restantes', 'Cant. Inicial', 'Stock Actual', 'Estado'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '4', $header);
            $sheet->getStyle($col . '4')->getFont()->setBold(true);
            $col++;
        }

        $row = 5;
        $hoy = time();
        foreach ($data['lotes'] as $lote) {
            $fecha_venc = strtotime($lote['fecha_vencimiento']);
            $dias_restantes = ceil(($fecha_venc - $hoy) / 86400);
            $estado = $dias_restantes <= 0 ? 'Vencido' : ($dias_restantes <= 15 ? 'Crítico' : 'Ok');

            $sheet->setCellValue('A' . $row, $lote['codigo_lote']);
            $sheet->setCellValue('B' . $row, $lote['nombre_item'] ?? '-');
            $sheet->setCellValue('C' . $row, ucfirst($lote['tipo'] ?? ''));
            $sheet->setCellValue('D' . $row, date('d/m/Y', strtotime($lote['fecha_entrada'])));
            $sheet->setCellValue('E' . $row, date('d/m/Y', strtotime($lote['fecha_vencimiento'])));
            $sheet->setCellValue('F' . $row, $dias_restantes);
            $sheet->setCellValue('G' . $row, $lote['cantidad_inicial']);
            $sheet->setCellValue('H' . $row, $lote['cantidad_actual']);
            $sheet->setCellValue('I' . $row, $estado);

            $sheet->getStyle('F' . $row)->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getStyle('G' . $row)->getNumberFormat()->setFormatCode('#,##0.00');
            $sheet->getStyle('H' . $row)->getNumberFormat()->setFormatCode('#,##0.00');

            $row++;
        }

        foreach (range('A', 'I') as $c) {
            $sheet->getColumnDimension($c)->setAutoSize(true);
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="reporte_vencimientos_' . date('Y-m-d') . '.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    private function generarPDFVencimientos($data)
    {
        require_once dirname(__DIR__, 2) . '/vendor/autoload.php';
        $html = $this->getHTMLVencimientos($data);
        $mpdf = new \Mpdf\Mpdf(['format' => 'A4', 'orientation' => 'L']);
        $mpdf->WriteHTML($html);
        $mpdf->Output('reporte_vencimientos_' . date('Y-m-d') . '.pdf', 'D');
        exit;
    }

    private function getHTMLVencimientos($data)
    {
        ob_start();
        $hoy = time();
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                body { font-family: monospace; font-size: 11px; }
                h1 { text-align: center; margin-bottom: 4px; }
                p.sub { text-align: center; color: #555; margin-top: 0; margin-bottom: 16px; }
                table { width: 100%; border-collapse: collapse; }
                th, td { border-bottom: 1px solid #ddd; padding: 6px; text-align: left; }
                th { background-color: #f5f5f5; }
                .num { text-align: right; }
                .text-danger { color: #dc3545; font-weight: bold; }
                .text-warning { color: #d39e00; font-weight: bold; }
                .text-success { color: #198754; }
            </style>
        </head>
        <body>
            <h1>Reporte de Vencimientos</h1>
            <p class="sub">Lotes con vencimiento en los próximos <?= $data['dias'] ?> días | Fecha: <?= date('d/m/Y H:i') ?></p>
            <table>
                <thead>
                    <tr>
                        <th>Código Lote</th>
                        <th>Item</th>
                        <th>Tipo</th>
                        <th>Fecha Entrada</th>
                        <th>Fecha Vencimiento</th>
                        <th class="num">Días Rest.</th>
                        <th class="num">Stock Actual</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($data['lotes'])) : ?>
                        <tr><td colspan="8" style="text-align: center; padding: 15px; color: #666;">No se encontraron lotes por vencer.</td></tr>
                    <?php else : ?>
                        <?php foreach ($data['lotes'] as $l) :
                            $fecha_venc = strtotime($l['fecha_vencimiento']);
                            $dias_restantes = ceil(($fecha_venc - $hoy) / 86400);
                            $clase = $dias_restantes <= 0 ? 'text-danger' : ($dias_restantes <= 15 ? 'text-warning' : 'text-success');
                            $estado = $dias_restantes <= 0 ? 'Vencido' : ($dias_restantes <= 15 ? 'Crítico' : 'Ok');
                            ?>
                            <tr>
                                <td><?= htmlspecialchars($l['codigo_lote']) ?></td>
                                <td><?= htmlspecialchars($l['nombre_item'] ?? '-') ?></td>
                                <td><?= ucfirst($l['tipo']) ?></td>
                                <td><?= date('d/m/Y', strtotime($l['fecha_entrada'])) ?></td>
                                <td><?= date('d/m/Y', strtotime($l['fecha_vencimiento'])) ?></td>
                                <td class="num <?= $clase ?>"><?= $dias_restantes ?></td>
                                <td class="num"><?= number_format((float)$l['cantidad_actual'], 2) ?></td>
                                <td class="<?= $clase ?>"><?= $estado ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </body>
        </html>
        <?php
        return ob_get_clean();
    }

    public function compras()
    {
        $fecha_inicio = $_GET['fecha_inicio'] ?? date('Y-m-01');
        $fecha_fin = $_GET['fecha_fin'] ?? date('Y-m-d');
        $id_proveedor = !empty($_GET['id_proveedor']) ? (int)$_GET['id_proveedor'] : null;
        $formato = $_GET['formato'] ?? 'html';

        $sucursal_id = $_SESSION['sucursal_id'] ?? 1;

        $proveedores = $this->proveedorModel->getAllBySucursal($sucursal_id);
        foreach ($proveedores as &$prov) {
            $prov['nombre_empresa'] = $prov['nombre'];
        }
        unset($prov);

        $compras = $this->compraModel->getByDateRangeAndProveedor($sucursal_id, $fecha_inicio, $fecha_fin, $id_proveedor);
        $total_compras = array_sum(array_column($compras, 'total'));

        $pageTitle = 'Reporte de Compras';
        $currentPage = 'reportes';

        $data = [
            'compras' => $compras,
            'total_compras' => $total_compras,
            'fecha_inicio' => $fecha_inicio,
            'fecha_fin' => $fecha_fin,
            'id_proveedor' => $id_proveedor,
            'proveedores' => $proveedores,
            'pageTitle' => $pageTitle,
            'currentPage' => $currentPage
        ];

        if ($formato === 'excel') {
            $this->generarExcelCompras($data);
        } elseif ($formato === 'pdf') {
            $this->generarPDFCompras($data);
        } else {
            require_once dirname(__DIR__) . '/Views/reportes/compras.php';
        }
    }

    private function generarExcelCompras($data)
    {
        require_once dirname(__DIR__, 2) . '/vendor/autoload.php';

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $spreadsheet->getProperties()->setCreator("SIPAN")
            ->setTitle("Reporte de Compras")
            ->setSubject("Historial de Compras del " . $data['fecha_inicio'] . " al " . $data['fecha_fin']);

        $sheet->setCellValue('A1', 'Reporte de Compras y Abastecimiento');
        $sheet->setCellValue('A2', 'Período: ' . date('d/m/Y', strtotime($data['fecha_inicio'])) . ' al ' . date('d/m/Y', strtotime($data['fecha_fin'])) . ' | Fecha emisión: ' . date('d/m/Y H:i'));

        $headers = ['Fecha', 'Proveedor', 'Comprobante', 'Items (Insumos/Productos)', 'Total ($)', 'Registrado por'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '4', $header);
            $sheet->getStyle($col . '4')->getFont()->setBold(true);
            $col++;
        }

        $row = 5;
        foreach ($data['compras'] as $compra) {
            $sheet->setCellValue('A' . $row, date('d/m/Y H:i', strtotime($compra['fecha_compra'])));
            $sheet->setCellValue('B' . $row, $compra['proveedor_nombre'] ?? 'Sin Proveedor');
            $sheet->setCellValue('C' . $row, $compra['numero_comprobante'] ?: 'S/N');
            $sheet->setCellValue('D' . $row, $compra['items_resumen'] ?? '-');
            $sheet->setCellValue('E' . $row, (float)$compra['total']);
            $sheet->setCellValue('F' . $row, $compra['usuario_nombre'] ?? '-');

            $sheet->getStyle('E' . $row)->getNumberFormat()->setFormatCode('#,##0.00');

            $row++;
        }

        // Totales
        $sheet->setCellValue('D' . $row, 'TOTAL COMPRAS:');
        $sheet->setCellValue('E' . $row, (float)$data['total_compras']);
        $sheet->getStyle('D' . $row)->getFont()->setBold(true);
        $sheet->getStyle('E' . $row)->getFont()->setBold(true);
        $sheet->getStyle('E' . $row)->getNumberFormat()->setFormatCode('#,##0.00');

        foreach (range('A', 'F') as $c) {
            $sheet->getColumnDimension($c)->setAutoSize(true);
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="reporte_compras_' . date('Y-m-d') . '.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    private function generarPDFCompras($data)
    {
        require_once dirname(__DIR__, 2) . '/vendor/autoload.php';
        $html = $this->getHTMLCompras($data);
        $mpdf = new \Mpdf\Mpdf(['format' => 'A4', 'orientation' => 'L']);
        $mpdf->WriteHTML($html);
        $mpdf->Output('reporte_compras_' . date('Y-m-d') . '.pdf', 'D');
        exit;
    }

    private function getHTMLCompras($data)
    {
        ob_start();
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                body { font-family: monospace; font-size: 11px; }
                h1 { text-align: center; margin-bottom: 4px; }
                p.sub { text-align: center; color: #555; margin-top: 0; margin-bottom: 16px; }
                table { width: 100%; border-collapse: collapse; }
                th, td { border-bottom: 1px solid #ddd; padding: 6px; text-align: left; }
                th { background-color: #f5f5f5; }
                .num { text-align: right; }
                .fw-bold { font-weight: bold; }
                .total-row { background-color: #f0f0f0; font-weight: bold; }
            </style>
        </head>
        <body>
            <h1>Reporte de Compras y Abastecimiento</h1>
            <p class="sub">Período: <?= date('d/m/Y', strtotime($data['fecha_inicio'])) ?> al <?= date('d/m/Y', strtotime($data['fecha_fin'])) ?> | Total: $ <?= number_format((float)$data['total_compras'], 2) ?></p>
            <table>
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Proveedor</th>
                        <th>Comprobante</th>
                        <th>Items (Insumos/Productos)</th>
                        <th>Registrado por</th>
                        <th class="num">Total ($)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($data['compras'])) : ?>
                        <tr><td colspan="6" style="text-align: center; padding: 15px; color: #666;">No se encontraron compras en el rango seleccionado.</td></tr>
                    <?php else : ?>
                        <?php foreach ($data['compras'] as $c) : ?>
                            <tr>
                                <td><?= date('d/m/Y H:i', strtotime($c['fecha_compra'])) ?></td>
                                <td><?= htmlspecialchars($c['proveedor_nombre'] ?? 'Sin Proveedor') ?></td>
                                <td><?= htmlspecialchars($c['numero_comprobante'] ?: 'S/N') ?></td>
                                <td><?= htmlspecialchars($c['items_resumen'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($c['usuario_nombre'] ?? '-') ?></td>
                                <td class="num fw-bold">$ <?= number_format((float)$c['total'], 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <tr class="total-row">
                            <td colspan="5" style="text-align: right;">TOTAL GENERAL:</td>
                            <td class="num">$ <?= number_format((float)$data['total_compras'], 2) ?></td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </body>
        </html>
        <?php
        return ob_get_clean();
    }
}

