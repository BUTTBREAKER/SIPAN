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
        <?php if ($_SESSION['user_rol'] === 'administrador'): ?>
        <button onclick="eliminarCliente(<?= $cliente['id'] ?>)" class="btn btn-danger">
            <i class="fas fa-trash"></i> Eliminar
        </button>
        <?php endif; ?>
    </div>
</div>

<?php if (isset($cliente) && !empty($cliente)): ?>
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
                        <?php if ($cliente['estado'] === 'activo'): ?>
                        <span class="badge badge-success">Activo</span>
                        <?php else: ?>
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
                        <?php if (!empty($cliente['telefono'])): ?>
                            <i class="fas fa-phone"></i> <?= htmlspecialchars($cliente['telefono']) ?>
                        <?php else: ?>
                            <span class="text-muted">-</span>
                        <?php endif; ?>
                    </p>
                </div>
                
                <div class="info-group">
                    <label class="info-label">Correo Electrónico:</label>
                    <p class="info-value">
                        <?php if (!empty($cliente['correo'])): ?>
                            <i class="fas fa-envelope"></i> <?= htmlspecialchars($cliente['correo']) ?>
                        <?php else: ?>
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
<?php if (isset($cliente['fecha_registro']) || isset($cliente['notas'])): ?>
<div class="row mt-3">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-info-circle"></i> Información Adicional
                </h3>
            </div>
            <div class="card-body">
                <?php if (isset($cliente['fecha_registro'])): ?>
                <div class="info-group">
                    <label class="info-label">Fecha de Registro:</label>
                    <p class="info-value">
                        <i class="fas fa-calendar"></i> 
                        <?= date('d/m/Y H:i', strtotime($cliente['fecha_registro'])) ?>
                    </p>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($cliente['notas'])): ?>
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

<?php else: ?>
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