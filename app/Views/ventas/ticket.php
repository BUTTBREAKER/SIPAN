<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket #<?= str_pad($venta['id'], 6, '0', STR_PAD_LEFT) ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            line-height: 1.4;
            padding: 20px;
            max-width: 300px;
            margin: 0 auto;
        }
        
        .ticket {
            border: 1px solid #000;
            padding: 10px;
        }
        
        .header {
            text-align: center;
            border-bottom: 1px dashed #000;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }
        
        .header h1 {
            font-size: 18px;
            margin-bottom: 5px;
        }
        
        .header p {
            margin: 2px 0;
        }
        
        .info {
            margin-bottom: 10px;
            border-bottom: 1px dashed #000;
            padding-bottom: 10px;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            margin: 3px 0;
        }
        
        .items {
            margin-bottom: 10px;
        }
        
        .item {
            margin-bottom: 8px;
        }
        
        .item-name {
            font-weight: bold;
        }
        
        .item-details {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
        }
        
        .totals {
            border-top: 1px dashed #000;
            padding-top: 10px;
            margin-top: 10px;
        }
        
        .total-row {
            display: flex;
            justify-content: space-between;
            margin: 5px 0;
        }
        
        .total-row.final {
            font-size: 16px;
            font-weight: bold;
            border-top: 2px solid #000;
            padding-top: 5px;
            margin-top: 5px;
        }
        
        .footer {
            text-align: center;
            margin-top: 15px;
            border-top: 1px dashed #000;
            padding-top: 10px;
            font-size: 11px;
        }
        
        @media print {
            body {
                padding: 0;
            }
            
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="no-print" style="text-align: center; margin-bottom: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; font-size: 14px; cursor: pointer;">
            🖨️ Imprimir Ticket
        </button>
        <button onclick="window.close()" style="padding: 10px 20px; font-size: 14px; cursor: pointer; margin-left: 10px;">
            ❌ Cerrar
        </button>
    </div>
    
    <div class="ticket">
        <div class="header">
            <h1><?= htmlspecialchars($negocio['nombre'] ?? 'SIPAN') ?></h1>
            <p><?= htmlspecialchars($sucursal['nombre'] ?? 'Sucursal Principal') ?></p>
            <p><?= htmlspecialchars($sucursal['direccion'] ?? '') ?></p>
            <?php if (!empty($negocio['telefono'])): ?>
            <p>Tel: <?= htmlspecialchars($negocio['telefono']) ?></p>
            <?php endif; ?>
        </div>
        
        <div class="info">
            <div class="info-row">
                <span>TICKET:</span>
                <span><strong>#<?= str_pad($venta['id'], 6, '0', STR_PAD_LEFT) ?></strong></span>
            </div>
            <div class="info-row">
                <span>FECHA:</span>
                <span><?= date('d/m/Y H:i', strtotime($venta['fecha_venta'])) ?></span>
            </div>
            <div class="info-row">
                <span>CLIENTE:</span>
                <span><?= htmlspecialchars($venta['cliente_nombre'] ?? 'Cliente General') ?></span>
            </div>
            <div class="info-row">
                <span>ATENDIÓ:</span>
                <span><?= htmlspecialchars($venta['usuario_nombre'] ?? '-') ?></span>
            </div>
        </div>
        
        <div class="items">
            <?php foreach ($detalles as $detalle): ?>
            <div class="item">
                <div class="item-name"><?= htmlspecialchars($detalle['producto_nombre'] ?? 'Producto') ?></div>
                <div class="item-details">
                    <span><?= $detalle['cantidad'] ?> x $ <?= number_format($detalle['precio_unitario'], 2) ?></span>
                    <span>$ <?= number_format($detalle['subtotal'], 2) ?></span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <div class="totals">
            <div class="total-row final">
                <span>TOTAL:</span>
                <span>$ <?= number_format($venta['total'], 2) ?></span>
            </div>
            <div class="total-row">
                <span>MÉTODO DE PAGO:</span>
                <div style="text-align: right;">
                    <?php if (empty($pagos)): ?>
                        <span><?= strtoupper($venta['metodo_pago']) ?></span>
                    <?php else: ?>
                        <?php foreach($pagos as $p): ?>
                            <div>
                                <?= strtoupper($p['metodo_pago']) ?>: $ <?= number_format($p['monto'], 2) ?>
                                <?php if($p['referencia']): ?><br><small>(Ref: <?= $p['referencia'] ?>)</small><?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="footer">
            <p>¡GRACIAS POR SU COMPRA!</p>
            <p>Vuelva Pronto</p>
            <p style="margin-top: 10px;">Sistema SIPAN</p>
        </div>
    </div>
    
    <script>
        // Auto-imprimir al cargar (opcional)
        // window.onload = function() { window.print(); }
    </script>
</body>
</html>
