<?php 
$pageTitle = 'Ventas';
$currentPage = 'ventas';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="page-header d-flex justify-between align-center">
    <div>
        <h2 class="page-title">Ventas</h2>
        <p class="page-subtitle">Historial de ventas realizadas</p>
    </div>
    <a href="./ventas/create" class="btn btn-success">
        <i class="fas fa-cash-register"></i> Nueva Venta
    </a>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Listado de Ventas</h3>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table datatable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Fecha</th>
                        <th>Cliente</th>
                        <th>Total</th>
                        <th>Método de Pago</th>
                        <th>Usuario</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($ventas)): ?>
                    <tr>
                        <td colspan="7" class="text-center">No hay ventas registradas</td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($ventas as $venta): ?>
                    <tr>
                        <td><strong>#<?= str_pad($venta['id'], 6, '0', STR_PAD_LEFT) ?></strong></td>
                        <td><?= date('d/m/Y H:i', strtotime($venta['fecha_venta'])) ?></td>
                        <td><?= htmlspecialchars($venta['cliente_nombre'] ?? 'Cliente General') ?></td>
                        <td><strong class="text-success">S/ <?= number_format($venta['total'], 2) ?></strong></td>
                        <td>
                            <?php
                            $metodos = [
                                'efectivo' => '<span class="badge badge-success">Efectivo</span>',
                                'tarjeta' => '<span class="badge badge-info">Tarjeta</span>',
                                'transferencia' => '<span class="badge badge-primary">Transferencia</span>',
                                'yape' => '<span class="badge badge-warning">Yape</span>',
                                'plin' => '<span class="badge badge-secondary">Plin</span>'
                            ];
                            echo $metodos[$venta['metodo_pago']] ?? $venta['metodo_pago'];
                            ?>
                        </td>
                        <td><?= htmlspecialchars($venta['usuario_nombre'] ?? '-') ?></td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="./ventas/show/<?= $venta['id'] ?>" class="btn btn-sm btn-info" title="Ver detalle">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="./ventas/ticket/<?= $venta['id'] ?>" class="btn btn-sm btn-secondary" title="Imprimir ticket" target="_blank">
                                    <i class="fas fa-print"></i>
                                </a>
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

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
