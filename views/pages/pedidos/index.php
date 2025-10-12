<?php 
$pageTitle = 'Pedidos';
$currentPage = 'pedidos';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="page-header d-flex justify-between align-center">
    <div>
        <h2 class="page-title">Pedidos</h2>
        <p class="page-subtitle">Gestión de pedidos de clientes</p>
    </div>
    <a href="/pedidos/create" class="btn btn-primary">
        <i class="fas fa-plus"></i> Nuevo Pedido
    </a>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Listado de Pedidos</h3>
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
                        <th>Estado</th>
                        <th>Estado de Pago</th>
                        <th>Fecha Entrega</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($pedidos)): ?>
                    <tr>
                        <td colspan="8" class="text-center">No hay pedidos registrados</td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($pedidos as $pedido): ?>
                    <tr>
                        <td><strong>#<?= str_pad($pedido['id'], 6, '0', STR_PAD_LEFT) ?></strong></td>
                        <td><?= date('d/m/Y', strtotime($pedido['fecha_pedido'])) ?></td>
                        <td><?= htmlspecialchars($pedido['cliente_nombre'] ?? 'Cliente #' . $pedido['id_cliente']) ?></td>
                        <td><strong>S/ <?= number_format($pedido['total'], 2) ?></strong></td>
                        <td>
                            <?php
                            $estados = [
                                'pendiente' => '<span class="badge badge-warning">Pendiente</span>',
                                'en_proceso' => '<span class="badge badge-info">En Proceso</span>',
                                'completado' => '<span class="badge badge-success">Completado</span>',
                                'cancelado' => '<span class="badge badge-danger">Cancelado</span>'
                            ];
                            echo $estados[$pedido['estado']] ?? $pedido['estado'];
                            ?>
                        </td>
                        <td>
                            <?php
                            $pagos = [
                                'pendiente' => '<span class="badge badge-danger">Pendiente</span>',
                                'abonado' => '<span class="badge badge-warning">Abonado</span>',
                                'pagado' => '<span class="badge badge-success">Pagado</span>'
                            ];
                            echo $pagos[$pedido['estado_pago']] ?? $pedido['estado_pago'];
                            ?>
                        </td>
                        <td><?= date('d/m/Y', strtotime($pedido['fecha_entrega'])) ?></td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="/pedidos/show/<?= $pedido['id'] ?>" class="btn btn-sm btn-info" title="Ver detalle">
                                    <i class="fas fa-eye"></i>
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
