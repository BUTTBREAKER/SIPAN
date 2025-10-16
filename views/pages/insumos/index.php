<?php 
$pageTitle = 'Insumos';
$currentPage = 'insumos';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="page-header d-flex justify-between align-center">
    <div>
        <h2 class="page-title">Insumos</h2>
        <p class="page-subtitle">Gestión de materias primas e insumos</p>
    </div>
    <a href="./insumos/create" class="btn btn-primary">
        <i class="fas fa-plus"></i> Nuevo Insumo
    </a>
</div>

<div class="card" x-data="insumosApp()">
    <div class="card-header">
        <div class="d-flex justify-between align-center">
            <h3 class="card-title">Listado de Insumos</h3>
            <div class="d-flex gap-2">
                <input type="text" 
                       class="form-control" 
                       placeholder="Buscar insumo..." 
                       x-model="searchQuery"
                       @input.debounce.500ms="buscar()"
                       style="width: 300px;">
                <button @click="verificarStockBajo()" class="btn btn-warning">
                    <i class="fas fa-exclamation-triangle"></i> Stock Bajo
                </button>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table datatable">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Unidad</th>
                        <th>Stock Actual</th>
                        <th>Stock Mínimo</th>
                        <th>Precio Unitario</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($insumos)): ?>
                    <tr>
                        <td colspan="8" class="text-center">No hay insumos registrados</td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($insumos as $insumo): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($insumo['nombre']) ?></strong></td>
                        <td><?= htmlspecialchars($insumo['descripcion'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($insumo['unidad_medida']) ?></td>
                        <td>
                            <?php if ($insumo['stock_actual'] <= $insumo['stock_minimo']): ?>
                            <span class="badge badge-danger"><?= $insumo['stock_actual'] ?></span>
                            <?php else: ?>
                            <span class="badge badge-success"><?= $insumo['stock_actual'] ?></span>
                            <?php endif; ?>
                        </td>
                        <td><?= $insumo['stock_minimo'] ?></td>
                        <td><strong>S/ <?= number_format($insumo['precio_unitario'], 2) ?></strong></td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="./insumos/edit/<?= $insumo['id'] ?>" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <?php if ($_SESSION['user_rol'] === 'administrador'): ?>
                                <button @click="eliminar(<?= $insumo['id'] ?>)" class="btn btn-sm btn-danger">
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
function insumosApp() {
    return {
        searchQuery: '',
        
        async buscar() {
            if (this.searchQuery.length === 0) {
                window.location.reload();
                return;
            }
            
            try {
                const response = await fetch(`/insumos/search?q=${encodeURIComponent(this.searchQuery)}`);
                const data = await response.json();
                
                if (data.success) {
                    console.log('Resultados:', data.insumos);
                }
            } catch (error) {
                console.error('Error al buscar:', error);
            }
        },
        
        async verificarStockBajo() {
            try {
                const response = await fetch(App::getUrl('insumos.stock-bajo'));
                const data = await response.json();
                
                if (data.success) {
                    if (data.insumos.length === 0) {
                        SIPAN.info('No hay insumos con stock bajo');
                    } else {
                        let html = '<ul style="text-align: left;">';
                        data.insumos.forEach(i => {
                            html += `<li><strong>${i.nombre}</strong>: ${i.stock_actual} ${i.unidad_medida} (mínimo: ${i.stock_minimo})</li>`;
                        });
                        html += '</ul>';
                        
                        Swal.fire({
                            icon: 'warning',
                            title: 'Insumos con Stock Bajo',
                            html: html,
                            confirmButtonColor: '#D4A574'
                        });
                    }
                }
            } catch (error) {
                SIPAN.error('Error al verificar stock');
            }
        },
        
        async eliminar(id) {
            const confirmed = await SIPAN.confirm('¿Eliminar este insumo?', '¿Estás seguro?');
            if (!confirmed) return;
            
            try {
                const response = await fetch(`/insumos/delete/${id}`, {
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
                SIPAN.error('Error al eliminar insumo');
            }
        }
    }
}
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
