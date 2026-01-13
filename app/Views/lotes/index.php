<?php 
$pageTitle = 'Gestión de Lotes';
$currentPage = 'inventario'; 
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="page-header">
    <h2 class="page-title">Control de Vencimientos y Lotes</h2>
    <p class="page-subtitle">Monitoreo de lotes activos y fechas de caducidad (FIFO/FEFO)</p>
</div>

<div class="card">
    <div class="card-body">
        
        <!-- Alerta de próximos vencimientos -->
        <?php
        $porVencer = array_filter($lotes, function($l) {
            $dias = (strtotime($l['fecha_vencimiento']) - time()) / (60 * 60 * 24);
            return $dias <= 7 && $dias >= 0;
        });
        ?>
        
        <?php if (!empty($porVencer)): ?>
        <div class="alert alert-warning mb-4">
            <h5 class="alert-heading"><i class="fas fa-exclamation-triangle"></i> Insumos/Productos Próximos a Vencer (7 días)</h5>
            <ul class="mb-0">
                <?php foreach ($porVencer as $pv): ?>
                <li>
                    <strong><?= htmlspecialchars($pv['nombre_item']) ?></strong> 
                    (Lote: <?= $pv['codigo_lote'] ?>) - Vence el <?= date('d/m/Y', strtotime($pv['fecha_vencimiento'])) ?>
                    <span class="badge bg-dark"><?= $pv['cantidad_actual'] ?> unid.</span>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <div class="table-responsive">
            <table class="table datatable table-hover">
                <thead>
                    <tr>
                        <th>Lote</th>
                        <th>Item</th>
                        <th>Tipo</th>
                        <th>Fecha Entrada</th>
                        <th>Vencimiento</th>
                        <th>Días Rest.</th>
                        <th>Stock Actual</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($lotes as $lote): ?>
                    <?php 
                        $diasRestantes = ceil((strtotime($lote['fecha_vencimiento']) - time()) / (60 * 60 * 24));
                        $rowClass = '';
                        if ($diasRestantes < 0) $rowClass = 'table-danger';
                        elseif ($diasRestantes <= 7) $rowClass = 'table-warning';
                    ?>
                    <tr class="<?= $rowClass ?>">
                        <td><strong><?= htmlspecialchars($lote['codigo_lote']) ?></strong></td>
                        <td><?= htmlspecialchars($lote['nombre_item']) ?></td>
                        <td><span class="badge bg-secondary"><?= ucfirst($lote['tipo']) ?></span></td>
                        <td><?= date('d/m/Y', strtotime($lote['fecha_entrada'])) ?></td>
                        <td><?= date('d/m/Y', strtotime($lote['fecha_vencimiento'])) ?></td>
                        <td class="fw-bold">
                            <?php if ($diasRestantes < 0): ?>
                                <span class="text-danger">Vencido (<?= abs($diasRestantes) ?> días)</span>
                            <?php else: ?>
                                <?= $diasRestantes ?> días
                            <?php endif; ?>
                        </td>
                        <td><?= $lote['cantidad_actual'] ?></td>
                        <td><?= ucfirst($lote['estado']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
