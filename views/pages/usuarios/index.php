<?php 
$pageTitle = 'Usuarios';
$currentPage = 'usuarios';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="page-header">
    <h2 class="page-title">Gestión de Usuarios</h2>
    <p class="page-subtitle">Usuarios de la sucursal actual</p>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Listado de Usuarios</h3>
        <div class="d-flex gap-2">
            <a href="./register" class="btn btn-primary">
                <i class="fas fa-user-plus"></i> Nuevo Usuario
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table datatable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th>Rol</th>
                        <th>Teléfono</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($usuarios)): ?>
                    <tr>
                        <td colspan="7" class="text-center">No hay usuarios registrados</td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($usuarios as $usuario): ?>
                    <tr>
                        <td>#<?= $usuario['id'] ?></td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="user-avatar-small me-2">
                                    <?= strtoupper(substr($usuario['nombre'], 0, 2)) ?>
                                </div>
                                <?= htmlspecialchars($usuario['nombre']) ?>
                            </div>
                        </td>
                        <td><?= htmlspecialchars($usuario['correo']) ?></td>
                        <td>
                            <?php
                            $roles = [
                                'administrador' => '<span class="badge badge-danger">Administrador</span>',
                                'cajero' => '<span class="badge badge-primary">Cajero</span>',
                                'empleado' => '<span class="badge badge-secondary">Empleado</span>'
                            ];
                            echo $roles[$usuario['rol']] ?? ucfirst($usuario['rol']);
                            ?>
                        </td>
                        <td><?= htmlspecialchars($usuario['telefono'] ?? '-') ?></td>
                        <td>
                            <span class="badge <?= $usuario['estado'] === 'activo' ? 'badge-success' : 'badge-danger' ?>">
                                <?= ucfirst($usuario['estado']) ?>
                            </span>
                        </td>
                        <td>
                            <div class="btn-group">
                                <a href="./usuarios/actividad?usuario_id=<?= $usuario['id'] ?>" 
                                   class="btn btn-sm btn-info" 
                                   title="Ver actividad">
                                    <i class="fas fa-history"></i>
                                </a>
                                
                                <?php if ($usuario['id'] != $_SESSION['user_id']): ?>
                                <button onclick="cambiarEstado(<?= $usuario['id'] ?>, '<?= $usuario['estado'] === 'activo' ? 'inactivo' : 'activo' ?>')" 
                                        class="btn btn-sm <?= $usuario['estado'] === 'activo' ? 'btn-warning' : 'btn-success' ?>"
                                        title="<?= $usuario['estado'] === 'activo' ? 'Desactivar' : 'Activar' ?>">
                                    <i class="fas fa-<?= $usuario['estado'] === 'activo' ? 'ban' : 'check' ?>"></i>
                                </button>
                                <?php endif; ?>
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

<style>
.user-avatar-small {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: linear-gradient(135deg, #D4A574, #8B6F47);
    color: white;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    font-weight: bold;
}
</style>

<script>
async function cambiarEstado(usuarioId, nuevoEstado) {
    const result = await Swal.fire({
        title: '¿Estás seguro?',
        text: `¿Deseas ${nuevoEstado === 'activo' ? 'activar' : 'desactivar'} este usuario?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#D4A574',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, continuar',
        cancelButtonText: 'Cancelar'
    });
    
    if (result.isConfirmed) {
        try {
            const response = await fetch('/usuarios/cambiar-estado', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({
                    usuario_id: usuarioId,
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
            Swal.fire('Error', 'Error al cambiar el estado del usuario', 'error');
        }
    }
}
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
