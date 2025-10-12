<?php 
$pageTitle = 'Productos';
$currentPage = 'productos';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="page-header d-flex justify-between align-center">
    <div>
        <h2 class="page-title">Productos</h2>
        <p class="page-subtitle">Gestión de productos de la panadería</p>
    </div>
    <a href="./productos/create" class="btn btn-primary">
        <i class="fas fa-plus"></i> Nuevo Producto
    </a>
</div>

<div class="card" x-data="productosApp()">
    <div class="card-header">
        <div class="d-flex justify-between align-center">
            <h3 class="card-title">Listado de Productos</h3>
            <div class="d-flex gap-2">
                <input type="text" 
                       class="form-control" 
                       placeholder="Buscar producto..." 
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
                        <th>Stock Actual</th>
                        <th>Stock Mínimo</th>
                        <th>Precio</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($productos)): ?>
                    <tr>
                        <td colspan="7" class="text-center">No hay productos registrados</td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($productos as $producto): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($producto['nombre']) ?></strong></td>
                        <td><?= htmlspecialchars($producto['descripcion'] ?? '-') ?></td>
                        <td>
                            <?php if ($producto['stock_actual'] <= $producto['stock_minimo']): ?>
                            <span class="badge badge-danger"><?= $producto['stock_actual'] ?></span>
                            <?php else: ?>
                            <span class="badge badge-success"><?= $producto['stock_actual'] ?></span>
                            <?php endif; ?>
                        </td>
                        <td><?= $producto['stock_minimo'] ?></td>
                        <td><strong>S/ <?= number_format($producto['precio_actual'], 2) ?></strong></td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="./productos/edit/<?= $producto['id'] ?>" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <?php if ($_SESSION['user_rol'] === 'administrador'): ?>
                                <button @click="eliminar(<?= $producto['id'] ?>)" class="btn btn-sm btn-danger">
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
function productosApp() {
    return {
        searchQuery: '',
        
        async buscar() {
            if (this.searchQuery.length === 0) {
                window.location.reload();
                return;
            }
            
            try {
                const response = await fetch(`/productos/search?q=${encodeURIComponent(this.searchQuery)}`);
                const data = await response.json();
                
                if (data.success) {
                    // Actualizar tabla con resultados
                    console.log('Resultados:', data.productos);
                }
            } catch (error) {
                console.error('Error al buscar:', error);
            }
        },
        
        async verificarStockBajo() {
            try {
                const response = await fetch('/productos/stock-bajo');
                const data = await response.json();
                
                if (data.success) {
                    if (data.productos.length === 0) {
                        SIPAN.info('No hay productos con stock bajo');
                    } else {
                        let html = '<ul style="text-align: left;">';
                        data.productos.forEach(p => {
                            html += `<li><strong>${p.nombre}</strong>: ${p.stock_actual} (mínimo: ${p.stock_minimo})</li>`;
                        });
                        html += '</ul>';
                        
                        Swal.fire({
                            icon: 'warning',
                            title: 'Productos con Stock Bajo',
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
            const confirmed = await SIPAN.confirm('¿Eliminar este producto?', '¿Estás seguro?');
            if (!confirmed) return;
            
            try {
                const response = await fetch(`/productos/delete/${id}`, {
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
                SIPAN.error('Error al eliminar producto');
            }
        }
    }
}
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
