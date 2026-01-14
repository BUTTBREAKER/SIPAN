<?php
$pageTitle = 'Sucursales';
$currentPage = 'sucursales';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="page-header">
    <h2 class="page-title">Gestión de Sucursales</h2>
    <p class="page-subtitle">Administración de sucursales del negocio</p>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Listado de Sucursales</h3>
        <a href="/sucursales/create" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nueva Sucursal
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table datatable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Dirección</th>
                        <th>Teléfono</th>
                        <th>Clave</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($sucursales)) : ?>
                    <tr>
                        <td colspan="7" class="text-center">No hay sucursales registradas</td>
                    </tr>
                    <?php else : ?>
                        <?php foreach ($sucursales as $sucursal) : ?>
                    <tr>
                        <td>#<?= $sucursal['id'] ?></td>
                        <td><strong><?= htmlspecialchars($sucursal['nombre']) ?></strong></td>
                        <td><?= htmlspecialchars($sucursal['direccion']) ?></td>
                        <td><?= htmlspecialchars($sucursal['telefono'] ?? '-') ?></td>
                        <td>
                            <code class="bg-light p-2 rounded"><?= $sucursal['clave_sucursal'] ?></code>
                            <button onclick="copiarClave('<?= $sucursal['clave_sucursal'] ?>')" 
                                    class="btn btn-sm btn-outline-secondary ms-1" 
                                    title="Copiar clave">
                                <i class="fas fa-copy"></i>
                            </button>
                        </td>
                        <td>
                            <span class="badge <?= $sucursal['estado'] === 'activo' ? 'badge-success' : 'badge-danger' ?>">
                                <?= ucfirst($sucursal['estado']) ?>
                            </span>
                        </td>
                        <td>
                            <div class="btn-group">
                                <a href="/sucursales/show/<?= $sucursal['id'] ?>" 
                                   class="btn btn-sm btn-info" 
                                   title="Ver detalles">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="/sucursales/edit/<?= $sucursal['id'] ?>" 
                                   class="btn btn-sm btn-warning" 
                                   title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button onclick="cambiarEstado(<?= $sucursal['id'] ?>, '<?= $sucursal['estado'] === 'activo' ? 'inactivo' : 'activo' ?>')" 
                                        class="btn btn-sm <?= $sucursal['estado'] === 'activo' ? 'btn-danger' : 'btn-success' ?>"
                                        title="<?= $sucursal['estado'] === 'activo' ? 'Desactivar' : 'Activar' ?>">
                                    <i class="fas fa-<?= $sucursal['estado'] === 'activo' ? 'ban' : 'check' ?>"></i>
                                </button>
                                <button onclick="regenerarClave(<?= $sucursal['id'] ?>)" 
                                        class="btn btn-sm btn-secondary"
                                        title="Regenerar clave">
                                    <i class="fas fa-sync"></i>
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

<script>
function copiarClave(clave) {
    navigator.clipboard.writeText(clave).then(() => {
        Swal.fire({
            icon: 'success',
            title: 'Clave copiada',
            text: 'La clave ha sido copiada al portapapeles',
            timer: 2000,
            showConfirmButton: false
        });
    });
}

async function cambiarEstado(sucursalId, nuevoEstado) {
    const result = await Swal.fire({
        title: '¿Estás seguro?',
        text: `¿Deseas ${nuevoEstado === 'activo' ? 'activar' : 'desactivar'} esta sucursal?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#D4A574',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, continuar',
        cancelButtonText: 'Cancelar'
    });
    
    if (result.isConfirmed) {
        try {
            const response = await fetch('/sucursales/cambiar-estado', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({
                    sucursal_id: sucursalId,
                    estado: nuevoEstado
                })
            });
            
            const data = await response.json();
            
            if (data.success) {
                Swal.fire('Éxito', data.message, 'success').then(() => {
                    location.reload();
                });
            } else {
                Swal.fire('Error', data.message, 'error');
            }
        } catch (error) {
            Swal.fire('Error', 'Error al cambiar el estado', 'error');
        }
    }
}

async function regenerarClave(sucursalId) {
    const result = await Swal.fire({
        title: '¿Regenerar clave?',
        text: 'Se generará una nueva clave para esta sucursal. La clave anterior dejará de funcionar.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#D4A574',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, regenerar',
        cancelButtonText: 'Cancelar'
    });
    
    if (result.isConfirmed) {
        try {
            const response = await fetch(`/sucursales/regenerar-clave/${sucursalId}`, {
                method: 'POST',
                headers: {'Content-Type': 'application/json'}
            });
            
            const data = await response.json();
            
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Clave regenerada',
                    html: `<p>Nueva clave: <code class="bg-light p-2 rounded">${data.clave_sucursal}</code></p>`,
                    confirmButtonText: 'Copiar y cerrar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        navigator.clipboard.writeText(data.clave_sucursal);
                        location.reload();
                    }
                });
            } else {
                Swal.fire('Error', data.message, 'error');
            }
        } catch (error) {
            Swal.fire('Error', 'Error al regenerar clave', 'error');
        }
    }
}
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
