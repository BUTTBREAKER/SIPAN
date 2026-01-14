<?php
$pageTitle = 'Reporte de Clientes';
$currentPage = 'reportes';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="page-header">
    <div>
        <h2 class="page-title">Reporte de Clientes</h2>
        <p class="page-subtitle">Cartera de clientes y estadísticas</p>
    </div>
    <div class="d-flex gap-2">
        <a href="/reportes/clientes?formato=pdf" class="btn btn-danger" target="_blank">
            <i class="fas fa-file-pdf"></i> Exportar PDF
        </a>
        <a href="/reportes/clientes?formato=excel" class="btn btn-success" target="_blank">
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
                        <th>Cliente</th>
                        <th>Documento</th>
                        <th>Teléfono</th>
                        <th>Compras Realizadas</th>
                        <th>Monto Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($clientes as $cli) : ?>
                    <tr>
                        <td><?= htmlspecialchars($cli['nombre']) ?></td>
                        <td><?= htmlspecialchars($cli['documento_numero'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($cli['telefono'] ?? '-') ?></td>
                        <td><?= $cli['total_compras'] ?></td>
                        <td>$ <?= number_format($cli['monto_total'], 2) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
