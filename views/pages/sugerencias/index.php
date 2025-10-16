<?php 
$pageTitle = 'Sugerencias de Compra';
$currentPage = 'sugerencias';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="page-header d-flex justify-between align-center">
    <div>
        <h2 class="page-title">Sugerencias de Compra</h2>
        <p class="page-subtitle">Sugerencias automáticas basadas en stock bajo</p>
    </div>
    <button onclick="generarSugerencias()" class="btn btn-primary">
        <i class="fas fa-magic"></i> Generar Sugerencias
    </button>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Listado de Sugerencias</h3>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table datatable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tipo</th>
                        <th>Item</th>
                        <th>Stock Actual</th>
                        <th>Cantidad Sugerida</th>
                        <th>Prioridad</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($sugerencias)): ?>
                    <tr>
                        <td colspan="8" class="text-center">No hay sugerencias de compra</td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($sugerencias as $sugerencia): ?>
                    <tr>
                        <td><?= $sugerencia['id'] ?></td>
                        <td>
                            <?php if ($sugerencia['tipo'] === 'producto'): ?>
                            <span class="badge badge-info">Producto</span>
                            <?php else: ?>
                            <span class="badge badge-warning">Insumo</span>
                            <?php endif; ?>
                        </td>
                        <td><strong><?= htmlspecialchars($sugerencia['item_nombre'] ?? 'Item #' . $sugerencia['id_item']) ?></strong></td>
                        <td>
                            <span class="badge badge-danger"><?= $sugerencia['stock_actual'] ?></span>
                        </td>
                        <td><strong><?= $sugerencia['cantidad_sugerida'] ?></strong></td>
                        <td>
                            <?php
                            $prioridades = [
                                'alta' => '<span class="badge badge-danger">Alta</span>',
                                'media' => '<span class="badge badge-warning">Media</span>',
                                'baja' => '<span class="badge badge-info">Baja</span>'
                            ];
                            echo $prioridades[$sugerencia['prioridad']] ?? $sugerencia['prioridad'];
                            ?>
                        </td>
                        <td>
                            <?php
                            $estados = [
                                'pendiente' => '<span class="badge badge-warning">Pendiente</span>',
                                'aprobada' => '<span class="badge badge-success">Aprobada</span>',
                                'rechazada' => '<span class="badge badge-danger">Rechazada</span>',
                                'completada' => '<span class="badge badge-info">Completada</span>'
                            ];
                            echo $estados[$sugerencia['estado']] ?? $sugerencia['estado'];
                            ?>
                        </td>
                        <td>
                            <?php if ($sugerencia['estado'] === 'pendiente'): ?>
                            <div class="d-flex gap-1">
                                <button onclick="aprobarSugerencia(<?= $sugerencia['id'] ?>)" class="btn btn-sm btn-success" title="Aprobar">
                                    <i class="fas fa-check"></i>
                                </button>
                                <button onclick="rechazarSugerencia(<?= $sugerencia['id'] ?>)" class="btn btn-sm btn-danger" title="Rechazar">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            <?php elseif ($sugerencia['estado'] === 'aprobada'): ?>
                            <button onclick="completarSugerencia(<?= $sugerencia['id'] ?>)" class="btn btn-sm btn-info" title="Marcar como completada">
                                <i class="fas fa-check-double"></i> Completar
                            </button>
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
    const confirmed = await SIPAN.confirm(
        'Se generarán sugerencias automáticas basadas en el stock actual',
        '¿Generar sugerencias?'
    );
    if (!confirmed) return;
    
    try {
        const response = await fetch(App::getUrl('sugerencias.generar'), {
            method: 'POST'
        });
        
        const data = await response.json();
        
        if (data.success) {
            SIPAN.success(data.message);
            setTimeout(() => window.location.reload(), 1500);
        } else {
            SIPAN.error(data.message);
        }
    } catch (error) {
        SIPAN.error('Error al generar sugerencias');
    }
}

async function aprobarSugerencia(id) {
    const formData = new FormData();
    formData.append('id', id);
    
    try {
        const response = await fetch(App::getUrl('sugerencias.aprobar'), {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            SIPAN.success(data.message);
            setTimeout(() => window.location.reload(), 1500);
        } else {
            SIPAN.error(data.message);
        }
    } catch (error) {
        SIPAN.error('Error al aprobar sugerencia');
    }
}

async function rechazarSugerencia(id) {
    const confirmed = await SIPAN.confirm('¿Rechazar esta sugerencia?');
    if (!confirmed) return;
    
    const formData = new FormData();
    formData.append('id', id);
    
    try {
        const response = await fetch(App::getUrl('sugerencias.rechazar'), {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            SIPAN.success(data.message);
            setTimeout(() => window.location.reload(), 1500);
        } else {
            SIPAN.error(data.message);
        }
    } catch (error) {
        SIPAN.error('Error al rechazar sugerencia');
    }
}

async function completarSugerencia(id) {
    const confirmed = await SIPAN.confirm('¿Marcar esta sugerencia como completada?');
    if (!confirmed) return;
    
    const formData = new FormData();
    formData.append('id', id);
    
    try {
        const response = await fetch(App::getUrl('sugerencias.completar'), {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            SIPAN.success(data.message);
            setTimeout(() => window.location.reload(), 1500);
        } else {
            SIPAN.error(data.message);
        }
    } catch (error) {
        SIPAN.error('Error al completar sugerencia');
    }
}
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
