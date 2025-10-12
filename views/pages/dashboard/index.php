<?php 
$pageTitle = 'Dashboard';
$currentPage = 'dashboard';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="page-header">
    <h2 class="page-title">Dashboard</h2>
    <p class="page-subtitle">Bienvenido al Sistema Integral para Panaderías</p>
</div>

<!-- Estadísticas principales -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="stat-card success">
            <div class="stat-content">
                <div class="stat-label">Ventas Hoy</div>
                <div class="stat-value">S/ <?= number_format($data['ventas_hoy'] ?? 0, 2) ?></div>
            </div>
            <div class="stat-icon"><i class="fas fa-dollar-sign"></i></div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="stat-card info">
            <div class="stat-content">
                <div class="stat-label">Ventas Semana</div>
                <div class="stat-value">S/ <?= number_format($data['ventas_semana'] ?? 0, 2) ?></div>
            </div>
            <div class="stat-icon"><i class="fas fa-chart-line"></i></div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="stat-card warning">
            <div class="stat-content">
                <div class="stat-label">Ventas Mes</div>
                <div class="stat-value">S/ <?= number_format($data['ventas_mes'] ?? 0, 2) ?></div>
            </div>
            <div class="stat-icon"><i class="fas fa-calendar-alt"></i></div>
        </div>
    </div>
    
    <div class="stat-card danger">
            <div class="stat-content">
                <div class="stat-label">Productos Stock Bajo</div>
                <div class="stat-value"><?= count($data['productos_stock_bajo'] ?? []) ?></div>
            </div>
            <div class="stat-icon"><i class="fas fa-exclamation-triangle"></i></div>
        </div>
    </div>
</div>

<!-- Alertas de Stock -->
<div class="row g-4 mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-box"></i> Productos con Stock Bajo
                </h3>
                <a href="/productos" class="btn btn-sm btn-warning">Ver Todos</a>
            </div>
            <div class="card-body">
                <?php if (empty($data['productos_stock_bajo'])): ?>
                <div class="alert alert-success mb-0">
                    <i class="fas fa-check-circle"></i> No hay productos con stock bajo
                </div>
                <?php else: ?>
                <div class="list-group">
                    <?php foreach (array_slice($data['productos_stock_bajo'], 0, 5) as $producto): ?>
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <strong><?= htmlspecialchars($producto['nombre']) ?></strong>
                            <br>
                            <small class="text-muted">
                                Stock: <span class="badge bg-danger"><?= $producto['stock_actual'] ?></span>
                                / Mínimo: <?= $producto['stock_minimo'] ?>
                            </small>
                        </div>
                        <a href="/productos/edit/<?= $producto['id'] ?>" class="btn btn-sm btn-warning">
                            <i class="fas fa-edit"></i>
                        </a>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-cubes"></i> Insumos con Stock Bajo
                </h3>
                <a href="/insumos" class="btn btn-sm btn-warning">Ver Todos</a>
            </div>
            <div class="card-body">
                <?php if (empty($data['insumos_stock_bajo'])): ?>
                <div class="alert alert-success mb-0">
                    <i class="fas fa-check-circle"></i> No hay insumos con stock bajo
                </div>
                <?php else: ?>
                <div class="list-group">
                    <?php foreach (array_slice($data['insumos_stock_bajo'], 0, 5) as $insumo): ?>
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <strong><?= htmlspecialchars($insumo['nombre']) ?></strong>
                            <br>
                            <small class="text-muted">
                                Stock: <span class="badge bg-danger"><?= $insumo['stock_actual'] ?></span>
                                / Mínimo: <?= $insumo['stock_minimo'] ?>
                            </small>
                        </div>
                        <a href="/insumos/edit/<?= $insumo['id'] ?>" class="btn btn-sm btn-warning">
                            <i class="fas fa-edit"></i>
                        </a>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Accesos Rápidos -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-bolt"></i> Accesos Rápidos
        </h3>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-2 col-sm-4 col-6">
                <a href="/ventas/create" class="btn btn-success w-100 py-3">
                    <i class="fas fa-cash-register fa-2x mb-2"></i>
                    <br>
                    <span>Nueva Venta</span>
                </a>
            </div>
            <div class="col-md-2 col-sm-4 col-6">
                <a href="/pedidos/create" class="btn btn-warning w-100 py-3">
                    <i class="fas fa-shopping-cart fa-2x mb-2"></i>
                    <br>
                    <span>Nuevo Pedido</span>
                </a>
            </div>
            <div class="col-md-2 col-sm-4 col-6">
                <a href="/producciones/create" class="btn btn-primary w-100 py-3">
                    <i class="fas fa-industry fa-2x mb-2"></i>
                    <br>
                    <span>Nueva Producción</span>
                </a>
            </div>
            <div class="col-md-2 col-sm-4 col-6">
                <a href="/clientes/create" class="btn btn-info w-100 py-3">
                    <i class="fas fa-user-plus fa-2x mb-2"></i>
                    <br>
                    <span>Nuevo Cliente</span>
                </a>
            </div>
            <div class="col-md-2 col-sm-4 col-6">
                <a href="/sugerencias" class="btn btn-secondary w-100 py-3">
                    <i class="fas fa-lightbulb fa-2x mb-2"></i>
                    <br>
                    <span>Sugerencias de Compra</span>
                </a>
            </div>
            <div class="col-md-2 col-sm-4 col-6">
                <a href="/reportes" class="btn btn-dark w-100 py-3">
                    <i class="fas fa-file-alt fa-2x mb-2"></i>
                    <br>
                    <span>Reportes</span>
                </a>
            </div>
        </div>
    </div>
</div>

<script>
// Verificar stock bajo al cargar
document.addEventListener('DOMContentLoaded', () => {
    <?php if (!empty($data['productos_stock_bajo']) || !empty($data['insumos_stock_bajo'])): ?>
    const totalBajo = <?= count($data['productos_stock_bajo'] ?? []) + count($data['insumos_stock_bajo'] ?? []) ?>;
    
    Swal.fire({
        icon: 'warning',
        title: 'Alerta de Stock Bajo',
        html: `<p>Hay <strong>${totalBajo}</strong> producto(s)/insumo(s) con stock bajo.</p>
               <p>Revisa las alertas en el dashboard.</p>`,
        confirmButtonColor: '#D4A574',
        confirmButtonText: 'Entendido'
    });
    <?php endif; ?>
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

