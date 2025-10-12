<?php 
$pageTitle = 'Producciones';
$currentPage = 'producciones';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="page-header d-flex justify-between align-center">
    <div>
        <h2 class="page-title">Producciones</h2>
        <p class="page-subtitle">Registro de lotes de producción</p>
    </div>
    <a href="./producciones/create" class="btn btn-warning">
        <i class="fas fa-industry"></i> Nueva Producción
    </a>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Listado de Producciones</h3>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table datatable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Fecha</th>
                        <th>Producto</th>
                        <th>Cantidad Producida</th>
                        <th>Costo Total</th>
                        <th>Usuario</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($producciones)): ?>
                    <tr>
                        <td colspan="7" class="text-center">No hay producciones registradas</td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($producciones as $produccion): ?>
                    <tr>
                        <td><strong>#<?= str_pad($produccion['id'], 6, '0', STR_PAD_LEFT) ?></strong></td>
                        <td><?= date('d/m/Y H:i', strtotime($produccion['fecha_produccion'])) ?></td>
                        <td><?= htmlspecialchars($produccion['producto_nombre'] ?? 'Producto #' . $produccion['id_producto']) ?></td>
                        <td><strong><?= $produccion['cantidad_producida'] ?></strong> unidades</td>
                        <td><strong>S/ <?= number_format($produccion['costo_total'], 2) ?></strong></td>
                        <td><?= htmlspecialchars($produccion['usuario_nombre'] ?? '-') ?></td>
                        <td>
                            <a href="./producciones/show/<?= $produccion['id'] ?>" class="btn btn-sm btn-info" title="Ver detalle">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
