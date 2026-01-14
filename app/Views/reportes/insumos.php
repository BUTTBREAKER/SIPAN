<?php
$pageTitle = 'Reporte de Insumos';
$currentPage = 'reportes';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="page-header">
    <div>
        <h2 class="page-title">Reporte de Insumos</h2>
        <p class="page-subtitle">Listado general de insumos</p>
    </div>
    <div class="d-flex gap-2">
        <a href="/reportes/insumos?formato=pdf" class="btn btn-danger" target="_blank">
            <i class="fas fa-file-pdf"></i> Exportar PDF
        </a>
        <a href="/reportes/insumos?formato=excel" class="btn btn-success" target="_blank">
            <i class="fas fa-file-excel"></i> Exportar Excel
        </a>
        <a href="/reportes" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Nombre</th>
                        <th>Unidad</th>
                        <th>Stock Actual</th>
                        <th>Stock Mínimo</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($insumos as $insumo) : ?>
                    <tr>
                        <td><?= htmlspecialchars($insumo['codigo'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($insumo['nombre']) ?></td>
                        <td><?= htmlspecialchars($insumo['unidad_medida']) ?></td>
                        <td><strong><?= $insumo['stock_actual'] ?></strong></td>
                        <td><?= $insumo['stock_minimo'] ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
