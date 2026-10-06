<?php

$pageTitle = 'Reporte de Clientes';
$currentPage = 'reportes';
require_once __DIR__ . '/../layouts/header.php';

$clientes    ??= [];
$fecha_inicio ??= date('Y-m-01');
$fecha_fin    ??= date('Y-m-d');

?>

<div class="page-header">
    <div>
        <h2 class="page-title">Reporte de Clientes</h2>
        <p class="page-subtitle">Período: <?= date('d/m/Y', strtotime($fecha_inicio)) ?> - <?= date('d/m/Y', strtotime($fecha_fin)) ?></p>
    </div>
    <div class="d-flex gap-2">
        <a href="/reportes/clientes?fecha_inicio=<?= $fecha_inicio ?>&fecha_fin=<?= $fecha_fin ?>&formato=excel" class="btn btn-success" target="_blank">
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
        <form method="GET" action="/reportes/clientes" class="row g-3 align-items-end">
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
                <a href="/reportes/clientes" class="btn btn-outline-secondary flex-fill">
                    <i class="fas fa-undo"></i> Limpiar
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Tarjetas resumen -->
<?php
$total_clientes   = count($clientes);
$clientes_activos = count(array_filter($clientes, fn($c) => ($c['monto_periodo'] + $c['monto_pedidos']) > 0));
$total_ventas_p   = array_sum(array_column($clientes, 'monto_periodo'));
$total_pedidos_p  = array_sum(array_column($clientes, 'monto_pedidos'));
$total_deuda      = array_sum(array_column($clientes, 'total_deuda'));
?>
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <i class="fas fa-users fa-2x text-primary mb-2"></i>
                <h3 class="mb-0"><?= $total_clientes ?></h3>
                <p class="text-muted mb-0">Total Clientes</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <i class="fas fa-user-check fa-2x text-success mb-2"></i>
                <h3 class="mb-0"><?= $clientes_activos ?></h3>
                <p class="text-muted mb-0">Activos en el período</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <i class="fas fa-dollar-sign fa-2x text-info mb-2"></i>
                <h3 class="mb-0">$ <?= number_format($total_ventas_p + $total_pedidos_p, 2) ?></h3>
                <p class="text-muted mb-0">Monto Total Período</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <i class="fas fa-exclamation-triangle fa-2x text-danger mb-2"></i>
                <h3 class="mb-0">$ <?= number_format($total_deuda, 2) ?></h3>
                <p class="text-muted mb-0">Deuda Total</p>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" id="tablaClientes">
                <thead>
                    <tr>
                        <th>Cliente</th>
                        <th>Teléfono</th>
                        <th>Compras (período)</th>
                        <th>Monto Ventas</th>
                        <th>Pedidos</th>
                        <th>Monto Pedidos</th>
                        <th>Deuda</th>
                        <th>Última Actividad</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($clientes as $cli) : ?>
                    <?php
                        $tiene_deuda = ($cli['total_deuda'] ?? 0) > 0;
                        $activo = ($cli['monto_periodo'] + $cli['monto_pedidos']) > 0;
                    ?>
                    <tr class="<?= $tiene_deuda ? 'table-warning' : '' ?>">
                        <td>
                            <strong><?= htmlspecialchars(trim($cli['nombre'] . ' ' . ($cli['apellido'] ?? ''))) ?></strong>
                            <?php if (!$activo): ?>
                                <br><small class="text-muted">Sin actividad</small>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($cli['telefono'] ?? '-') ?></td>
                        <td class="text-center"><?= $cli['total_compras'] ?></td>
                        <td>$ <?= number_format($cli['monto_periodo'], 2) ?></td>
                        <td class="text-center"><?= $cli['total_pedidos'] ?></td>
                        <td>$ <?= number_format($cli['monto_pedidos'], 2) ?></td>
                        <td>
                            <?php if ($tiene_deuda): ?>
                                <strong class="text-danger">$ <?= number_format($cli['total_deuda'], 2) ?></strong>
                            <?php else: ?>
                                <span class="text-success"><i class="fas fa-check-circle"></i> Al día</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <small class="text-muted">
                                <?= ($cli['ultima_actividad'] && $cli['ultima_actividad'] > '2000-01-02')
                                    ? date('d/m/Y', strtotime($cli['ultima_actividad']))
                                    : '-' ?>
                            </small>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
