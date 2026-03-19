<?php
$pageTitle = 'Detalle del Cliente';
$currentPage = 'clientes';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="page-header d-flex justify-between align-center">
    <div>
        <h2 class="page-title">Detalle del Cliente</h2>
        <p class="page-subtitle">Información completa del cliente</p>
    </div>
    <div class="d-flex gap-2">
        <a href="/clientes" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
        <a href="/clientes/edit/<?= $cliente['id'] ?>" class="btn btn-warning">
            <i class="fas fa-edit"></i> Editar
        </a>
        <?php if ($_SESSION['user_rol'] === 'administrador') : ?>
        <button onclick="eliminarCliente(<?= $cliente['id'] ?>)" class="btn btn-danger">
            <i class="fas fa-trash"></i> Eliminar
        </button>
        <?php endif; ?>
    </div>
</div>

<?php if (isset($cliente) && !empty($cliente)) : ?>
<div class="row">
    <!-- Información Personal -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-user"></i> Información Personal
                </h3>
            </div>
            <div class="card-body">
                <div class="info-group">
                    <label class="info-label">Nombre Completo:</label>
                    <p class="info-value">
                        <strong><?= htmlspecialchars($cliente['nombre'] . ' ' . $cliente['apellido']) ?></strong>
                    </p>
                </div>
                
                <div class="info-group">
                    <label class="info-label">Tipo de Documento:</label>
                    <p class="info-value"><?= htmlspecialchars($cliente['documento_tipo'] ?? '-') ?></p>
                </div>
                
                <div class="info-group">
                    <label class="info-label">Número de Documento:</label>
                    <p class="info-value"><?= htmlspecialchars($cliente['documento_numero'] ?? '-') ?></p>
                </div>
                
                <div class="info-group">
                    <label class="info-label">Estado:</label>
                    <p class="info-value">
                        <?php if ($cliente['estado'] === 'activo') : ?>
                        <span class="badge badge-success">Activo</span>
                        <?php else : ?>
                        <span class="badge badge-danger">Inactivo</span>
                        <?php endif; ?>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Información de Contacto -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-address-book"></i> Información de Contacto
                </h3>
            </div>
            <div class="card-body">
                <div class="info-group">
                    <label class="info-label">Teléfono:</label>
                    <p class="info-value">
                        <?php if (!empty($cliente['telefono'])) : ?>
                            <i class="fas fa-phone"></i> <?= htmlspecialchars($cliente['telefono']) ?>
                        <?php else : ?>
                            <span class="text-muted">-</span>
                        <?php endif; ?>
                    </p>
                </div>
                
                <div class="info-group">
                    <label class="info-label">Correo Electrónico:</label>
                    <p class="info-value">
                        <?php if (!empty($cliente['correo'])) : ?>
                            <i class="fas fa-envelope"></i> <?= htmlspecialchars($cliente['correo']) ?>
                        <?php else : ?>
                            <span class="text-muted">-</span>
                        <?php endif; ?>
                    </p>
                </div>
                
                <div class="info-group">
                    <label class="info-label">Dirección:</label>
                    <p class="info-value"><?= htmlspecialchars($cliente['direccion'] ?? '-') ?></p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Información Adicional -->
    <?php if (isset($cliente['fecha_registro']) || isset($cliente['notas'])) : ?>
<div class="row mt-3">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-info-circle"></i> Información Adicional
                </h3>
            </div>
            <div class="card-body">
                <?php if (isset($cliente['fecha_registro'])) : ?>
                <div class="info-group">
                    <label class="info-label">Fecha de Registro:</label>
                    <p class="info-value">
                        <i class="fas fa-calendar"></i> 
                        <?= date('d/m/Y H:i', strtotime($cliente['fecha_registro'])) ?>
                    </p>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($cliente['notas'])) : ?>
                <div class="info-group">
                    <label class="info-label">Notas:</label>
                    <p class="info-value"><?= nl2br(htmlspecialchars($cliente['notas'])) ?></p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
    <?php endif; ?>

