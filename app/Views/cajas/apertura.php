<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card border-0 shadow-lg overflow-hidden glass-card">
                <div class="card-header bg-success text-white py-4 text-center">
                    <div class="icon-circle bg-white text-success mb-3 mx-auto d-flex align-items-center justify-content-center">
                        <i class="fas fa-unlock-alt fa-2x"></i>
                    </div>
                    <h3 class="fw-bold mb-0">Apertura de Caja</h3>
                    <p class="mb-0 opacity-75">Inicia un nuevo turno de trabajo</p>
                </div>
                <div class="card-body p-4 p-md-5">
                    <?php
                    $tasa = (new \App\Models\Configuracion())->getTasaBCV();
                    ?>
                    <div class="alert alert-info border-0 rounded-4 mb-4 d-flex align-items-center">
                        <i class="fas fa-info-circle fs-4 me-3"></i>
                        <div>
                            <small class="d-block opacity-75">Tasa BCV del día</small>
                            <span class="fw-bold fs-5">Bs <?php echo number_format($tasa, 2); ?></span>
                        </div>
                    </div>

                    <form action="/cajas/abrir" method="POST" id="aperturaForm">
                        <div class="mb-4">
                            <label class="form-label text-muted fw-medium">Efectivo en Dólares ($)</label>
                            <div class="input-group input-group-lg border rounded-3 overflow-hidden">
                                <span class="input-group-text bg-light border-0"><i class="fas fa-dollar-sign text-success"></i></span>
                                <input type="number" step="0.01" name="monto_usd" class="form-control border-0 bg-light" placeholder="0.00" required autofocus>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label text-muted fw-medium">Efectivo en Bolívares (Bs)</label>
                            <div class="input-group input-group-lg border rounded-3 overflow-hidden">
                                <span class="input-group-text bg-light border-0"><span class="fw-bold text-primary">Bs</span></span>
                                <input type="number" step="0.01" name="monto_bs" class="form-control border-0 bg-light" placeholder="0.00" required>
                            </div>
                        </div>

                        <div class="d-grid gap-3">
                            <button type="submit" class="btn btn-success btn-lg py-3 fw-bold shadow-sm">
                                <i class="fas fa-check-circle me-2"></i> Confirmar Apertura
                            </button>
                            <a href="/cajas" class="btn btn-link text-muted py-2">
                                <i class="fas fa-arrow-left me-2"></i> Cancelar y Volver
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
    border-color: #28a745 !important;
    box-shadow: 0 0 0 0.25rem rgba(40, 167, 69, 0.1);
}
</style>

<?php require_once __DIR__ . '/../layouts/footer.php';
