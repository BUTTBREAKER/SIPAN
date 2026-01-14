<?php
$pageTitle = 'Detalle de Venta';
$currentPage = 'ventas';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="page-header">
    <h2 class="page-title">Detalle de Venta #<?= str_pad($venta['id'], 6, '0', STR_PAD_LEFT) ?></h2>
    <p class="page-subtitle">Información completa de la venta</p>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Productos Vendidos</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Precio Unit.</th>
                                <th>Cantidad</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($detalles as $detalle) : ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($detalle['producto_nombre'] ?? 'Producto #' . $detalle['id_producto']) ?></strong></td>
                                <td>$ <?= number_format($detalle['precio_unitario'], 2) ?></td>
                                <td><?= $detalle['cantidad'] ?></td>
                                <td><strong>$ <?= number_format($detalle['subtotal'], 2) ?></strong></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" class="text-end"><strong>TOTAL:</strong></td>
                                <td><strong class="text-success" style="font-size: 1.3rem;">$ <?= number_format($venta['total'], 2) ?></strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Información de la Venta</h3>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="text-muted">Fecha y Hora:</label>
                    <p class="mb-0"><strong><?= date('d/m/Y H:i:s', strtotime($venta['fecha_venta'])) ?></strong></p>
                </div>
                
                <div class="mb-3">
                    <label class="text-muted">Cliente:</label>
                    <p class="mb-0"><strong><?= htmlspecialchars($venta['cliente_nombre'] ?? 'Cliente General') ?></strong></p>
                </div>
                
                <div class="mb-3">
                    <label class="text-muted">Método de Pago:</label>
                    <p class="mb-0">
                        <?php
                        $nombres_pagos = [
                            'efectivo' => 'Efectivo',
                            'efectivo_bs' => 'Efectivo (Bs)',
                            'efectivo_usd' => 'Efectivo ($)',
                            'pago_movil' => 'Pago Móvil',
                            'tarjeta' => 'Tarjeta/Punto',
                            'transferencia' => 'Transferencia',
                            'zelle' => 'Zelle',
                            'biopago' => 'Biopago',
                            'plin' => 'Plin',
                            'yape' => 'Yape',
                            'otros' => 'Otros'
                        ];

                        // Fallback legacy
                        if (empty($pagos)) {
                            echo '<span class="badge badge-secondary">' . ($nombres_pagos[$venta['metodo_pago']] ?? $venta['metodo_pago']) . '</span>';
                        } else {
                            // Mostrar lista de pagos
                            echo '<ul class="list-unstyled mb-0">';
                            foreach ($pagos as $pago) {
                                $nombre = $nombres_pagos[$pago['metodo_pago']] ?? $pago['metodo_pago'];
                                $ref = $pago['referencia'] ? " <small class='text-muted'>(Ref: {$pago['referencia']})</small>" : "";
                                echo "<li class='mb-1'>";
                                echo "<strong>{$nombre}:</strong> $ " . number_format($pago['monto'], 2) . $ref;
                                echo "</li>";
                            }
                            echo '</ul>';
                        }
                        ?>
                    </p>
                </div>
                
                <div class="mb-3">
                    <label class="text-muted">Atendido por:</label>
                    <p class="mb-0"><strong><?= htmlspecialchars($venta['usuario_nombre'] ?? 'Usuario #' . $venta['id_usuario']) ?></strong></p>
                </div>
                
                <div class="mb-3">
                    <label class="text-muted">Sucursal:</label>
                    <p class="mb-0"><strong><?= htmlspecialchars($venta['sucursal_nombre'] ?? '-') ?></strong></p>
                </div>
                
                <hr>
                
                <div class="d-grid gap-2">
                    <a href="/ventas/ticket/<?= $venta['id'] ?>" class="btn btn-primary" target="_blank">
                        <i class="fas fa-print"></i> Imprimir Ticket
                    </a>
                    <a href="/ventas" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Volver al Listado
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
