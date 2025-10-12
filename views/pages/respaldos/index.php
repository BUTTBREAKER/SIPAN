<?php 
$pageTitle = 'Respaldos';
$currentPage = 'respaldos';
require_once __DIR__ . '/../layouts/header.php';

// Solo administradores
if ($_SESSION['user_rol'] !== 'administrador') {
    header('Location: /dashboard');
    exit;
}
?>

<div class="page-header d-flex justify-between align-center">
    <div>
        <h2 class="page-title">Respaldos de Base de Datos</h2>
        <p class="page-subtitle">Gestión de copias de seguridad</p>
    </div>
    <button onclick="generarRespaldo()" class="btn btn-success">
        <i class="fas fa-database"></i> Generar Respaldo
    </button>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Listado de Respaldos</h3>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table datatable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Fecha de Creación</th>
                        <th>Nombre del Archivo</th>
                        <th>Tamaño</th>
                        <th>Usuario</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($respaldos)): ?>
                    <tr>
                        <td colspan="6" class="text-center">No hay respaldos disponibles</td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($respaldos as $respaldo): ?>
                    <tr>
                        <td><?= $respaldo['id'] ?></td>
                        <td><?= date('d/m/Y H:i:s', strtotime($respaldo['fecha_creacion'])) ?></td>
                        <td><code><?= htmlspecialchars($respaldo['nombre_archivo']) ?></code></td>
                        <td>
                            <?php
                            $ruta = __DIR__ . '/../../../backups/' . $respaldo['nombre_archivo'];
                            if (file_exists($ruta)) {
                                $size = filesize($ruta);
                                echo number_format($size / 1024, 2) . ' KB';
                            } else {
                                echo '<span class="text-danger">Archivo no encontrado</span>';
                            }
                            ?>
                        </td>
                        <td><?= htmlspecialchars($respaldo['usuario_nombre'] ?? 'N/A') ?></td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="/respaldos/descargar/<?= $respaldo['id'] ?>" class="btn btn-sm btn-info" title="Descargar">
                                    <i class="fas fa-download"></i>
                                </a>
                                <button onclick="restaurarRespaldo(<?= $respaldo['id'] ?>)" class="btn btn-sm btn-warning" title="Restaurar">
                                    <i class="fas fa-undo"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="alert alert-warning mt-3">
    <i class="fas fa-exclamation-triangle"></i> 
    <strong>Importante:</strong> Restaurar un respaldo sobrescribirá todos los datos actuales de la base de datos. 
    Se recomienda generar un respaldo antes de restaurar.
</div>

<script>
async function generarRespaldo() {
    const confirmed = await SIPAN.confirm(
        'Se generará una copia de seguridad completa de la base de datos',
        '¿Generar respaldo?'
    );
    if (!confirmed) return;
    
    SIPAN.loading('Generando respaldo...');
    
    try {
        const response = await fetch('/respaldos/generar', {
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
        SIPAN.error('Error al generar respaldo');
    }
}

async function restaurarRespaldo(id) {
    const confirmed = await SIPAN.confirm(
        '⚠️ ADVERTENCIA: Esta acción sobrescribirá TODOS los datos actuales de la base de datos. ¿Está completamente seguro?',
        'Restaurar Respaldo',
        'warning'
    );
    if (!confirmed) return;
    
    // Segunda confirmación
    const confirmed2 = await SIPAN.confirm(
        'Esta es su última oportunidad para cancelar. ¿Desea continuar con la restauración?',
        'Confirmar Restauración',
        'error'
    );
    if (!confirmed2) return;
    
    SIPAN.loading('Restaurando respaldo...');
    
    const formData = new FormData();
    formData.append('id', id);
    
    try {
        const response = await fetch('/respaldos/restaurar', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            SIPAN.success(data.message);
            setTimeout(() => window.location.reload(), 2000);
        } else {
            SIPAN.error(data.message);
        }
    } catch (error) {
        SIPAN.error('Error al restaurar respaldo');
    }
}
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
