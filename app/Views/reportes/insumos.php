<?php

$pageTitle = 'Reporte de Insumos';
$currentPage = 'reportes';
require_once __DIR__ . '/../layouts/header.php';

$insumos ??= null;

?>

<div class="page-header">
    <div>
        <h2 class="page-title">Reporte de Insumos</h2>
        <p class="page-subtitle">Período: <?= date('d/m/Y', strtotime($fecha_inicio)) ?> - <?= date('d/m/Y', strtotime($fecha_fin)) ?></p>
    </div>
    <div class="d-flex gap-2">
        <a href="/reportes/insumos?fecha_inicio=<?= $fecha_inicio ?>&fecha_fin=<?= $fecha_fin ?>&formato=pdf" class="btn btn-danger" target="_blank">
            <i class="fas fa-file-pdf"></i> Exportar PDF
        </a>
        <a href="/reportes/insumos?fecha_inicio=<?= $fecha_inicio ?>&fecha_fin=<?= $fecha_fin ?>&formato=excel" class="btn btn-success" target="_blank">
            <i class="fas fa-file-excel"></i> Exportar Excel
        </a>
        <a href="/reportes" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>
</div>

<!-- Filtros -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="/reportes/insumos" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label">Fecha Inicio</label>
                <input type="date" name="fecha_inicio" class="form-control" value="<?= $fecha_inicio ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">Fecha Fin</label>
                <input type="date" name="fecha_fin" class="form-control" value="<?= $fecha_fin ?>">
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-fill">
                    <i class="fas fa-filter"></i> Filtrar
                </button>
                <a href="/reportes/insumos" class="btn btn-outline-secondary flex-fill">
                    <i class="fas fa-undo"></i> Limpiar
                </a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" id="tablaInsumos">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Nombre</th>
                        <th>Unidad</th>
                        <th>Fecha Reg.</th>
                        <th>Stock Actual</th>
                        <th>Stock Mín.</th>
                        <th>Cant. Usada</th>
                        <th>Gasto Calculado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($insumos as $insumo) : ?>
                    <tr>
                        <td><?= htmlspecialchars($insumo['codigo'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($insumo['nombre']) ?></td>
                        <td><?= htmlspecialchars($insumo['unidad_medida']) ?></td>
                        <td><small class="text-muted"><?= isset($insumo['fecha_compra']) ? date('d/m/y', strtotime($insumo['fecha_compra'])) : '-' ?></small></td>
                        <td><strong><?= $insumo['stock_actual'] ?></strong></td>
                        <td><?= $insumo['stock_minimo'] ?></td>
                        <td><?= $insumo['cantidad_usada'] ?></td>
                        <td><strong class="text-danger">$ <?= number_format($insumo['gasto_total'], 2) ?></strong></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof gridjs !== 'undefined') {
        new gridjs.Grid({
            from: document.getElementById('tablaInsumos'),
            search: true,
            sort: true,
            pagination: { limit: 20 },
            language: {
                search: { placeholder: 'Buscar insumo...' },
                pagination: { previous: 'Anterior', next: 'Siguiente', showing: 'Mostrando', results: () => 'resultados' }
            }
        }).render(document.getElementById('tablaInsumos').parentElement);
    }
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
