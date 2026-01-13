<?php
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/sidebar.php';
?>

<div class="main-content" x-data="editSucursalApp(<?= htmlspecialchars(json_encode($sucursal)) ?>)">
    <div class="content-header">
        <h1><i class="fas fa-store"></i> Editar Sucursal</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="/sucursales">Sucursales</a></li>
                <li class="breadcrumb-item active">Editar</li>
            </ol>
        </nav>
    </div>

    <div class="card">
        <div class="card-body">
            <form @submit.prevent="handleSubmit()">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nombre de la Sucursal <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" x-model="formData.nombre" required>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Estado <span class="text-danger">*</span></label>
                        <select class="form-select" x-model="formData.estado" required>
                            <option value="activa">Activa</option>
                            <option value="inactiva">Inactiva</option>
                        </select>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Dirección <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" x-model="formData.direccion" required>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Teléfono</label>
                        <input type="text" class="form-control" x-model="formData.telefono">
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Correo</label>
                        <input type="email" class="form-control" x-model="formData.correo">
                    </div>
                </div>
                
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> <strong>Clave de Sucursal:</strong> <?= htmlspecialchars($sucursal['clave_sucursal']) ?>
                    <br><small>Para cambiar la clave, use el botón "Regenerar Clave" en la vista de detalle</small>
                </div>
                
                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Guardar Cambios
                    </button>
                    <a href="/sucursales" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editSucursalApp(sucursal) {
    return {
        formData: {
            nombre: sucursal.nombre,
            direccion: sucursal.direccion,
            telefono: sucursal.telefono || '',
            correo: sucursal.correo || '',
            estado: sucursal.estado
        },
        
        async handleSubmit() {
            try {
                const response = await fetch('/sucursales/update/<?= $sucursal['id'] ?>', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify(this.formData)
                });
                
                const result = await response.json();
                
                if (result.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Éxito',
                        text: 'Sucursal actualizada correctamente',
                        confirmButtonColor: '#D4A574'
                    }).then(() => {
                        window.location.href = '/sucursales';
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: result.message || 'Error al actualizar sucursal',
                        confirmButtonColor: '#D4A574'
                    });
                }
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error de conexión',
                    confirmButtonColor: '#D4A574'
                });
            }
        }
    };
}
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

