<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Caja Chica</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 11px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .header h1 { margin: 0; font-size: 20px; color: #1f2937; }
        .header p { margin: 5px 0 0; color: #6b7280; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #d1d5db; padding: 6px; text-align: left; }
        th { background-color: #f3f4f6; font-weight: bold; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .badge { padding: 2px 4px; border-radius: 4px; font-weight: bold; }
        .bg-red { background-color: #fee2e2; color: #991b1b; }
        .bg-green { background-color: #dcfce7; color: #166534; }
        .footer { position: fixed; bottom: -20px; width: 100%; text-align: center; font-size: 10px; color: #9ca3af; }
    </style>
</head>
<body>
    <div class="header">
        <h1>REPORTE DE ARQUEOS DE CAJA CHICA</h1>
        <h2>{{ strtoupper($sucursal->nombre) }}</h2>
        <p>Periodo: {{ date('d/m/Y', strtotime($fecha_inicio)) }} al {{ date('d/m/Y', strtotime($fecha_fin)) }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Fecha Apertura</th>
                <th>Aperturado Por</th>
                <th>Fecha Cierre</th>
                <th>Cerrado Por</th>
                <th class="text-right">Monto Inicial ($)</th>
                <th class="text-right">Monto Cierre ($)</th>
                <th class="text-right">Descuadre ($)</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @forelse($cajas as $caja)
            @php 
                $descuadre = $caja->monto_cierre_usd - $caja->monto_esperado_usd;
                $clase_descuadre = abs($descuadre) > 0.1 ? 'bg-red' : 'bg-green';
            @endphp
            <tr>
                <td>#{{ $caja->id }}</td>
                <td>{{ $caja->fecha_apertura->format('d/m/Y h:i A') }}</td>
                <td>{{ $caja->usuarioApertura->primer_nombre ?? 'N/A' }}</td>
                <td>{{ $caja->fecha_cierre ? $caja->fecha_cierre->format('d/m/Y h:i A') : '-' }}</td>
                <td>{{ $caja->usuarioCierre->primer_nombre ?? '-' }}</td>
                <td class="text-right">${{ number_format($caja->monto_apertura_usd, 2) }}</td>
                <td class="text-right">${{ number_format($caja->monto_cierre_usd, 2) }}</td>
                <td class="text-right"><span class="badge {{ $clase_descuadre }}">${{ number_format($descuadre, 2) }}</span></td>
                <td>{{ ucfirst($caja->estado) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="9" class="text-center">No hay registros de cajas chicas en este periodo.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Sistema SIPAN V3 - Generado por Usuario #{{ Auth::id() }}
    </div>
</body>
</html>
