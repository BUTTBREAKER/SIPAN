<?php
$pageTitle = 'Detalle de Proveedor';
$currentPage = 'proveedores';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="page-header d-flex justify-between align-center">
    <div>
        <h2 class="page-title"><?= htmlspecialchars($proveedor['nombre']) ?></h2>
        <p class="page-subtitle">Información detallada y movimientos</p>
    </div>
    <div>
        <a href="/proveedores/edit/<?= $proveedor['id'] ?>" class="btn btn-warning me-2">
            <i class="fas fa-edit"></i> Editar
        </a>
        <a href="/proveedores" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Info General -->
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-info-circle"></i> Datos Generales</h3>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item">
                        <strong>RIF/DNI:</strong><br>
                        <?= htmlspecialchars($proveedor['rif'] ?? 'No registrado') ?>
                    </li>
                    <li class="list-group-item">
                        <strong>Teléfono:</strong><br>
                        <?= htmlspecialchars($proveedor['telefono'] ?? 'No registrado') ?>
                    </li>
                    <li class="list-group-item">
                        <strong>Correo:</strong><br>
                        <?= htmlspecialchars($proveedor['correo'] ?? 'No registrado') ?>
                    </li>
                    <li class="list-group-item">
                        <strong>Dirección:</strong><br>
                        <?= htmlspecialchars($proveedor['direccion'] ?? 'No registrado') ?>
                    </li>
                    <li class="list-group-item">
                         <strong>Frecuencia Visita:</strong><br>
                        <?= htmlspecialchars($proveedor['observaciones'] ?? 'No especificada') ?>
                        <!-- Aquí podrías poner días de visita si se guarda -->
                    </li>
                </ul>
            </div>
        </div>
    </div>
    
    <!-- KPIs -->
    <div class="col-md-8">
        <div class="row g-4 mb-4">
            <div class="col-md-6">
                <div class="stat-card danger">
                    <div class="stat-content">
                        <div class="stat-label">Deuda Total</div>
                        <div class="stat-value">$ <?= number_format($deuda_total, 2) ?></div>
                        <small>Monto pendiente de pago</small>
                    </div>
                    <div class="stat-icon"><i class="fas fa-money-bill-wave"></i></div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="stat-card info">
                    <div class="stat-content">
                        <div class="stat-label">Total Pedidos</div>
                        <div class="stat-value"><?= count($compras) ?></div>
                        <small>Histórico de compras</small>
                    </div>
                    <div class="stat-icon"><i class="fas fa-shopping-bag"></i></div>
                </div>
            </div>
        </div>
        
        <!-- Insumos Asociados -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title mb-0"><i class="fas fa-cubes"></i> Insumos que provee</h3>
                <a href="/insumos/create?id_proveedor=<?= $proveedor['id'] ?>" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-plus"></i> Asignar Insumo
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive" style="max-height: 250px;">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Insumo</th>
                                <th>Unidad</th>
                                <th>Precio (Ref)</th>
                                <th>Stock Actual</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($insumos)) : ?>
                                <tr><td colspan="4" class="text-center py-3 text-muted">No tiene insumos asignados directamente</td></tr>
                            <?php else : ?>
                                <?php foreach ($insumos as $ins) : ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($ins['nombre']) ?></strong></td>
                                    <td><?= $ins['unidad_medida'] ?></td>
                                    <td>$ <?= number_format($ins['precio_unitario'], 2) ?></td>
                                    <td>
                                        <span class="badge <?= $ins['stock_actual'] <= $ins['stock_minimo'] ? 'bg-danger' : 'bg-success' ?>">
                                            <?= $ins['stock_actual'] ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Historial de Pedidos / Compras -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title mb-0"><i class="fas fa-history"></i> Historial de Compras</h3>
        <a href="/compras/create?proveedor_id=<?= $proveedor['id'] ?>" class="btn btn-success">
            <i class="fas fa-cart-plus"></i> Registrar Compra
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table datatable">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Nro Compra/Ref</th>
                        <th>Registrado Por</th>
                        <th>Estado Pago</th>
                        <th>Total</th>
                        <th>Deuda</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($compras)) : ?>
                        <tr><td colspan="7" class="text-center">No hay compras registradas a este proveedor</td></tr>
                    <?php else : ?>
                        <?php foreach ($compras as $compra) : ?>
                        <tr>
                            <td><?= date('d/m/Y', strtotime($compra['fecha_compra'])) ?></td>
                            <td><?= htmlspecialchars($compra['numero_comprobante'] ?? $compra['id']) ?></td>
                            <td><?= htmlspecialchars($compra['usuario_nombre']) ?></td>
                            <td>
                                <?php
                                    $estado = $compra['estado_pago'] ?? 'pendiente';
                                    $class = match ($estado) {
                                        'pagado' => 'success',
                                        'pendiente' => 'danger',
                                        'parcial' => 'warning',
                                        default => 'secondary'
                                    };
    ?>
                                <span class="badge bg-<?= $class ?>"><?= ucfirst($estado) ?></span>
                            </td>
                            <td><strong>$ <?= number_format($compra['total'], 2) ?></strong></td>
                            <td class="text-center">
                                <?php if (($compra['monto_deuda'] ?? 0) > 0) : ?>
                                    <span class="text-danger">$ <?= number_format($compra['monto_deuda'], 2) ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="/compras/show/<?= $compra['id'] ?>" class="btn btn-sm btn-info" title="Ver Detalle">
                                    <i class="fas fa-eye"></i>
                                </a>
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
