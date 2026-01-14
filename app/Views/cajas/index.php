<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h2 class="h4 mb-0 text-dark fw-bold">Gestión de Caja Chica</h2>
            <div class="d-flex gap-2">
                <?php if ($cajaActiva) : ?>
                    <a href="/cajas/movimientos" class="btn btn-primary d-flex align-items-center">
                        <i class="fas fa-exchange-alt me-2"></i> Nuevo Movimiento
                    </a>
                    <a href="/cajas/cerrar" class="btn btn-danger d-flex align-items-center">
                        <i class="fas fa-lock me-2"></i> Cerrar Caja
                    </a>
                <?php else : ?>
                    <a href="/cajas/aprir" class="btn btn-success d-flex align-items-center">
                        <i class="fas fa-unlock me-2"></i> Abrir Caja
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php if ($cajaActiva) : ?>
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm overflow-hidden h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="icon-shape bg-soft-success text-success rounded-circle me-3">
                            <i class="fas fa-door-open"></i>
                        </div>
                        <h6 class="mb-0 text-muted">Apertura</h6>
                    </div>
                    <h3 class="mb-0 fw-bold"><?php echo SIPAN::formatMoney($cajaActiva['monto_apertura']); ?></h3>
                    <div class="d-flex justify-content-between mt-1 mb-2">
                        <small class="text-muted">$ <?php echo number_format($cajaActiva['monto_apertura_usd'] ?? 0, 2); ?></small>
                        <small class="text-muted">Bs <?php echo number_format($cajaActiva['monto_apertura_bs'] ?? 0, 2); ?></small>
                    </div>
                    <small class="text-muted d-block border-top pt-1"><?php echo SIPAN::formatDateTime($cajaActiva['fecha_apertura']); ?></small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm overflow-hidden h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="icon-shape bg-soft-info text-info rounded-circle me-3">
                            <i class="fas fa-arrow-up"></i>
                        </div>
                        <h6 class="mb-0 text-muted">Ingresos</h6>
                    </div>
                    <?php
                        $resumen = (new \App\Models\Caja())->getResumen($cajaActiva['id']);
                    ?>
                    <h3 class="mb-0 fw-bold text-success">+ <?php echo SIPAN::formatMoney($resumen['ingresos']); ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm overflow-hidden h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="icon-shape bg-soft-danger text-danger rounded-circle me-3">
                            <i class="fas fa-arrow-down"></i>
                        </div>
                        <h6 class="mb-0 text-muted">Egresos</h6>
                    </div>
                    <h3 class="mb-0 fw-bold text-danger">- <?php echo SIPAN::formatMoney($resumen['egresos']); ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm overflow-hidden h-100 bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="icon-shape bg-white-transparent text-white rounded-circle me-3">
                            <i class="fas fa-wallet"></i>
                        </div>
                        <h6 class="mb-0 opacity-75">Saldo Actual</h6>
                    </div>
                    <h3 class="mb-0 fw-bold"><?php echo SIPAN::formatMoney($resumen['esperado']); ?></h3>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title mb-0">Historial de Cajas</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light text-muted">
                                <tr>
                                    <th class="px-4 py-3 border-0">Fecha Apertura</th>
                                    <th class="py-3 border-0">Apertura</th>
                                    <th class="py-3 border-0">Cierre</th>
                                    <th class="py-3 border-0">Esperado</th>
                                    <th class="py-3 border-0">Estado</th>
                                    <th class="px-4 py-3 border-0 text-end">Usuario</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($historial as $h) : ?>
                                <tr>
                                    <td class="px-4 py-3 border-0">
                                        <div class="d-flex flex-column">
                                            <span class="fw-medium text-capitalize"><?php echo SIPAN::formatDateTime($h['fecha_apertura']); ?></span>
                                        </div>
                                    </td>
                                    <td class="py-3 border-0">
                                        <div class="d-flex flex-column">
                                            <span><?php echo SIPAN::formatMoney($h['monto_apertura']); ?></span>
                                            <small class="text-muted" style="font-size: 0.75rem;">
                                                $<?php echo number_format($h['monto_apertura_usd'] ?? 0, 2); ?> | Bs<?php echo number_format($h['monto_apertura_bs'] ?? 0, 2); ?>
                                            </small>
                                        </div>
                                    </td>
                                    <td class="py-3 border-0">
                                        <?php if ($h['monto_cierre'] !== null) : ?>
                                            <div class="d-flex flex-column">
                                                <span><?php echo SIPAN::formatMoney($h['monto_cierre']); ?></span>
                                                <small class="text-muted" style="font-size: 0.75rem;">
                                                    $<?php echo number_format($h['monto_cierre_usd'] ?? 0, 2); ?> | Bs<?php echo number_format($h['monto_cierre_bs'] ?? 0, 2); ?>
                                                </small>
                                            </div>
                                        <?php else : ?>
                                            ---
                                        <?php endif; ?>
                                    </td>
                                    <td class="py-3 border-0">
                                        <?php echo $h['monto_esperado'] !== null ? SIPAN::formatMoney($h['monto_esperado']) : '---'; ?>
                                    </td>
                                    <td class="py-3 border-0">
                                        <?php if ($h['estado'] === 'abierta') : ?>
                                            <span class="badge bg-soft-success text-success px-3 py-2">ABIERTA</span>
                                        <?php else : ?>
                                            <span class="badge bg-soft-secondary text-secondary px-3 py-2">CERRADA</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-4 py-3 border-0 text-end">
                                        <div class="d-flex align-items-center justify-content-end">
                                            <div class="avatar-sm bg-soft-primary text-primary rounded-circle me-2 d-flex align-items-center justify-content-center">
                                                <?php echo strtoupper(substr($h['usuario_apertura'], 0, 1)); ?>
                                            </div>
                                            <span><?php echo $h['usuario_apertura']; ?></span>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.icon-shape {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
}
.bg-soft-success { background-color: rgba(40, 167, 69, 0.1); }
.bg-soft-info { background-color: rgba(23, 162, 184, 0.1); }
.bg-soft-danger { background-color: rgba(220, 53, 69, 0.1); }
.bg-soft-primary { background-color: rgba(13, 110, 253, 0.1); }
.bg-soft-secondary { background-color: rgba(108, 117, 125, 0.1); }
.bg-white-transparent { background-color: rgba(255, 255, 255, 0.2); }
.avatar-sm { width: 32px; height: 32px; font-weight: bold; font-size: 0.8rem; }
</style>

<?php require_once __DIR__ . '/../layouts/footer.php';
