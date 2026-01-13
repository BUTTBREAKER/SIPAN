<?php 
$pageTitle = 'Sugerencias de Compra';
$currentPage = 'sugerencias';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="page-header d-flex justify-between align-center">
    <div>
        <h2 class="page-title">Sugerencias de Compra</h2>
        <p class="page-subtitle">Recomendaciones basadas en predicciones de demanda y stock crítico</p>
    </div>
    <div>
        <button class="btn btn-primary" id="btnGenerar" onclick="generarSugerencias()">
            <i class="fas fa-magic"></i> Analizar y Generar
        </button>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h3 class="card-title">Listado de Sugerencias</h3>
            <div class="btn-group">
                <a href="?estado=pendiente" class="btn btn-sm btn-outline-secondary <?= ($estado ?? 'pendiente') === 'pendiente' ? 'active' : '' ?>">Pendientes</a>
                <a href="?estado=aprobada" class="btn btn-sm btn-outline-success <?= ($estado ?? '') === 'aprobada' ? 'active' : '' ?>">Aprobadas</a>
                <a href="?estado=rechazada" class="btn btn-sm btn-outline-danger <?= ($estado ?? '') === 'rechazada' ? 'active' : '' ?>">Rechazadas</a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Prioridad</th>
                        <th>Item</th>
                        <th>Stock Actual</th>
                        <th>Sugerido</th>
                        <th>Razón</th>
                        <th>Fecha</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($sugerencias)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <i class="fas fa-clipboard-check fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No hay sugerencias en este estado.</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($sugerencias as $sug): ?>
                            <tr>
                                <td>
                                    <?php 
                                    $badge = match($sug['prioridad']) {
                                        'alta' => 'danger',
                                        'media' => 'warning',
                                        'baja' => 'info',
                                        default => 'secondary'
                                    };
                                    ?>
                                    <span class="badge bg-<?= $badge ?>"><?= ucfirst($sug['prioridad']) ?></span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm bg-light me-2 d-flex align-items-center justify-content-center rounded">
                                            <i class="fas <?= $sug['tipo'] === 'insumo' ? 'fa-box' : 'fa-tag' ?>"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0"><?= htmlspecialchars($sug['item_nombre']) ?></h6>
                                            <small class="text-muted"><?= ucfirst($sug['tipo']) ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <?= floatval($sug['cantidad_actual']) ?> <?= $sug['unidad_medida'] ?>
                                </td>
                                <td>
                                    <span class="text-primary fw-bold">
                                        <?= floatval($sug['cantidad_sugerida']) ?> <?= $sug['unidad_medida'] ?>
                                    </span>
                                </td>
                                <td>
                                    <small class="text-muted d-block" style="max-width: 250px;">
                                        <?= htmlspecialchars($sug['razon'] ?? '') ?>
                                    </small>
                                </td>
                                <td>
                                    <?= date('d/m H:i', strtotime($sug['fecha_sugerencia'])) ?>
                                </td>
                                <td>
                                    <?php if ($sug['estado'] === 'pendiente'): ?>
                                        <button class="btn btn-sm btn-success" onclick="aprobar(<?= $sug['id'] ?>)" title="Aprobar para compra">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger" onclick="rechazar(<?= $sug['id'] ?>)" title="Rechazar">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    <?php elseif ($sug['estado'] === 'aprobada'): ?>
                                        <a href="/compras/create?from_sugerencia=<?= $sug['id'] ?>" class="btn btn-sm btn-primary">
                                            <i class="fas fa-cart-plus"></i> Comprar
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
async function generarSugerencias() {
    const btn = document.getElementById('btnGenerar');
    const originalText = btn.innerHTML;
    
    // Cambiar estado a cargando
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Analizando...';

    try {
        const response = await fetch('/predicciones/generar', {
            method: 'POST',
            headers: {
                'X-CSRF-Token': '<?= \App\Helpers\CSRF::getToken() ?>'
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            SIPAN.success(data.message);
            setTimeout(() => window.location.reload(), 1500);
        } else {
            SIPAN.error(data.message);
        }
    } catch (error) {
        console.error(error);
        SIPAN.error('Error al conectar con el motor de predicciones');
    } finally {
        btn.disabled = false;
        btn.innerHTML = originalText;
    }
}

async function aprobar(id) {
    if(!confirm('¿Aprobar esta sugerencia?')) return;
    updateEstado('/sugerencias/aprobar', id);
}

async function rechazar(id) {
    if(!confirm('¿Rechazar esta sugerencia?')) return;
    updateEstado('/sugerencias/rechazar', id);
}

async function updateEstado(url, id) {
    const fd = new FormData();
    fd.append('id', id);
    
    try {
        const res = await fetch(url, {
            method: 'POST',
            body: fd,
            headers: {
                'X-CSRF-Token': '<?= \App\Helpers\CSRF::getToken() ?>'
            }
        });
        const data = await res.json();
        if(data.success) {
            SIPAN.success(data.message);
            location.reload();
        } else {
            SIPAN.error(data.message);
        }
    } catch(e) {
        SIPAN.error('Error de conexión');
    }
}
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
