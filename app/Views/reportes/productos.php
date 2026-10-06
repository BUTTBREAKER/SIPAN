<?php

$pageTitle = 'Reporte de Productos';
$currentPage = 'reportes';
require_once __DIR__ . '/../layouts/header.php';

$productos ??= null;
$valor_total ??= null;

?>

<div class="page-header">
    <div>
        <h2 class="page-title">Reporte de Productos</h2>
        <p class="page-subtitle">Período: <?= date('d/m/Y', strtotime($fecha_inicio)) ?> - <?= date('d/m/Y', strtotime($fecha_fin)) ?></p>
    </div>
    <div class="d-flex gap-2">
        <a href="/reportes/productos?fecha_inicio=<?= $fecha_inicio ?>&fecha_fin=<?= $fecha_fin ?>&formato=pdf" class="btn btn-danger" target="_blank">
            <i class="fas fa-file-pdf"></i> Exportar PDF
        </a>
        <a href="/reportes/productos?fecha_inicio=<?= $fecha_inicio ?>&fecha_fin=<?= $fecha_fin ?>&formato=excel" class="btn btn-success" target="_blank">
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
        <form method="GET" action="/reportes/productos" class="row g-3 align-items-end">
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
                <a href="/reportes/productos" class="btn btn-outline-secondary flex-fill">
                    <i class="fas fa-undo"></i> Limpiar
                </a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" id="tablaProductos">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Categoría</th>
                        <th>Stock</th>
                        <th>Precio</th>
                        <th>Valor Stock</th>
                        <th>Cant. Vendida</th>
                        <th>Total Generado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($productos as $prod) : ?>
                    <tr>
                        <td><?= htmlspecialchars($prod['nombre']) ?></td>
                        <td><?= htmlspecialchars($prod['categoria_nombre'] ?? '-') ?></td>
                        <td><strong><?= $prod['stock_actual'] ?></strong></td>
                        <td>$ <?= number_format($prod['precio_actual'], 2) ?></td>
                        <td>$ <?= number_format($prod['valor_stock'], 2) ?></td>
                        <td><?= $prod['total_vendido'] ?></td>
                        <td><strong class="text-success">$ <?= number_format($prod['total_generado'], 2) ?></strong></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr class="table-info">
                        <td colspan="4" class="text-end"><strong>VALOR TOTAL INVENTARIO:</strong></td>
                        <td><strong>$ <?= number_format($valor_total, 2) ?></strong></td>
                        <td></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof gridjs !== 'undefined') {
        new gridjs.Grid({
            from: document.getElementById('tablaProductos'),
            search: true,
            sort: true,
            pagination: { limit: 20 },
            language: {
                search: { placeholder: 'Buscar producto...' },
                pagination: { previous: 'Anterior', next: 'Siguiente', showing: 'Mostrando', results: () => 'resultados' }
            }
        }).render(document.getElementById('tablaProductos').parentElement);
    }
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
