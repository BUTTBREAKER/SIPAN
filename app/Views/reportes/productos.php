<?php 
$pageTitle = 'Reporte de Productos';
$currentPage = 'reportes';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="page-header">
    <div>
        <h2 class="page-title">Reporte de Productos</h2>
        <p class="page-subtitle">Inventario valorizado</p>
    </div>
    <div class="d-flex gap-2">
        <a href="/reportes/productos?formato=pdf" class="btn btn-danger" target="_blank">
            <i class="fas fa-file-pdf"></i> Exportar PDF
        </a>
        <a href="/reportes/productos?formato=excel" class="btn btn-success" target="_blank">
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
                        <th>Producto</th>
                        <th>Categoría</th>
                        <th>Stock</th>
                        <th>Precio</th>
                        <th>Valor Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($productos as $prod): ?>
                    <tr>
                        <td><?= htmlspecialchars($prod['nombre']) ?></td>
                        <td><?= htmlspecialchars($prod['categoria_nombre'] ?? '-') ?></td>
                        <td><strong><?= $prod['stock_actual'] ?></strong></td>
                        <td>$ <?= number_format($prod['precio_actual'], 2) ?></td>
                        <td>$ <?= number_format($prod['valor_stock'], 2) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr class="table-info">
                        <td colspan="4" class="text-end"><strong>VALOR TOTAL INVENTARIO:</strong></td>
                        <td><strong>$ <?= number_format($valor_total, 2) ?></strong></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
