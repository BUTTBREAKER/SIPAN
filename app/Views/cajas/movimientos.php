<?php require_once '../app/Views/layouts/header.php'; ?>

<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1">
                        <li class="breadcrumb-item"><a href="/cajas" class="text-decoration-none">Caja Chica</a></li>
                        <li class="breadcrumb-item active">Movimientos</li>
                    </ol>
                </nav>
                <h2 class="h4 mb-0 text-dark fw-bold">Movimientos de Caja</h2>
            </div>
            <button class="btn btn-primary d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#movimientoModal">
                <i class="fas fa-plus me-2"></i> Registrar Movimiento
            </button>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light text-muted">
                                <tr>
                                    <th class="px-4 py-3 border-0">Hora</th>
                                    <th class="py-3 border-0">Tipo</th>
                                    <th class="py-3 border-0">Descripción</th>
                                    <th class="py-3 border-0">Método</th>
                                    <th class="py-3 border-0">Monto</th>
                                    <th class="px-4 py-3 border-0 text-end">Venta Ref.</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($movimientos)) : ?>
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        <i class="fas fa-info-circle fa-2x mb-3 d-block opacity-25"></i>
                                        No hay movimientos registrados en esta caja.
                                    </td>
                                </tr>
                                <?php endif; ?>
                                <?php foreach ($movimientos as $m) : ?>
                                <tr>
                                    <td class="px-4 py-3 border-0 text-muted">
                                        <?php echo date('H:i', strtotime($m['fecha'])); ?>
                                    </td>
                                    <td class="py-3 border-0">
                                        <?php if ($m['tipo'] === 'ingreso') : ?>
                                            <span class="badge bg-soft-success text-success">
                                                <i class="fas fa-arrow-up me-1"></i> INGRESO
                                            </span>
                                        <?php else : ?>
                                            <span class="badge bg-soft-danger text-danger">
                                                <i class="fas fa-arrow-down me-1"></i> EGRESO
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="py-3 border-0 fw-medium"><?php echo $m['descripcion']; ?></td>
                                    <td class="py-3 border-0 text-muted text-capitalize"><?php echo $m['metodo_pago']; ?></td>
                                    <td class="py-3 border-0 fw-bold <?php echo $m['tipo'] === 'ingreso' ? 'text-success' : 'text-danger'; ?>">
                                        <?php echo ($m['tipo'] === 'ingreso' ? '+' : '-') . ' ' . SIPAN::formatMoney($m['monto']); ?>
                                    </td>
                                    <td class="px-4 py-3 border-0 text-end">
                                        <?php if ($m['id_venta']) : ?>
                                            <span class="text-muted">#<?php echo $m['id_venta']; ?></span>
                                        <?php else : ?>
                                            <span class="text-muted opacity-25">---</span>
                                        <?php endif; ?>
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

<!-- Modal Registrar Movimiento -->
<div class="modal fade" id="movimientoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Nuevo Movimiento de Caja</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="/cajas/movimientos" method="POST">
                <input type="hidden" name="id_caja" value="<?php echo $caja['id']; ?>">
                <div class="modal-body p-4">
                    <div class="mb-4">
                        <label class="form-label text-muted fw-medium">Tipo de Movimiento</label>
                        <div class="d-flex gap-2">
                            <input type="radio" class="btn-check" name="tipo" id="tipo_ingreso" value="ingreso" checked>
                            <label class="btn btn-outline-success flex-fill border-2 py-2 fw-bold" for="tipo_ingreso">
                                <i class="fas fa-arrow-up me-2"></i> Ingreso
                            </label>

                            <input type="radio" class="btn-check" name="tipo" id="tipo_egreso" value="egreso">
                            <label class="btn btn-outline-danger flex-fill border-2 py-2 fw-bold" for="tipo_egreso">
                                <i class="fas fa-arrow-down me-2"></i> Egreso
                            </label>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label text-muted fw-medium">Concepto / Descripción</label>
                        <input type="text" name="descripcion" class="form-control bg-light border-0" placeholder="Ej: Pago de propina, Vuelto..." required>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label text-muted fw-medium">Monto</label>
                            <div class="input-group border rounded-2 overflow-hidden">
                                <span class="input-group-text bg-light border-0">$</span>
                                <input type="number" step="0.01" name="monto" class="form-control border-0 bg-light" placeholder="0.00" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted fw-medium">Método</label>
                            <select name="metodo_pago" class="form-select bg-light border-0">
                                <option value="efectivo">Efectivo</option>
                                <option value="transferencia">Transferencia</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 p-4">
                    <button type="button" class="btn btn-link text-muted fw-bold text-decoration-none" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary px-4 fw-bold shadow-sm">Registrar Movimiento</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.bg-soft-success { background-color: rgba(40, 167, 69, 0.1); }
.bg-soft-danger { background-color: rgba(220, 53, 69, 0.1); }
.btn-outline-success:checked + label { background-color: #28a745; color: white; }
.btn-outline-danger:checked + label { background-color: #dc3545; color: white; }
</style>

<?php require_once '../app/Views/layouts/footer.php'; ?>
