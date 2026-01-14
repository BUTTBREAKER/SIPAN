<?php
$pageTitle = 'Mi Perfil';
$currentPage = 'usuarios';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="page-header">
    <h2 class="page-title">Mi Perfil</h2>
    <p class="page-subtitle">Información personal y configuración de cuenta</p>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                <div class="user-avatar-large mb-3">
                    <?= strtoupper(substr($usuario['primer_nombre'] ?? '', 0, 1) . substr($usuario['apellido_paterno'] ?? '', 0, 1)) ?>
                </div>
                <h3><?= htmlspecialchars(trim(($usuario['primer_nombre'] ?? '') . ' ' . ($usuario['segundo_nombre'] ?? '') . ' ' . ($usuario['apellido_paterno'] ?? '') . ' ' . ($usuario['apellido_materno'] ?? ''))) ?></h3>
                <p class="text-muted"><?= ucfirst($usuario['rol']) ?></p>
                <p class="text-muted"><i class="fas fa-envelope"></i> <?= htmlspecialchars($usuario['correo']) ?></p>
                <?php if ($usuario['telefono']) : ?>
                    <p class="text-muted"><i class="fas fa-phone"></i> <?= htmlspecialchars($usuario['telefono']) ?></p>
                <?php endif; ?>

                <div class="mt-3">
                    <span class="badge <?= $usuario['estado'] === 'activo' ? 'badge-success' : 'badge-danger' ?>">
                        <?= ucfirst($usuario['estado']) ?>
                    </span>
                </div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h3 class="card-title">Actividad Reciente</h3>
            </div>
            <div class="card-body">
                <a href="/usuarios/actividad?usuario_id=<?= $usuario['id'] ?>" class="btn btn-primary btn-block">
                    <i class="fas fa-history"></i> Ver Historial Completo
                </a>

                <div class="mt-3">
                    <small class="text-muted">Últimas acciones:</small>
                    <?php if (empty($actividad)) : ?>
                        <p class="text-muted mt-2">No hay actividad reciente</p>
                    <?php else : ?>
                        <ul class="list-unstyled mt-2">
                            <?php foreach (array_slice($actividad, 0, 5) as $act) : ?>
                                <li class="mb-2">
                                    <small>
                                        <strong><?= ucfirst($act['accion']) ?></strong> en <?= $act['tabla'] ?>
                                        <br>
                                        <span class="text-muted"><?= date('d/m/Y H:i', strtotime($act['fecha_accion'])) ?></span>
                                    </small>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Editar Información Personal</h3>
            </div>
            <div class="card-body">
                <form id="formPerfil">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Primer Nombre <span class="text-danger">*</span></label>
                                <input type="text" name="primer_nombre" class="form-control" value="<?= htmlspecialchars($usuario['primer_nombre'] ?? '') ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Segundo Nombre</label>
                                <input type="text" name="segundo_nombre" class="form-control" value="<?= htmlspecialchars($usuario['segundo_nombre'] ?? '') ?>">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Apellido Paterno <span class="text-danger">*</span></label>
                                <input type="text" name="apellido_paterno" class="form-control" value="<?= htmlspecialchars($usuario['apellido_paterno'] ?? '') ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Apellido Materno</label>
                                <input type="text" name="apellido_materno" class="form-control" value="<?= htmlspecialchars($usuario['apellido_materno'] ?? '') ?>">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Correo Electrónico <span class="text-danger">*</span></label>
                        <input type="email" name="correo" class="form-control" value="<?= htmlspecialchars($usuario['correo']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Teléfono</label>
                        <input type="text" name="telefono" class="form-control" value="<?= htmlspecialchars($usuario['telefono'] ?? '') ?>">
                    </div>

                    <hr class="my-4">

                    <h4>Cambiar Contraseña</h4>
                    <p class="text-muted">Deja estos campos en blanco si no deseas cambiar tu contraseña</p>

                    <div class="form-group">
                        <label class="form-label">Contraseña Actual</label>
                        <input type="password" name="clave_actual" class="form-control">
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Nueva Contraseña</label>
                                <input type="password" name="clave_nueva" class="form-control" minlength="6">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Confirmar Nueva Contraseña</label>
                                <input type="password" name="clave_confirmar" class="form-control" minlength="6">
                            </div>
                        </div>
                    </div>

                    <div class="form-group mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .user-avatar-large {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        background: linear-gradient(135deg, #D4A574, #8B6F47);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 48px;
        font-weight: bold;
        margin: 0 auto;
    }
</style>

<script>
    document.getElementById('formPerfil').addEventListener('submit', async (e) => {
        e.preventDefault();

        const formData = new FormData(e.target);
        const data = Object.fromEntries(formData);

        // Validar contraseñas si se están cambiando
        if (data.clave_nueva || data.clave_actual) {
            if (!data.clave_actual) {
                Swal.fire('Error', 'Debes ingresar tu contraseña actual', 'error');
                return;
            }

            if (data.clave_nueva !== data.clave_confirmar) {
                Swal.fire('Error', 'Las contraseñas no coinciden', 'error');
                return;
            }

            if (data.clave_nueva.length < 6) {
                Swal.fire('Error', 'La contraseña debe tener al menos 6 caracteres', 'error');
                return;
            }
        }

        try {
            const response = await fetch('/usuarios/actualizar-perfil', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (result.success) {
                Swal.fire('Éxito', result.message, 'success').then(() => {
                    location.reload();
                });
            } else {
                Swal.fire('Error', result.message, 'error');
            }
        } catch (error) {
            Swal.fire('Error', 'Error al actualizar el perfil', 'error');
        }
    });
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
