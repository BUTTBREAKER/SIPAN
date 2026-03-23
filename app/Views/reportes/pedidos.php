<?php
$pageTitle = 'Reporte de Pedidos';
$currentPage = 'reportes';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="page-header">
    <div>
        <h2 class="page-title">Reporte de Pedidos</h2>
        <p class="page-subtitle">Período: <?= date('d/m/Y', strtotime($fecha_inicio)) ?> - <?= date('d/m/Y', strtotime($fecha_fin)) ?></p>
    </div>
    <div class="d-flex gap-2">
        <a href="/reportes/pedidos?fecha_inicio=<?= $fecha_inicio ?>&fecha_fin=<?= $fecha_fin ?>&formato=pdf" class="btn btn-danger" target="_blank">
            <i class="fas fa-file-pdf"></i> Exportar PDF
        </a>
        <a href="/reportes/pedidos?fecha_inicio=<?= $fecha_inicio ?>&fecha_fin=<?= $fecha_fin ?>&formato=excel" class="btn btn-success" target="_blank">
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
        <form method="GET" action="/reportes/pedidos" class="row g-3 align-items-end">
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
                <a href="/reportes/pedidos" class="btn btn-outline-secondary flex-fill">
                    <i class="fas fa-undo"></i> Limpiar
                </a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>N° Pedido</th>
                        <th>Fecha</th>
                        <th>Cliente</th>
                        <th>Total</th>
                        <th>Estado</th>
                        <th>Pago</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pedidos as $p) : ?>
                    <tr>
                        <td><?= htmlspecialchars($p['numero_pedido']) ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($p['fecha_pedido'])) ?></td>
                        <td><?= htmlspecialchars($p['cliente_nombre'] . ' ' . $p['cliente_apellido']) ?></td>
                        <td><strong>$ <?= number_format($p['total'], 2) ?></strong></td>
                        <td><?= ucfirst($p['estado_pedido']) ?></td>
                        <td><?= ucfirst($p['estado_pago']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
