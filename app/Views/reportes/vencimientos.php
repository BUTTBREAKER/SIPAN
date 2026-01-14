<?php
$pageTitle = 'Reporte de Vencimientos';
$currentPage = 'reportes';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="page-header">
    <div>
        <h2 class="page-title">Reporte de Vencimientos</h2>
        <p class="page-subtitle">Lotes próximos a vencer</p>
    </div>
    <div class="d-flex gap-2">
        <a href="/reportes/vencimientos?dias=<?= $dias ?>&formato=excel" class="btn btn-success" target="_blank">
            <i class="fas fa-file-excel"></i> Exportar Excel
        </a>
        <a href="/reportes" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="/reportes/vencimientos" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label">Días para vencer</label>
                <select name="dias" class="form-select">
                    <option value="15" <?= $dias == 15 ? 'selected' : '' ?>>Próximos 15 días</option>
                    <option value="30" <?= $dias == 30 ? 'selected' : '' ?>>Próximos 30 días</option>
                    <option value="60" <?= $dias == 60 ? 'selected' : '' ?>>Próximos 60 días</option>
                    <option value="90" <?= $dias == 90 ? 'selected' : '' ?>>Próximos 90 días</option>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-filter"></i> Filtrar
                </button>
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
                        <th>Código Lote</th>
                        <th>Item (Insumo/Producto)</th>
                        <th>Fecha Entrada</th>
                        <th>Fecha Vencimiento</th>
                        <th>Días Restantes</th>
                        <th>Cantidad Inicial</th>
                        <th>Stock Actual</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($lotes)) : ?>
                    <tr><td colspan="8" class="text-center text-muted">No se encontraron lotes por vencer en el rango seleccionado.</td></tr>
                    <?php else : ?>
                        <?php foreach ($lotes as $lote) :
                            $fecha_venc = strtotime($lote['fecha_vencimiento']);
                            $hoy = time();
                            $dias_restantes = ceil(($fecha_venc - $hoy) / 86400);
                            $clase_estado = $dias_restantes <= 0 ? 'bg-danger text-white' : ($dias_restantes <= 15 ? 'bg-warning' : 'bg-success text-white');
                            $texto_estado = $dias_restantes <= 0 ? 'Vencido' : ($dias_restantes <= 15 ? 'Crítico' : 'Ok');
                            ?>
                    <tr>
                        <td><?= htmlspecialchars($lote['codigo_lote']) ?></td>
                        <td>
                            <strong><?= htmlspecialchars($lote['nombre_item']) ?></strong>
                            <small class="d-block text-muted"><?= ucfirst($lote['tipo']) ?></small>
                        </td>
                        <td><?= date('d/m/Y', strtotime($lote['fecha_entrada'])) ?></td>
                        <td><strong><?= date('d/m/Y', strtotime($lote['fecha_vencimiento'])) ?></strong></td>
                        <td>
                            <span class="badge <?= $dias_restantes <= 7 ? 'bg-danger' : 'bg-info' ?>">
                                <?= $dias_restantes ?> días
                            </span>
                        </td>
                        <td><?= $lote['cantidad_inicial'] ?></td>
                        <td><strong><?= $lote['cantidad_actual'] ?></strong></td>
                        <td><span class="badge <?= $clase_estado ?>"><?= $texto_estado ?></span></td>
                    </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
