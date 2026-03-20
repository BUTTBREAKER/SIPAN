<?php
$pageTitle = 'Nuevo Cliente';
$currentPage = 'clientes';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="page-header">
    <h2 class="page-title">Nuevo Cliente</h2>
    <p class="page-subtitle">Registrar un nuevo cliente</p>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Información del Cliente</h3>
        <a href="/clientes" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>
    <div class="card-body">
        <form id="formCliente" action="/clientes/store" method="POST">
            <?php
            require_once __DIR__ . '/../../Helpers/CSRF.php';
            echo \App\Helpers\CSRF::field();
            ?>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Nombre <span class="text-danger">*</span></label>
                        <input type="text" name="nombre" class="form-control" required>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Apellido <span class="text-danger">*</span></label>
                        <input type="text" name="apellido" class="form-control" required>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label">Tipo de Documento <span class="text-danger">*</span></label>
                        <select name="documento_tipo" class="form-control" required>
                            <option value="">Seleccionar</option>
                            <option value="Cedula">Cédula</option>
                            <option value="RIF">RIF</option>
                        </select>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label">Número de Documento <span class="text-danger">*</span></label>
                        <input type="text" name="documento_numero" class="form-control" required placeholder="Ej: V-12345678" pattern="^[VEJG]-[0-9]{7,9}$" oninput="this.value = SIPAN.formatDNI(this.value)">
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label">Teléfono <span class="text-danger">*</span></label>
                        <input type="text" name="telefono" class="form-control" required placeholder="Ej: 0414-1234567" pattern="^0[0-9]{3}-[0-9]{7}$" oninput="this.value = SIPAN.formatPhone(this.value)">
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Correo Electrónico</label>
                        <input type="email" name="correo" class="form-control" placeholder="cliente@ejemplo.com">
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Dirección <span class="text-danger">*</span></label>
                        <input type="text" name="direccion" class="form-control" required placeholder="Dirección completa para entregas">
                    </div>
                </div>
            </div>
            
            <div class="form-group mt-4">
                <button type="submit" class="btn btn-primary" id="btnSubmitCliente">
                    <i class="fas fa-save"></i> Guardar Cliente
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
    const btn = document.getElementById('btnSubmitCliente');
    const originalContent = btn.innerHTML;
    
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';

    const formData = new FormData(this);

    try {
        const response = await fetch(this.action, {
            method: 'POST',
            body: formData
        });

        const data = await response.json();
        
        if (data.success) {
            SIPAN.success('Cliente guardado con éxito');
            setTimeout(() => {
                window.location.href = '/clientes';
            }, 1500);
        } else {
            SIPAN.error(data.message || 'Error al guardar el cliente');
        }
    } catch (error) {
        console.error(error);
        SIPAN.error('Error de conexión con el servidor');
    } finally {
        btn.disabled = false;
        btn.innerHTML = originalContent;
    }
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