<!-- ESTADO DE CUENTA Y PEDIDOS -->
<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title text-primary"><i class="fas fa-file-invoice-dollar"></i> Estado de Cuenta & Historial de Pedidos</h3>
            </div>
            <div class="card-body">
                <?php
                // Calcular totales de los pedidos que pasamos desde el controlador
                $resumen_comprado = 0;
                $resumen_pagado = 0;
                $resumen_deuda = 0;
                
                if (isset($pedidos) && is_array($pedidos)) {
                    foreach ($pedidos as $p) {
                        $resumen_comprado += $p['total'] ?? 0;
                        $resumen_pagado += $p['monto_pagado'] ?? 0;
                        $resumen_deuda += $p['monto_deuda'] ?? 0;
                    }
                }
                ?>
                <div class="row mb-4 text-center">
                    <div class="col-md-4">
                        <div class="p-3 border rounded bg-light">
                            <h6 class="text-secondary mb-1">Total Comprado</h6>
                            <h4 class="mb-0">$ <?= number_format($resumen_comprado, 2) ?></h4>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 border rounded bg-light">
                            <h6 class="text-secondary mb-1">Total Pagado</h6>
                            <h4 class="mb-0 text-success">$ <?= number_format($resumen_pagado, 2) ?></h4>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 border rounded <?= $resumen_deuda > 0 ? 'bg-danger text-white' : 'bg-light' ?>">
                            <h6 class="<?= $resumen_deuda > 0 ? 'text-white-50' : 'text-secondary' ?> mb-1">Deuda Pendiente</h6>
                            <h4 class="mb-0 fw-bold">$ <?= number_format($resumen_deuda, 2) ?></h4>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Pedido #</th>
                                <th>Fecha</th>
                                <th>Total</th>
                                <th>Pagado</th>
                                <th>Deuda</th>
                                <th>Estado Gral</th>
                                <th>Detalle</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($pedidos)) : ?>
                                <tr>
                                    <td colspan="7" class="text-center">El cliente no tiene pedidos registrados.</td>
                                </tr>
                            <?php else : ?>
                                <?php foreach ($pedidos as $p) : ?>
                                    <tr class="<?= ($p['monto_deuda'] ?? 0) > 0 ? 'table-warning' : '' ?>">
                                        <td><strong>#<?= str_pad($p['id'], 6, '0', STR_PAD_LEFT) ?></strong></td>
                                        <td><?= date('d/m/Y H:i', strtotime($p['fecha_pedido'])) ?></td>
                                        <td>$ <?= number_format($p['total'], 2) ?></td>
                                        <td>$ <?= number_format($p['monto_pagado'] ?? 0, 2) ?></td>
                                        <td>
                                            <?php if (($p['monto_deuda'] ?? 0) > 0) : ?>
                                                <strong class="text-danger">$ <?= number_format($p['monto_deuda'], 2) ?></strong>
                                            <?php else: ?>
                                                <span class="text-success">$ 0.00</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php
                                            $badge_colors = [
                                                'pendiente' => 'warning', 'en_proceso' => 'info', 'en_camino' => 'primary',
                                                'completado' => 'success', 'entregado' => 'success',
                                                'no_entregado' => 'danger', 'cancelado' => 'danger'
                                            ];
                                            $color = $badge_colors[$p['estado_pedido']] ?? 'secondary';
                                            echo "<span class='badge bg-{$color}'>" . ucfirst(str_replace('_', ' ', $p['estado_pedido'])) . "</span>";
                                            ?>
                                        </td>
                                        <td>
                                            <a href="/pedidos/show/<?= $p['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i></a>
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

<?php else : ?>
<div class="card">
    <div class="card-body text-center">
        <i class="fas fa-exclamation-triangle" style="font-size: 48px; color: #f39c12;"></i>
        <h3 class="mt-3">Cliente no encontrado</h3>
        <p class="text-muted">El cliente que buscas no existe o ha sido eliminado.</p>
        <a href="/clientes" class="btn btn-primary mt-3">
            <i class="fas fa-arrow-left"></i> Volver a Clientes
        </a>
    </div>
</div>
<?php endif; ?>

<style>
.info-group {
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid #eee;
}

.info-group:last-child {
    border-bottom: none;
    margin-bottom: 0;
}

.info-label {
    font-weight: 600;
    color: #666;
    font-size: 0.9rem;
    margin-bottom: 0.5rem;
    display: block;
}

.info-value {
    font-size: 1rem;
    color: #333;
    margin: 0;
}

.gap-2 {
    gap: 0.5rem;
}
</style>

<script>
async function eliminarCliente(id) {
    const confirmed = await SIPAN.confirm('¿Eliminar este cliente?', '¿Estás seguro? Esta acción no se puede deshacer.');
    if (!confirmed) return;
    
    try {
        const response = await fetch(`/clientes/delete/${id}`, {
            method: 'POST'
        });
        
        const data = await response.json();
        
        if (data.success) {
            SIPAN.success(data.message);
            setTimeout(() => window.location.href = '/clientes', 1500);
        } else {
            SIPAN.error(data.message);
        }
    } catch (error) {
        SIPAN.error('Error al eliminar cliente');
    }
}
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
