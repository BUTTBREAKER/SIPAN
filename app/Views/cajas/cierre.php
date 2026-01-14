<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-lg overflow-hidden glass-card">
                <div class="card-header bg-danger text-white py-4 text-center">
                    <div class="icon-circle bg-white text-danger mb-3 mx-auto d-flex align-items-center justify-content-center">
                        <i class="fas fa-lock fa-2x"></i>
                    </div>
                    <h3 class="fw-bold mb-0">Cierre de Caja</h3>
                    <p class="mb-0 opacity-75">Finaliza el turno y verifica el saldo</p>
                </div>
                <div class="card-body p-4 p-md-5">
                    <div class="row mb-5 text-center">
                        <div class="col-md-4">
                            <small class="text-muted d-block mb-1">Monto Inicial</small>
                            <h5 class="fw-bold"><?php echo SIPAN::formatMoney($resumen['apertura']); ?></h5>
                        </div>
                        <div class="col-md-4 border-start border-end">
                            <small class="text-muted d-block mb-1">Ventas/Ingresos</small>
                            <h5 class="fw-bold text-success">+ <?php echo SIPAN::formatMoney($resumen['ingresos']); ?></h5>
                        </div>
                        <div class="col-md-4">
                            <small class="text-muted d-block mb-1">Egresos/Gastos</small>
                            <h5 class="fw-bold text-danger">- <?php echo SIPAN::formatMoney($resumen['egresos']); ?></h5>
                        </div>
                    </div>

                    <div class="alert alert-primary border-0 rounded-4 p-4 mb-5 text-center">
                        <h6 class="text-uppercase fw-bold opacity-75 mb-2">Saldo Esperado en Caja</h6>
                        <h2 class="display-6 fw-bold mb-0"><?php echo SIPAN::formatMoney($resumen['esperado']); ?></h2>
                    </div>

                    <form action="/cajas/cerrar" method="POST" id="cierreForm">
                        <input type="hidden" name="id_caja" value="<?php echo $caja['id']; ?>">
                        
                        <div class="mb-4">
                            <label class="form-label text-muted fw-medium">Efectivo Real en Dólares ($)</label>
                            <div class="input-group input-group-lg border rounded-3 overflow-hidden">
                                <span class="input-group-text bg-light border-0"><i class="fas fa-dollar-sign text-danger"></i></span>
                                <input type="number" step="0.01" name="monto_usd" class="form-control border-0 bg-light" placeholder="0.00" required autofocus>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label text-muted fw-medium">Efectivo Real en Bolívares (Bs)</label>
                            <div class="input-group input-group-lg border rounded-3 overflow-hidden">
                                <span class="input-group-text bg-light border-0"><span class="fw-bold text-danger">Bs</span></span>
                                <input type="number" step="0.01" name="monto_bs" class="form-control border-0 bg-light" placeholder="0.00" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label text-muted fw-medium">Observaciones / Novedades</label>
                            <textarea name="observaciones" class="form-control bg-light border-0 rounded-3" rows="3" placeholder="Ej: Faltó sencillo, se pagó propina..."></textarea>
                        </div>

                        <div class="d-grid gap-3">
                            <button type="submit" class="btn btn-danger btn-lg py-3 fw-bold shadow-sm">
                                <i class="fas fa-check-double me-2"></i> Confirmar y Cerrar Turno
                            </button>
                            <a href="/cajas" class="btn btn-link text-muted py-2">
                                <i class="fas fa-arrow-left me-2"></i> Volver sin Cerrar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.glass-card {
    background: rgba(255, 255, 255, 0.9);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 20px;
}
.icon-circle {
    width: 80px;
    height: 80px;
    border-radius: 50%;
}
.form-control:focus {
    box-shadow: none;
    background-color: #fff !important;
}
.input-group:focus-within {
    border-color: #dc3545 !important;
    box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.1);
}
</style>

<?php require_once __DIR__ . '/../layouts/footer.php';
