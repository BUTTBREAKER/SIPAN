<?php
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/sidebar.php';
?>

<div class="main-content">
    <div class="content-header">
        <h1><i class="fas fa-store"></i> Detalle de Sucursal</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="/sucursales">Sucursales</a></li>
                <li class="breadcrumb-item active">Detalle</li>
            </ol>
        </nav>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-info-circle"></i> Información de la Sucursal</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Nombre:</strong><br>
                            <h4><?= htmlspecialchars($sucursal['nombre']) ?></h4>
                        </div>
                        <div class="col-md-6">
                            <strong>Estado:</strong><br>
                            <?php if ($sucursal['estado'] === 'activa') : ?>
                                <span class="badge bg-success">Activa</span>
                            <?php else : ?>
                                <span class="badge bg-danger">Inactiva</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <strong>Dirección:</strong><br>
                            <?= htmlspecialchars($sucursal['direccion']) ?>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Teléfono:</strong><br>
                            <?= htmlspecialchars($sucursal['telefono'] ?? 'No especificado') ?>
                        </div>
                        <div class="col-md-6">
                            <strong>Correo:</strong><br>
                            <?= htmlspecialchars($sucursal['correo'] ?? 'No especificado') ?>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <strong>Clave de Sucursal:</strong><br>
                            <div class="alert alert-info">
                                <i class="fas fa-key"></i> <strong><?= htmlspecialchars($sucursal['clave_sucursal']) ?></strong>
                                <br><small>Comparte esta clave con nuevos empleados para que puedan registrarse</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card mt-3">
                <div class="card-header">
                    <h5><i class="fas fa-users"></i> Empleados de esta Sucursal</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Correo</th>
                                    <th>Rol</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($empleados as $empleado) : ?>
                                <tr>
                                    <td><?= htmlspecialchars($empleado['nombre']) ?></td>
                                    <td><?= htmlspecialchars($empleado['correo']) ?></td>
                                    <td><span class="badge bg-primary"><?= ucfirst($empleado['rol']) ?></span></td>
                                    <td>
                                        <?php if ($empleado['estado'] === 'activo') : ?>
                                            <span class="badge bg-success">Activo</span>
                                        <?php else : ?>
                                            <span class="badge bg-danger">Inactivo</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php if (empty($empleados)) : ?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted">No hay empleados registrados</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-chart-bar"></i> Estadísticas</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3 p-3 bg-light rounded">
                        <div class="d-flex justify-content-between mb-2">
                            <strong>Total Empleados:</strong>
                            <span class="badge bg-primary"><?= count($empleados) ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <strong>Empleados Activos:</strong>
                            <span class="badge bg-success"><?= count(array_filter($empleados, fn($e) => $e['estado'] === 'activo')) ?></span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <strong>Empleados Inactivos:</strong>
                            <span class="badge bg-danger"><?= count(array_filter($empleados, fn($e) => $e['estado'] === 'inactivo')) ?></span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card mt-3">
                <div class="card-header">
                    <h5><i class="fas fa-cog"></i> Acciones</h5>
                </div>
                <div class="card-body">
                    <a href="/sucursales/edit/<?= $sucursal['id'] ?>" class="btn btn-warning w-100 mb-2">
                        <i class="fas fa-edit"></i> Editar Sucursal
                    </a>
                    <a href="/sucursales" class="btn btn-secondary w-100 mb-2">
                        <i class="fas fa-arrow-left"></i> Volver al Listado
                    </a>
                    <button onclick="regenerarClave(<?= $sucursal['id'] ?>)" class="btn btn-info w-100">
                        <i class="fas fa-sync"></i> Regenerar Clave
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
async function regenerarClave(id) {
    const result = await Swal.fire({
        title: '¿Regenerar clave?',
        text: 'La clave actual dejará de funcionar',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#D4A574',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, regenerar',
        cancelButtonText: 'Cancelar'
    });
    
    if (result.isConfirmed) {
        try {
            const response = await fetch(`/sucursales/regenerar-clave/${id}`, {
                method: 'POST'
            });
            const data = await response.json();
            
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Clave Regenerada',
                    html: `Nueva clave: <strong>${data.nueva_clave}</strong>`,
                    confirmButtonColor: '#D4A574'
                }).then(() => location.reload());
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.message,
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
}
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

