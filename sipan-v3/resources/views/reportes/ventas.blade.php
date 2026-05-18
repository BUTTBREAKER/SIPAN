<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Ventas</title>
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
        .totals { margin-top: 20px; width: 50%; float: right; }
        .totals th { background-color: #e5e7eb; }
        .badge-success { color: green; }
        .footer { position: fixed; bottom: -20px; width: 100%; text-align: center; font-size: 10px; color: #9ca3af; }
    </style>
</head>
<body>
    <div class="header">
        <h1>REPORTE DE VENTAS - {{ strtoupper($sucursal->nombre) }}</h1>
        <p>Periodo: {{ date('d/m/Y', strtotime($fecha_inicio)) }} al {{ date('d/m/Y', strtotime($fecha_fin)) }}</p>
        <p>Generado el: {{ date('d/m/Y H:i:s') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Nro Venta</th>
                <th>Fecha y Hora</th>
                <th>Cliente</th>
                <th>Método</th>
                <th class="text-right">Tasa BCV</th>
                <th class="text-right">Total (Bs)</th>
                <th class="text-right">Total ($)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($ventas as $venta)
            <tr>
                <td>{{ $venta->numero_venta }}</td>
                <td>{{ $venta->fecha_venta->format('d/m/Y h:i A') }}</td>
                <td>{{ $venta->cliente->nombre ?? 'Cliente Final' }}</td>
                <td>{{ ucfirst(str_replace('_', ' ', $venta->metodo_pago)) }}</td>
                <td class="text-right">{{ number_format($venta->tasa_bcv, 2) }}</td>
                <td class="text-right">{{ number_format($venta->total_ves, 2) }}</td>
                <td class="text-right badge-success">${{ number_format($venta->total_usd, 2) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center">No se encontraron ventas completadas en este periodo.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <table class="totals">
        <tbody>
            <tr>
                <th>Total Facturado (VES)</th>
                <td class="text-right font-bold">Bs {{ number_format($total_ves, 2) }}</td>
            </tr>
            <tr>
                <th>Total Facturado (USD)</th>
                <td class="text-right font-bold badge-success">${{ number_format($total_usd, 2) }}</td>
            </tr>
            <tr>
                <th>Total de Operaciones</th>
                <td class="text-right">{{ count($ventas) }}</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        Sistema SIPAN V3 - Generado por Usuario #{{ Auth::id() }}
    </div>
</body>
</html>
