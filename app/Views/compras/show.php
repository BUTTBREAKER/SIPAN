<?php 
$pageTitle = 'Detalle de Compra #' . $compra['id'];
$currentPage = 'compras';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h2 class="page-title">Compra #<?= $compra['id'] ?></h2>
        <p class="page-subtitle">Detalle completo de la compra</p>
    </div>
    <div>
        <a href="/compras" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>
</div>

<div class="row">
    <!-- Información General -->
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h3 class="card-title h6 mb-0"><i class="fas fa-info-circle"></i> Información General</h3>
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless">
                    <tr>
                        <td class="text-muted">Fecha de Compra:</td>
                        <td class="fw-bold"><?= date('d/m/Y H:i', strtotime($compra['fecha_compra'])) ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted">N° Comprobante:</td>
                        <td class="fw-bold"><?= htmlspecialchars($compra['numero_comprobante']) ?: '-' ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Estado:</td>
                        <td>
                            <?php
                            $badgeClass = 'bg-success';
                            if ($compra['estado'] === 'pendiente') $badgeClass = 'bg-warning';
                            if ($compra['estado'] === 'anulada') $badgeClass = 'bg-danger';
                            ?>
                            <span class="badge <?= $badgeClass ?>"><?= ucfirst($compra['estado']) ?></span>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">Registrado por:</td>
                        <td class="fw-bold"><?= htmlspecialchars($compra['usuario_nombre']) ?></td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Información del Proveedor -->
        <div class="card">
            <div class="card-header bg-light">
                <h3 class="card-title h6 mb-0"><i class="fas fa-truck"></i> Proveedor</h3>
            </div>
            <div class="card-body">
                <h5 class="mb-2"><?= htmlspecialchars($compra['proveedor_nombre'] ?? 'Sin especificar') ?></h5>
                <?php if (!empty($compra['proveedor_telefono'])): ?>
                <p class="mb-0 text-muted">
                    <i class="fas fa-phone"></i> <?= htmlspecialchars($compra['proveedor_telefono']) ?>
                </p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Detalle de Items -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-light">
                <h3 class="card-title h6 mb-0"><i class="fas fa-list"></i> Detalle de Items</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Item</th>
                                <th>Tipo</th>
                                <th>Lote / Vencimiento</th>
                                <th class="text-end">Cantidad</th>
                                <th class="text-end">Costo Unit.</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($detalles as $detalle): ?>
                            <tr>
                                <td>
                                    <strong><?= htmlspecialchars($detalle['item_nombre']) ?></strong>
                                </td>
                                <td>
                                    <span class="badge bg-primary"><?= ucfirst($detalle['tipo_item']) ?></span>
                                </td>
                                <td>
                                    <?php if ($detalle['lote_codigo']): ?>
                                        <small class="d-block"><strong>Lote:</strong> <?= htmlspecialchars($detalle['lote_codigo']) ?></small>
                                    <?php endif; ?>
                                    <?php if ($detalle['fecha_vencimiento']): ?>
                                        <small class="d-block text-danger">
                                            <strong>Vence:</strong> <?= date('d/m/Y', strtotime($detalle['fecha_vencimiento'])) ?>
                                        </small>
                                    <?php endif; ?>
                                    <?php if (!$detalle['lote_codigo'] && !$detalle['fecha_vencimiento']): ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <?= number_format($detalle['cantidad'], 2) ?> 
                                    <small class="text-muted"><?= $detalle['unidad_medida'] ?></small>
                                </td>
                                <td class="text-end">$ <?= number_format($detalle['costo_unitario'], 2) ?></td>
                                <td class="text-end fw-bold">$ <?= number_format($detalle['subtotal'], 2) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="5" class="text-end"><strong>TOTAL:</strong></td>
                                <td class="text-end">
                                    <h5 class="mb-0 text-primary">$ <?= number_format($compra['total'], 2) ?></h5>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
