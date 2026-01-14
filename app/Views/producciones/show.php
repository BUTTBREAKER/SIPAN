<?php

require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/sidebar.php';

?>

<div class="main-content">
    <div class="content-header">
        <h1><i class="fas fa-industry"></i> Detalle de Producción #<?= $produccion['id'] ?></h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="/producciones">Producciones</a></li>
                <li class="breadcrumb-item active">Detalle</li>
            </ol>
        </nav>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-info-circle"></i> Información de la Producción</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Producto:</strong><br>
                            <?= htmlspecialchars($producto['nombre']) ?>
                        </div>
                        <div class="col-md-6">
                            <strong>Cantidad Producida:</strong><br>
                            <?= number_format($produccion['cantidad_producida'], 2) ?> unidades
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Fecha de Producción:</strong><br>
                            <?= date('d/m/Y H:i', strtotime($produccion['fecha_produccion'])) ?>
                        </div>
                        <div class="col-md-6">
                            <strong>Usuario:</strong><br>
                            <?= htmlspecialchars("{$usuario['primer_nombre']} {$usuario['apellido_paterno']}") ?>
                        </div>
                    </div>
                    
                    <?php if (!empty($produccion['observaciones'])) : ?>
                    <div class="row mb-3">
                        <div class="col-12">
                            <strong>Observaciones:</strong><br>
                            <?= nl2br(htmlspecialchars($produccion['observaciones'])) ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="card mt-3">
                <div class="card-header">
                    <h5><i class="fas fa-box"></i> Insumos Utilizados</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Insumo</th>
                                    <th>Cantidad Utilizada</th>
                                    <th>Unidad</th>
                                    <th>Costo Unitario</th>
                                    <th>Costo Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $costo_total = 0;
                                foreach ($insumos as $insumo) :
                                    $costo_insumo = $insumo['cantidad_utilizada'] * $insumo['precio_unitario'];
                                    $costo_total += $costo_insumo;
                                    ?>
                                <tr>
                                    <td><?= htmlspecialchars($insumo['nombre']) ?></td>
                                    <td><?= number_format($insumo['cantidad_utilizada'], 2) ?></td>
                                    <td><?= htmlspecialchars($insumo['unidad_medida']) ?></td>
                                    <td>$ <?= number_format($insumo['precio_unitario'], 2) ?></td>
                                    <td>$ <?= number_format($costo_insumo, 2) ?></td>
                                </tr>
                                <?php endforeach; ?>
                                <?php if (empty($insumos_utilizados)) : ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted">No hay insumos registrados</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                            <?php if (!empty($insumos_utilizados)) : ?>
                            <tfoot>
                                <tr class="table-info">
                                    <th colspan="4" class="text-end">Costo Total de Producción:</th>
                                    <th>$ <?= number_format($costo_total, 2) ?></th>
                                </tr>
                                <tr class="table-success">
                                    <th colspan="4" class="text-end">Costo por Unidad:</th>
                                    <th>$ <?= number_format($costo_total / $produccion['cantidad'], 2) ?></th>
                                </tr>
                            </tfoot>
                            <?php endif; ?>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-calculator"></i> Resumen de Costos</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($insumos_utilizados)) : ?>
                    <div class="mb-3 p-3 bg-light rounded">
                        <div class="d-flex justify-content-between mb-2">
                            <strong>Cantidad Producida:</strong>
                            <span><?= number_format($produccion['cantidad'], 2) ?> unidades</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <strong>Costo Total:</strong>
                            <span class="text-primary">$ <?= number_format($costo_total, 2) ?></span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between">
                            <strong>Costo por Unidad:</strong>
                            <span class="text-success fs-5">$ <?= number_format($costo_total / $produccion['cantidad'], 2) ?></span>
                        </div>
                    </div>
                    <?php else : ?>
                    <p class="text-muted">No hay datos de costos disponibles</p>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="card mt-3">
                <div class="card-header">
                    <h5><i class="fas fa-cog"></i> Acciones</h5>
                </div>
                <div class="card-body">
                    <a href="/producciones" class="btn btn-secondary w-100 mb-2">
                        <i class="fas fa-arrow-left"></i> Volver al Listado
                    </a>
                    <button onclick="window.print()" class="btn btn-info w-100">
                        <i class="fas fa-print"></i> Imprimir
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

