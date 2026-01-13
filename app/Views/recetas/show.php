<?php 
$pageTitle = 'Detalle de Receta';
$currentPage = 'recetas';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="page-header">
    <h2 class="page-title">Detalle de Receta: <?= htmlspecialchars($producto['nombre'] ?? 'Desconocido') ?></h2>
</div>

<div class="row">
    <!-- Información General y Costos -->
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h3 class="card-title text-white"> <i class="fas fa-calculator"></i> Análisis de Costos</h3>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Rendimiento
                        <span class="badge bg-secondary rounded-pill"><?= $receta['rendimiento'] ?> Unidades</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Costo Total Receta
                        <span class="fw-bold">$ <?= number_format($costo_total_receta, 2) ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center list-group-item-warning">
                        <strong>Costo Unitario</strong>
                        <strong class="text-dark">$ <?= number_format($costo_unitario_produccion, 2) ?></strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Precio Venta
                        <span class="text-muted">$ <?= number_format($precio_venta, 2) ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center <?= $margen_unitario > 0 ? 'list-group-item-success' : 'list-group-item-danger' ?>">
                        <strong>Margen Unitario</strong>
                        <strong>$ <?= number_format($margen_unitario, 2) ?> (<?= number_format($margen_porcentaje, 1) ?>%)</strong>
                    </li>
                </ul>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Instrucciones</h3>
            </div>
            <div class="card-body">
                <p><?= nl2br(htmlspecialchars($receta['instrucciones'] ?? 'Sin instrucciones especificadas.')) ?></p>
            </div>
        </div>
    </div>

    <!-- Lista de Insumos -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title">Insumos Requeridos</h3>
                <div>
                    <a href="/recetas/edit/<?= $receta['id'] ?>" class="btn btn-warning btn-sm">
                        <i class="fas fa-edit"></i> Editar Receta
                    </a>
                    <a href="/recetas" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Insumo</th>
                                <th>Cantidad</th>
                                <th>Costo Unit. (Ref)</th>
                                <th>Costo Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($detalles_insumos as $insumo): ?>
                            <tr>
                                <td><?= htmlspecialchars($insumo['nombre'] ?? $insumo['nombre_insumo']) ?></td>
                                <td><?= $insumo['cantidad'] ?> <?= $insumo['unidad_medida'] ?? '' ?></td>
                                <td>$ <?= number_format($insumo['costo_unitario'], 2) ?></td>
                                <td>$ <?= number_format($insumo['subtotal_costo'], 2) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr class="table-active">
                                <td colspan="3" class="text-end"><strong>TOTAL DE COSTOS:</strong></td>
                                <td><strong>$ <?= number_format($costo_total_receta, 2) ?></strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="alert alert-info mt-3">
                    <i class="fas fa-info-circle"></i> Los costos se calculan en base al "Costo Unitario" registrado actualmente en la ficha de cada insumo.
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
