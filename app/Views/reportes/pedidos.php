<?php

$pageTitle = 'Reporte de Pedidos';
$currentPage = 'reportes';
require_once __DIR__ . '/../layouts/header.php';

$fecha_inicio    ??= date('Y-m-01');
$fecha_fin       ??= date('Y-m-d');
$pedidos         ??= [];
$resumen_estados ??= [];
$repartidores    ??= [];
$total_monto     ??= 0;
$total_deuda     ??= 0;
$estado_filtro   ??= null;
$pago_filtro     ??= null;

// Agrupar resumen
$por_estado = [];
foreach ($resumen_estados as $r) {
    $key = $r['estado_pedido'];
    if (!isset($por_estado[$key])) {
        $por_estado[$key] = ['total' => 0, 'monto' => 0, 'deuda' => 0];
    }
    $por_estado[$key]['total'] += $r['total'];
    $por_estado[$key]['monto'] += $r['monto'];
    $por_estado[$key]['deuda'] += $r['deuda'];
}

$badges = [
    'pendiente'  => 'warning',
    'en_proceso' => 'info',
    'listo'      => 'primary',
    'entregado'  => 'success',
    'cancelado'  => 'danger',
];
?>

<div class="page-header">
    <div>
        <h2 class="page-title">Reporte de Pedidos</h2>
        <p class="page-subtitle">Período: <?= date('d/m/Y', strtotime($fecha_inicio)) ?> - <?= date('d/m/Y', strtotime($fecha_fin)) ?></p>
    </div>
    <div class="d-flex gap-2">
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
            <div class="col-md-3">
                <label class="form-label">Fecha Inicio</label>
                <input type="date" name="fecha_inicio" class="form-control" value="<?= $fecha_inicio ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Fecha Fin</label>
                <input type="date" name="fecha_fin" class="form-control" value="<?= $fecha_fin ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label">Estado Pedido</label>
                <select name="estado_pedido" class="form-control">
                    <option value="">Todos</option>
                    <option value="pendiente"  <?= $estado_filtro === 'pendiente'  ? 'selected' : '' ?>>Pendiente</option>
                    <option value="en_proceso" <?= $estado_filtro === 'en_proceso' ? 'selected' : '' ?>>En Proceso</option>
                    <option value="listo"      <?= $estado_filtro === 'listo'      ? 'selected' : '' ?>>Listo</option>
                    <option value="entregado"  <?= $estado_filtro === 'entregado'  ? 'selected' : '' ?>>Entregado</option>
                    <option value="cancelado"  <?= $estado_filtro === 'cancelado'  ? 'selected' : '' ?>>Cancelado</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Estado Pago</label>
                <select name="estado_pago" class="form-control">
                    <option value="">Todos</option>
                    <option value="pendiente" <?= $pago_filtro === 'pendiente' ? 'selected' : '' ?>>Pendiente</option>
                    <option value="parcial"   <?= $pago_filtro === 'parcial'   ? 'selected' : '' ?>>Parcial</option>
                    <option value="pagado"    <?= $pago_filtro === 'pagado'    ? 'selected' : '' ?>>Pagado</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-fill">
                    <i class="fas fa-filter"></i> Filtrar
                </button>
                <a href="/reportes/pedidos" class="btn btn-outline-secondary flex-fill">
                    <i class="fas fa-undo"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Tarjetas de resumen -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <i class="fas fa-box fa-2x text-primary mb-2"></i>
                <h3 class="mb-0"><?= count($pedidos) ?></h3>
                <p class="text-muted mb-0">Total Pedidos</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <i class="fas fa-dollar-sign fa-2x text-success mb-2"></i>
                <h3 class="mb-0">$ <?= number_format($total_monto, 2) ?></h3>
                <p class="text-muted mb-0">Monto Total</p>
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
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <i class="fas fa-motorcycle fa-2x text-warning mb-2"></i>
                <h3 class="mb-0"><?= count($repartidores) ?></h3>
                <p class="text-muted mb-0">Repartidores</p>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <!-- Resumen por estado -->
    <div class="col-md-7">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="fas fa-chart-pie me-2"></i>Resumen por Estado</h5>
            </div>
            <div class="card-body p-0">
                <table class="table mb-0">
                    <thead><tr><th>Estado</th><th>Cantidad</th><th>Monto</th><th>Deuda</th></tr></thead>
                    <tbody>
                        <?php foreach ($por_estado as $est => $info): ?>
                        <tr>
                            <td><span class="badge bg-<?= $badges[$est] ?? 'secondary' ?>"><?= ucfirst(str_replace('_', ' ', $est)) ?></span></td>
                            <td><?= $info['total'] ?></td>
                            <td>$ <?= number_format($info['monto'], 2) ?></td>
                            <td><?= $info['deuda'] > 0 ? '<strong class="text-danger">$ '.number_format($info['deuda'], 2).'</strong>' : '-' ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Tabla repartidores -->
    <?php if (!empty($repartidores)): ?>
    <div class="col-md-5">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="fas fa-motorcycle me-2"></i>Repartidores</h5>
            </div>
            <div class="card-body p-0">
                <table class="table mb-0">
                    <thead><tr><th>Repartidor</th><th>Pedidos</th><th>Entregados</th></tr></thead>
                    <tbody>
                        <?php foreach ($repartidores as $rep): ?>
                        <tr>
                            <td><?= htmlspecialchars($rep['repartidor']) ?></td>
                            <td><?= $rep['total_pedidos'] ?></td>
                            <td><span class="badge bg-success"><?= $rep['entregados'] ?></span></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Tabla principal de pedidos -->
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">Detalle de Pedidos</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" id="tablaPedidos">
                <thead>
                    <tr>
                        <th>N° Pedido</th>
                        <th>Fecha</th>
                        <th>Cliente</th>
                        <th>Total</th>
                        <th>Deuda</th>
                        <th>Estado</th>
                        <th>Pago</th>
                        <th>Repartidor</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pedidos as $p) : ?>
                    <?php $con_deuda = ($p['monto_deuda'] ?? 0) > 0; ?>
                    <tr class="<?= $con_deuda ? 'table-warning' : '' ?>">
                        <td><strong><?= htmlspecialchars($p['numero_pedido']) ?></strong></td>
                        <td><?= date('d/m/Y H:i', strtotime($p['fecha_pedido'])) ?></td>
                        <td><?= htmlspecialchars($p['cliente_nombre'] . ' ' . $p['cliente_apellido']) ?></td>
                        <td>$ <?= number_format($p['total'], 2) ?></td>
                        <td>
                            <?php if ($con_deuda): ?>
                                <strong class="text-danger">$ <?= number_format($p['monto_deuda'], 2) ?></strong>
                            <?php else: ?>
                                <span class="text-success">-</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="badge bg-<?= $badges[$p['estado_pedido']] ?? 'secondary' ?>">
                                <?= ucfirst(str_replace('_', ' ', $p['estado_pedido'])) ?>
                            </span>
                        </td>
                        <td><?= ucfirst($p['estado_pago']) ?></td>
                        <td>
                            <?php if (!empty($p['rep_nombre'])): ?>
                                <small><?= htmlspecialchars($p['rep_nombre'] . ' ' . $p['rep_apellido']) ?></small>
                            <?php else: ?>
                                <small class="text-muted">-</small>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
