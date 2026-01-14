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
                        <th>Tipo</th>
                        <th>Usuario</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($respaldos)) : ?>
                        <tr>
                            <td colspan="7" class="text-center">No hay respaldos disponibles</td>
                        </tr>
                    <?php else : ?>
                        <?php foreach ($respaldos as $respaldo) : ?>
                            <tr>
                                <td><?= $respaldo['id'] ?></td>
                                <td><?= date('d/m/Y H:i:s', strtotime($respaldo['fecha_creacion'])) ?></td>
                                <td><code><?= htmlspecialchars($respaldo['nombre_archivo']) ?></code></td>
                                <td>
                                    <?php
                                    $size = $respaldo['tamano_bytes'] ?? 0;
                                    if ($size < 1024) {
                                        echo $size . ' B';
                                    } elseif ($size < 1048576) {
                                        echo number_format($size / 1024, 2) . ' KB';
                                    } else {
                                        echo number_format($size / 1048576, 2) . ' MB';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <span class="badge <?= $respaldo['tipo'] === 'automatico' ? 'badge-info' : 'badge-primary' ?>">
                                        <?= ucfirst($respaldo['tipo']) ?>
                                    </span>
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
        const result = await Swal.fire({
            title: '¿Generar respaldo?',
            text: 'Se generará una copia de seguridad completa de la base de datos',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, generar',
            cancelButtonText: 'Cancelar'
        });

        if (!result.isConfirmed) return;

        Swal.fire({
            title: 'Generando respaldo...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        try {
            const response = await fetch('/respaldos/generar', {
                method: 'POST'
            });

            const data = await response.json();

            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Éxito',
                    text: data.message,
                    confirmButtonColor: '#28a745'
                }).then(() => {
                    window.location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.message,
                    confirmButtonColor: '#dc3545'
                });
            }
        } catch (error) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Error al generar respaldo',
                confirmButtonColor: '#dc3545'
            });
        }
    }

    async function restaurarRespaldo(id) {
        const result1 = await Swal.fire({
            title: 'Restaurar Respaldo',
            html: '⚠️ <strong>ADVERTENCIA:</strong> Esta acción sobrescribirá TODOS los datos actuales de la base de datos.<br><br>¿Está completamente seguro?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ffc107',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Continuar',
            cancelButtonText: 'Cancelar'
        });

        if (!result1.isConfirmed) return;

        // Segunda confirmación
        const result2 = await Swal.fire({
            title: 'Confirmar Restauración',
            text: 'Esta es su última oportunidad para cancelar. ¿Desea continuar con la restauración?',
            icon: 'error',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, restaurar',
            cancelButtonText: 'Cancelar'
        });

        if (!result2.isConfirmed) return;

        Swal.fire({
            title: 'Restaurando respaldo...',
            text: 'Por favor espere, esto puede tomar unos momentos',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        try {
            const response = await fetch('/respaldos/restaurar', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    id: id
                })
            });

            const data = await response.json();

            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Éxito',
                    text: data.message,
                    confirmButtonColor: '#28a745'
                }).then(() => {
                    window.location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.message,
                    confirmButtonColor: '#dc3545'
                });
            }
        } catch (error) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Error al restaurar respaldo',
                confirmButtonColor: '#dc3545'
            });
        }
    }
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
