<?php

namespace App\Controllers;

use App\Models\Venta;
use App\Models\Producto;
use App\Models\Cliente;
use App\Models\Produccion;
use App\Models\Insumo;
use App\Models\Pedido;

class ReportesController
{
    private $ventaModel;
    private $productoModel;
    private $clienteModel;
    private $produccionModel;
    private $insumoModel;
    private $pedidoModel;

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
        $this->insumoModel = new Insumo();
        $this->pedidoModel = new Pedido();
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

        // Obtener desglose de pagos detallado
        // Necesitamos iterar ventas y buscar sus pagos si es mixto, o si queremos precision total
        // Lo ideal sería un query agrupado en venta_pagos, pero lo haremos iterativo por simplicidad

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

            // Buscar pagos de esta venta
            // Asumimos que ventaModel tiene metodo getPagos, si no, usaremos db directo
            $sql = "SELECT metodo_pago, monto FROM venta_pagos WHERE id_venta = ?";
            // Acceso "truco" al db del modelo si es protected, pero mejor instanciar modelo Venta si tiene el metodo
            // Si Venta no tiene getPagos, lo agregamos rapido o usamos query directo
            // Usaremos el modelo venta para ser limpios, asumiendo getPagos existe o lo creamos.
            // Si no existe, fallback a 'metodo_pago' de la tabla ventas.

            $pagos = $this->ventaModel->getPagos($venta['id']); // Necesitamos crear este metodo

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
                if ($m == 'mixto') {
                    // Si es mixto pero no tiene pagos en tabla (error data antigua), no sumamos al desglose especifico o lo ponemos en otros
                } else {
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
        $producciones = $this->produccionModel->getWithDetails($_SESSION['sucursal_id']);
        $formato = $_GET['formato'] ?? 'html';

        $data = ['producciones' => $producciones];

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
        $sheet->setCellValue('A2', 'Fecha: ' . date('d/m/Y H:i'));

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
        $pedidos = $this->pedidoModel->getWithDetails($_SESSION['sucursal_id']);
        $formato = $_GET['formato'] ?? 'html';

        $data = ['pedidos' => $pedidos];

        if ($formato === 'pdf') {
            $this->generarPDFPedidos($data);
        } else {
            $data['pageTitle'] = 'Reporte de Pedidos';
            $data['currentPage'] = 'reportes';
            require_once __DIR__ . '/../Views/reportes/pedidos.php';
        }
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
    private function generarPDFCompras($data)
    {
        require_once dirname(__DIR__, 2) . '/vendor/autoload.php';
        $html = $this->getHTMLCompras($data);
        $mpdf = new \Mpdf\Mpdf(['format' => 'A4']);
        $mpdf->WriteHTML($html);
        $mpdf->Output('reporte_compras_' . date('Y-m-d') . '.pdf', 'D');
    }

    private function getHTMLCompras($data)
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
            <h1>Reporte de Compras</h1>
            <p style="text-align:center">Del: <?= $data['fecha_inicio'] ?> Al: <?= $data['fecha_fin'] ?></p>
            <table>
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Proveedor</th>
                        <th>Comprobante</th>
                        <th class="num">Total</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data['compras'] as $c) : ?>
                        <tr>
                            <td><?= date('d/m/Y', strtotime($c['fecha_compra'])) ?></td>
                            <td><?= htmlspecialchars($c['proveedor_nombre']) ?></td>
                            <td><?= htmlspecialchars($c['numero_comprobante']) ?></td>
                            <td class="num"><?= number_format($c['total'], 2) ?></td>
                            <td><?= ucfirst($c['estado']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="3" class="num">TOTAL</th>
                        <th class="num">$ <?= number_format($data['total_compras'], 2) ?></th>
                        <th></th>
                    </tr>
                </tfoot>
            </table>
        </body>

        </html>
    <?php
        return ob_get_clean();
    }

    private function generarPDFVencimientos($data)
    {
        require_once dirname(__DIR__, 2) . '/vendor/autoload.php';
        $html = $this->getHTMLVencimientos($data);
        $mpdf = new \Mpdf\Mpdf(['format' => 'A4']);
        $mpdf->WriteHTML($html);
        $mpdf->Output('reporte_vencimientos_' . date('Y-m-d') . '.pdf', 'D');
    }

    private function getHTMLVencimientos($data)
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

                .badge {
                    font-size: 9px;
                    padding: 2px;
                    border: 1px solid #ccc;
                }
            </style>
        </head>

        <body>
            <h1>Reporte de Vencimientos</h1>
            <p style="text-align:center">Lotes que vencen en los próximos <?= $data['dias'] ?> días</p>
            <table>
                <thead>
                    <tr>
                        <th>Lote</th>
                        <th>Item</th>
                        <th>Vencimiento</th>
                        <th>Días</th>
                        <th class="num">Stock</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data['lotes'] as $l) :
                        $dias = ceil((strtotime($l['fecha_vencimiento']) - time()) / 86400);
                    ?>
                        <tr>
                            <td><?= htmlspecialchars($l['codigo_lote']) ?></td>
                            <td><?= htmlspecialchars($l['nombre_item']) ?></td>
                            <td><?= date('d/m/Y', strtotime($l['fecha_vencimiento'])) ?></td>
                            <td><?= $dias ?></td>
                            <td class="num"><?= $l['cantidad_actual'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </body>

        </html>
<?php
        return ob_get_clean();
    }
}
