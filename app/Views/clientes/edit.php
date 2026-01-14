<?php
$pageTitle = 'Editar Cliente';
$currentPage = 'clientes';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="page-header">
    <h2 class="page-title">Editar Cliente</h2>
    <p class="page-subtitle">Modificar información del cliente</p>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Información del Cliente</h3>
        <a href="/clientes" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>
    <div class="card-body">
        <form id="formCliente" action="/clientes/update/<?= $cliente['id'] ?>" method="POST">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Nombre <span class="text-danger">*</span></label>
                        <input type="text" name="nombre" class="form-control" required value="<?= htmlspecialchars($cliente['nombre']) ?>">
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Apellido <span class="text-danger">*</span></label>
                        <input type="text" name="apellido" class="form-control" required value="<?= htmlspecialchars($cliente['apellido']) ?>">
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label">Tipo de Documento <span class="text-danger">*</span></label>
                        <select name="documento_tipo" class="form-control" required>
                            <option value="">Seleccionar</option>
                            <option value="DNI" <?= $cliente['documento_tipo'] === 'DNI' ? 'selected' : '' ?>>DNI</option>
                            <option value="RUC" <?= $cliente['documento_tipo'] === 'RUC' ? 'selected' : '' ?>>RUC</option>
                            <option value="CE" <?= $cliente['documento_tipo'] === 'CE' ? 'selected' : '' ?>>Carnet de Extranjería</option>
                            <option value="Pasaporte" <?= $cliente['documento_tipo'] === 'Pasaporte' ? 'selected' : '' ?>>Pasaporte</option>
                        </select>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label">Número de Documento <span class="text-danger">*</span></label>
                        <input type="text" name="documento_numero" class="form-control" required value="<?= htmlspecialchars($cliente['documento_numero']) ?>">
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label">Teléfono</label>
                        <input type="text" name="telefono" class="form-control" value="<?= htmlspecialchars($cliente['telefono'] ?? '') ?>">
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Correo Electrónico</label>
                        <input type="email" name="correo" class="form-control" value="<?= htmlspecialchars($cliente['correo'] ?? '') ?>">
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Dirección</label>
                        <input type="text" name="direccion" class="form-control" value="<?= htmlspecialchars($cliente['direccion'] ?? '') ?>">
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Estado</label>
                        <select name="estado" class="form-control">
                            <option value="activo" <?= $cliente['estado'] === 'activo' ? 'selected' : '' ?>>Activo</option>
                            <option value="inactivo" <?= $cliente['estado'] === 'inactivo' ? 'selected' : '' ?>>Inactivo</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="form-group mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Actualizar Cliente
                </button>
                <a href="/clientes" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancelar
                </a>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('formCliente').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    if (!SIPAN.validateForm(this)) {
        SIPAN.error('Por favor complete todos los campos requeridos');
        return;
    }
    
    const data = await SIPAN.submitForm(this, (response) => {
        setTimeout(() => {
            window.location.href = '/clientes';
        }, 1500);
    });
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
