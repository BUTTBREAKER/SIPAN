<?php
$pageTitle = 'Nueva Sucursal';
$currentPage = 'sucursales';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="page-header">
    <h2 class="page-title">Nueva Sucursal</h2>
    <p class="page-subtitle">Crear una nueva sucursal del negocio</p>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Información de la Sucursal</h3>
        <a href="/sucursales" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>
    <div class="card-body">
        <form id="formSucursal">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Nombre de la Sucursal <span class="text-danger">*</span></label>
                        <input type="text" name="nombre" class="form-control" required placeholder="Ej: Panadería Central">
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Teléfono</label>
                        <input type="text" name="telefono" class="form-control" placeholder="Ej: 0212-1234567">
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Correo Electrónico</label>
                        <input type="email" name="correo" class="form-control" placeholder="sucursal@ejemplo.com">
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Dirección <span class="text-danger">*</span></label>
                        <input type="text" name="direccion" class="form-control" required placeholder="Dirección completa">
                    </div>
                </div>
            </div>
            
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> 
                <strong>Nota:</strong> Al crear la sucursal, se generará automáticamente una clave única que los empleados usarán para registrarse.
            </div>
            
            <div class="form-group mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Crear Sucursal
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('formSucursal').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    const data = Object.fromEntries(formData);
    
    try {
        const response = await fetch('/sucursales/store', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.success) {
            Swal.fire({
                icon: 'success',
                title: 'Sucursal creada',
                html: `
                    <p>La sucursal ha sido creada exitosamente.</p>
                    <div class="alert alert-warning mt-3">
                        <strong>Clave de Sucursal:</strong><br>
                        <code class="bg-light p-2 rounded fs-5">${result.clave_sucursal}</code><br>
                        <small>Comparte esta clave con los empleados para que puedan registrarse</small>
                    </div>
                `,
                confirmButtonText: 'Copiar clave y continuar'
            }).then((res) => {
                if (res.isConfirmed) {
                    navigator.clipboard.writeText(result.clave_sucursal);
                    window.location.href = '/sucursales';
                }
            });
        } else {
            Swal.fire('Error', result.message, 'error');
        }
    } catch (error) {
        Swal.fire('Error', 'Error al crear la sucursal', 'error');
    }
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
