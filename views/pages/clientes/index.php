<?php 
$pageTitle = 'Clientes';
$currentPage = 'clientes';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="page-header d-flex justify-between align-center">
    <div>
        <h2 class="page-title">Clientes</h2>
        <p class="page-subtitle">Gestión de clientes</p>
    </div>
    <a href="./clientes/create" class="btn btn-primary">
        <i class="fas fa-user-plus"></i> Nuevo Cliente
    </a>
</div>

<div class="card" x-data="clientesApp()">
    <div class="card-header">
        <div class="d-flex justify-between align-center">
            <h3 class="card-title">Listado de Clientes</h3>
            <input type="text" 
                   class="form-control" 
                   placeholder="Buscar cliente..." 
                   x-model="searchQuery"
                   @input.debounce.500ms="buscar()"
                   style="width: 300px;">
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table datatable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre Completo</th>
                        <th>Documento</th>
                        <th>Teléfono</th>
                        <th>Correo</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($clientes)): ?>
                    <tr>
                        <td colspan="7" class="text-center">No hay clientes registrados</td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($clientes as $cliente): ?>
                    <tr>
                        <td><?= $cliente['id'] ?></td>
                        <td><strong><?= htmlspecialchars($cliente['nombre'] . ' ' . $cliente['apellido']) ?></strong></td>
                        <td><?= htmlspecialchars($cliente['documento_tipo'] . ': ' . $cliente['documento_numero']) ?></td>
                        <td><?= htmlspecialchars($cliente['telefono'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($cliente['correo'] ?? '-') ?></td>
                        <td>
                            <?php if ($cliente['estado'] === 'activo'): ?>
                            <span class="badge badge-success">Activo</span>
                            <?php else: ?>
                            <span class="badge badge-danger">Inactivo</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="./clientes/show/<?= $cliente['id'] ?>" class="btn btn-sm btn-info" title="Ver detalle">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="./clientes/edit/<?= $cliente['id'] ?>" class="btn btn-sm btn-warning" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <?php if ($_SESSION['user_rol'] === 'administrador'): ?>
                                <button @click="eliminar(<?= $cliente['id'] ?>)" class="btn btn-sm btn-danger" title="Eliminar">
                                    <i class="fas fa-trash"></i>
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

<script>
function clientesApp() {
    return {
        searchQuery: '',
        
        async buscar() {
            if (this.searchQuery.length === 0) {
                window.location.reload();
                return;
            }
            
            try {
                const response = await fetch(`/clientes/search?q=${encodeURIComponent(this.searchQuery)}`);
                const data = await response.json();
                
                if (data.success) {
                    console.log('Resultados:', data.clientes);
                }
            } catch (error) {
                console.error('Error al buscar:', error);
            }
        },
        
        async eliminar(id) {
            const confirmed = await SIPAN.confirm('¿Eliminar este cliente?', '¿Estás seguro?');
            if (!confirmed) return;
            
            try {
                const response = await fetch(`/clientes/delete/${id}`, {
                    method: 'POST'
                });
                
                const data = await response.json();
                
                if (data.success) {
                    SIPAN.success(data.message);
                    setTimeout(() => window.location.reload(), 1500);
                } else {
                    SIPAN.error(data.message);
                }
            } catch (error) {
                SIPAN.error('Error al eliminar cliente');
            }
        }
    }
}
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
