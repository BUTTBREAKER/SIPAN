<?php 
$pageTitle = 'Reporte de Compras';
$currentPage = 'reportes';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="page-header">
    <div>
        <h2 class="page-title">Reporte de Compras</h2>
        <p class="page-subtitle">Historial de abastecimiento</p>
    </div>
    <div class="d-flex gap-2">
        <?php
            // Reconstruir params para mantener filtros en exportacion
            $params = http_build_query([
                'fecha_inicio' => $fecha_inicio,
                'fecha_fin' => $fecha_fin,
                'id_proveedor' => $id_proveedor
            ]);
        ?>
        <a href="/reportes/compras?<?= $params ?>&formato=pdf" class="btn btn-danger" target="_blank">
            <i class="fas fa-file-pdf"></i> PDF
        </a>
        <a href="/reportes/compras?<?= $params ?>&formato=excel" class="btn btn-success" target="_blank">
            <i class="fas fa-file-excel"></i> Excel
        </a>
        <a href="/reportes" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="/reportes/compras" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label">Fecha Inicio</label>
                <input type="date" name="fecha_inicio" class="form-control" value="<?= $fecha_inicio ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Fecha Fin</label>
                <input type="date" name="fecha_fin" class="form-control" value="<?= $fecha_fin ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Proveedor</label>
                <select name="id_proveedor" class="form-select">
                    <option value="">Todos los proveedores</option>
                    <?php foreach ($proveedores as $prov): ?>
                    <option value="<?= $prov['id'] ?>" <?= $id_proveedor == $prov['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($prov['nombre_empresa']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-search"></i> Buscar
                </button>
            </div>
        </form>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-6">
        <div class="card bg-primary text-white">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="mb-0">Total Compras</h6>
                    <h2 class="mb-0">$ <?= number_format($total_compras, 2) ?></h2>
                </div>
                <i class="fas fa-money-bill-wave fa-3x opacity-50"></i>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card bg-success text-white">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="mb-0">Proveedores Activos</h6>
                    <h2 class="mb-0"><?= count(array_unique(array_column($compras, 'id_proveedor'))) ?></h2>
                </div>
                <i class="fas fa-truck fa-3x opacity-50"></i>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Proveedor</th>
                        <th>Comprobante</th>
                        <th>Productos/Insumos</th>
                        <th>Total</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($compras)): ?>
                    <tr><td colspan="6" class="text-center text-muted">No se encontraron compras en el rango seleccionado.</td></tr>
                    <?php else: ?>
                    <?php foreach ($compras as $compra): ?>
                    <tr>
                        <td><?= date('d/m/Y H:i', strtotime($compra['fecha_compra'])) ?></td>
                        <td><?= htmlspecialchars($compra['proveedor_nombre']) ?></td>
                        <td><?= htmlspecialchars($compra['numero_comprobante']) ?></td>
                        <td>
                            <small class="text-muted">Ver detalles</small>
                        </td>
                        <td><strong>$ <?= number_format($compra['total'], 2) ?></strong></td>
                        <td>
                            <button class="btn btn-sm btn-info" title="Ver Detalle"><i class="fas fa-eye"></i></button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
