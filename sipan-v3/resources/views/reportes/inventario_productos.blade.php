<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Inventario de Productos</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .header h1 { margin: 0; font-size: 20px; color: #1f2937; }
        .header p { margin: 5px 0 0; color: #6b7280; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #d1d5db; padding: 8px; text-align: left; }
        th { background-color: #f3f4f6; font-weight: bold; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .warning { color: #d97706; font-weight: bold; }
        .danger { color: #dc2626; font-weight: bold; }
        .footer { position: fixed; bottom: -20px; width: 100%; text-align: center; font-size: 10px; color: #9ca3af; }
    </style>
</head>
<body>
    <div class="header">
        <h1>INVENTARIO DE PRODUCTOS FINALES</h1>
        <h2>{{ strtoupper($sucursal->nombre) }}</h2>
        <p>Fecha de corte: {{ date('d/m/Y H:i:s', strtotime($fecha)) }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Código</th>
                <th>Producto</th>
                <th>Categoría</th>
                <th class="text-right">Stock Actual</th>
                <th class="text-right">Stock Mínimo</th>
                <th class="text-right">Precio Costo ($)</th>
                <th class="text-right">Valorizado ($)</th>
            </tr>
        </thead>
        <tbody>
            @php $total_valorizado = 0; @endphp
            @forelse($items as $item)
            @php 
                $valor = $item->stock_actual * $item->precio_costo; 
                $total_valorizado += $valor;
                $clase_stock = '';
                if ($item->stock_actual <= 0) $clase_stock = 'danger';
                elseif ($item->stock_actual <= $item->stock_minimo) $clase_stock = 'warning';
            @endphp
            <tr>
                <td>{{ $item->codigo }}</td>
                <td>{{ $item->nombre }}</td>
                <td>{{ $item->categoria }}</td>
                <td class="text-right {{ $clase_stock }}">{{ number_format($item->stock_actual, 2) }}</td>
                <td class="text-right">{{ number_format($item->stock_minimo, 2) }}</td>
                <td class="text-right">{{ number_format($item->precio_costo, 2) }}</td>
                <td class="text-right">${{ number_format($valor, 2) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center">No hay productos registrados activos.</td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <th colspan="6" class="text-right">TOTAL VALORIZADO EN ALMACÉN (COSTO):</th>
                <th class="text-right font-bold">${{ number_format($total_valorizado, 2) }}</th>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        Sistema SIPAN V3 - Generado por Usuario #{{ Auth::id() }}
    </div>
</body>
</html>
