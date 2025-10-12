<?php 
$pageTitle = 'Actividad de Usuario';
$currentPage = 'usuarios';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="page-header">
    <h2 class="page-title">Actividad de <?= htmlspecialchars($usuario['nombre']) ?></h2>
    <p class="page-subtitle">Historial completo de cambios realizados</p>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Historial de Actividad</h3>
        <a href="<?= $_SESSION['user_rol'] === 'administrador' ? '/usuarios' : '/usuarios/perfil' ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>
    <div class="card-body">
        <?php if (empty($actividad)): ?>
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> No hay actividad registrada para este usuario
        </div>
        <?php else: ?>
        <div class="timeline">
            <?php foreach ($actividad as $act): ?>
            <div class="timeline-item">
                <div class="timeline-marker <?= $act['accion'] === 'insert' ? 'bg-success' : ($act['accion'] === 'delete' ? 'bg-danger' : 'bg-warning') ?>">
                    <i class="fas fa-<?= $act['accion'] === 'insert' ? 'plus' : ($act['accion'] === 'delete' ? 'trash' : 'edit') ?>"></i>
                </div>
                <div class="timeline-content">
                    <div class="timeline-header">
                        <strong><?= ucfirst($act['accion']) ?></strong> en <strong><?= $act['tabla'] ?></strong>
                        <span class="timeline-date"><?= date('d/m/Y H:i:s', strtotime($act['fecha_accion'])) ?></span>
                    </div>
                    <div class="timeline-body">
                        <p class="mb-1"><strong>Registro ID:</strong> <?= $act['id_registro'] ?></p>
                        
                        <?php if ($act['accion'] === 'update' && $act['datos_anteriores'] && $act['datos_nuevos']): ?>
                        <div class="row">
                            <div class="col-md-6">
                                <small class="text-muted">Datos anteriores:</small>
                                <pre class="bg-light p-2 rounded"><?= htmlspecialchars(json_encode(json_decode($act['datos_anteriores']), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) ?></pre>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted">Datos nuevos:</small>
                                <pre class="bg-light p-2 rounded"><?= htmlspecialchars(json_encode(json_decode($act['datos_nuevos']), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) ?></pre>
                            </div>
                        </div>
                        <?php elseif ($act['accion'] === 'insert' && $act['datos_nuevos']): ?>
                        <small class="text-muted">Datos creados:</small>
                        <pre class="bg-light p-2 rounded"><?= htmlspecialchars(json_encode(json_decode($act['datos_nuevos']), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) ?></pre>
                        <?php elseif ($act['accion'] === 'delete' && $act['datos_anteriores']): ?>
                        <small class="text-muted">Datos eliminados:</small>
                        <pre class="bg-light p-2 rounded"><?= htmlspecialchars(json_encode(json_decode($act['datos_anteriores']), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) ?></pre>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding: 20px 0;
}

.timeline-item {
    position: relative;
    padding-left: 60px;
    margin-bottom: 30px;
}

.timeline-marker {
    position: absolute;
    left: 0;
    top: 0;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 16px;
}

.timeline-content {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 15px;
    border-left: 3px solid #D4A574;
}

.timeline-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}

.timeline-date {
    font-size: 0.85rem;
    color: #6c757d;
}

.timeline-body pre {
    font-size: 0.85rem;
    max-height: 200px;
    overflow-y: auto;
    margin-bottom: 0;
}
</style>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
