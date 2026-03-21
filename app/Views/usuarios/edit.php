<?php
$pageTitle = 'Editar Usuario';
$currentPage = 'usuarios';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h2 class="page-title">Editar Usuario</h2>
        <p class="page-subtitle">Modificando información de: <strong><?= htmlspecialchars($usuario['primer_nombre'] . ' ' . $usuario['apellido_paterno']) ?></strong></p>
    </div>
    <a href="/usuarios" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i> Volver a la lista
    </a>
</div>

<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h3 class="card-title mb-0 h5">Configuración de Cuenta</h3>
            </div>
            <div class="card-body p-4">
                <form id="formEditUsuario">
                    <?= \App\Helpers\CSRF::field() ?>
                    <input type="hidden" name="id" value="<?= $usuario['id'] ?>">

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Primer Nombre <span class="text-danger">*</span></label>
                            <input type="text" name="primer_nombre" class="form-control" value="<?= htmlspecialchars($usuario['primer_nombre']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Segundo Nombre</label>
                            <input type="text" name="segundo_nombre" class="form-control" value="<?= htmlspecialchars($usuario['segundo_nombre'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Apellido Paterno <span class="text-danger">*</span></label>
                            <input type="text" name="apellido_paterno" class="form-control" value="<?= htmlspecialchars($usuario['apellido_paterno']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Apellido Materno</label>
                            <input type="text" name="apellido_materno" class="form-control" value="<?= htmlspecialchars($usuario['apellido_materno'] ?? '') ?>">
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Correo Electrónico <span class="text-danger">*</span></label>
                            <input type="email" name="correo" class="form-control" value="<?= htmlspecialchars($usuario['correo']) ?>" required>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Teléfono</label>
                            <input type="text" name="telefono" class="form-control" value="<?= htmlspecialchars($usuario['telefono'] ?? '') ?>" oninput="this.value = SIPAN.formatPhone(this.value)">
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Rol del Usuario <span class="text-danger">*</span></label>
                            <select name="rol" class="form-select" required>
                                <option value="empleado" <?= $usuario['rol'] === 'empleado' ? 'selected' : '' ?>>Empleado (Repartidor/Producción)</option>
                                <option value="cajero" <?= $usuario['rol'] === 'cajero' ? 'selected' : '' ?>>Cajero</option>
                                <option value="administrador" <?= $usuario['rol'] === 'administrador' ? 'selected' : '' ?>>Administrador</option>
                            </select>
                        </div>
                    </div>

                    <div class="alert alert-info mt-4 mb-0 border-0 shadow-none" style="background-color: #f8f9fa;">
                        <h4 class="h6 mb-2"><i class="fas fa-key me-2"></i> Restablecer Contraseña</h4>
                        <p class="small text-muted mb-3">Si deseas cambiar la contraseña del usuario, ingresa una nueva abajo. De lo contrario, deja el campo en blanco.</p>
                        <div class="row">
                            <div class="col-md-6">
                                <input type="password" name="clave_nueva" class="form-control" placeholder="Nueva contraseña" minlength="6">
                            </div>
                            <div class="col-md-6 text-end">
                                <small class="text-muted d-block mt-2">Mínimo 6 caracteres.</small>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-top text-end">
                        <button type="submit" class="btn btn-primary btn-lg px-4" id="btnGuardar">
                            <i class="fas fa-save me-2"></i> <span>Guardar Cambios</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('formEditUsuario').addEventListener('submit', async (e) => {
        e.preventDefault();

        const btn = document.getElementById('btnGuardar');
        const btnText = btn.querySelector('span');
        const btnIcon = btn.querySelector('i');
        const originalText = btnText.innerText;
        const originalIconClass = btnIcon.className;

        const formData = new FormData(e.target);
        const data = Object.fromEntries(formData);

        try {
            // Activar estado de carga
            btn.disabled = true;
            btnText.innerText = 'Guardando...';
            btnIcon.className = 'fas fa-spinner fa-spin me-2';

            const response = await fetch('/usuarios/update', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': document.querySelector('input[name="csrf_token"]').value
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (result.success) {
                Swal.fire({
                    icon: 'success',
                    title: '¡Éxito!',
                    text: result.message,
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    window.location.href = '/usuarios';
                });
            } else {
                Swal.fire('Error', result.message, 'error');
            }
        } catch (error) {
            Swal.fire('Error', 'Error al procesar la actualización', 'error');
        } finally {
            // Desactivar estado de carga
            btn.disabled = false;
            btnText.innerText = originalText;
            btnIcon.className = originalIconClass;
        }
    });
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
