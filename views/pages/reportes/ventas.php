<?php 
$pageTitle = 'Reporte de Ventas';
$currentPage = 'reportes';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="page-header">
    <div>
        <h2 class="page-title">Reporte de Ventas</h2>
        <p class="page-subtitle">Período: <?= date('d/m/Y', strtotime($fecha_inicio)) ?> - <?= date('d/m/Y', strtotime($fecha_fin)) ?></p>
    </div>
    <div class="d-flex gap-2">
        <a href="./reportes/ventas?fecha_inicio=<?= $fecha_inicio ?>&fecha_fin=<?= $fecha_fin ?>&formato=pdf" class="btn btn-danger" target="_blank">
            <i class="fas fa-file-pdf"></i> Exportar PDF
        </a>
        <a href="./reportes" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>
</div>

<!-- Tarjetas de Estadísticas -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card">
            <div class="card-body text-center">
                <i class="fas fa-shopping-cart fa-3x text-primary mb-2"></i>
                <h3 class="mb-0"><?= $cantidad_ventas ?></h3>
                <p class="text-muted mb-0">Total de Ventas</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card">
            <div class="card-body text-center">
                <i class="fas fa-dollar-sign fa-3x text-success mb-2"></i>
                <h3 class="mb-0">S/ <?= number_format($total_ventas, 2) ?></h3>
                <p class="text-muted mb-0">Monto Total</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card">
            <div class="card-body text-center">
                <i class="fas fa-chart-line fa-3x text-info mb-2"></i>
                <h3 class="mb-0">S/ <?= number_format($promedio, 2) ?></h3>
                <p class="text-muted mb-0">Promedio por Venta</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card">
            <div class="card-body text-center">
                <i class="fas fa-calendar fa-3x text-warning mb-2"></i>
                <h3 class="mb-0"><?= ceil((strtotime($fecha_fin) - strtotime($fecha_inicio)) / 86400) + 1 ?></h3>
                <p class="text-muted mb-0">Días del Período</p>
            </div>
        </div>
    </div>
</div>

<!-- Tabla de Ventas -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Detalle de Ventas</h3>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Fecha</th>
                        <th>Cliente</th>
                        <th>Total</th>
                        <th>Método de Pago</th>
                        <th>Usuario</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($ventas)): ?>
                    <tr>
                        <td colspan="6" class="text-center">No hay ventas en este período</td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($ventas as $venta): ?>
                    <tr>
                        <td>#<?= str_pad($venta['id'], 6, '0', STR_PAD_LEFT) ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($venta['fecha_venta'])) ?></td>
                        <td><?= htmlspecialchars($venta['cliente_nombre'] ?? 'Cliente General') ?></td>
                        <td><strong>S/ <?= number_format($venta['total'], 2) ?></strong></td>
                        <td>
                            <?php
                            $metodos = [
                                'efectivo' => '<span class="badge badge-success">Efectivo</span>',
                                'tarjeta' => '<span class="badge badge-info">Tarjeta</span>',
                                'transferencia' => '<span class="badge badge-primary">Transferencia</span>',
                                'yape' => '<span class="badge badge-warning">Yape</span>',
                                'plin' => '<span class="badge badge-secondary">Plin</span>'
                            ];
                            echo $metodos[$venta['metodo_pago']] ?? ucfirst($venta['metodo_pago']);
                            ?>
                        </td>
                        <td><?= htmlspecialchars($venta['usuario_nombre'] ?? '-') ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
                <tfoot>
                    <tr class="table-success">
                        <td colspan="3" class="text-end"><strong>TOTALES:</strong></td>
                        <td><strong>S/ <?= number_format($total_ventas, 2) ?></strong></td>
                        <td colspan="2"><strong><?= $cantidad_ventas ?> ventas</strong></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<!-- Gráfico de Ventas por Método de Pago -->
<div class="card mt-4">
    <div class="card-header">
        <h3 class="card-title">Ventas por Método de Pago</h3>
    </div>
    <div class="card-body">
        <canvas id="chartMetodosPago" height="80"></canvas>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Preparar datos para el gráfico
const ventasPorMetodo = {};
<?php foreach ($ventas as $venta): ?>
    const metodo = '<?= ucfirst($venta['metodo_pago']) ?>';
    if (!ventasPorMetodo[metodo]) {
        ventasPorMetodo[metodo] = 0;
    }
    ventasPorMetodo[metodo] += <?= $venta['total'] ?>;
<?php endforeach; ?>

const labels = Object.keys(ventasPorMetodo);
const data = Object.values(ventasPorMetodo);

const ctx = document.getElementById('chartMetodosPago').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: labels,
        datasets: [{
            label: 'Monto Total (S/)',
            data: data,
            backgroundColor: [
                'rgba(40, 167, 69, 0.8)',
                'rgba(23, 162, 184, 0.8)',
                'rgba(0, 123, 255, 0.8)',
                'rgba(255, 193, 7, 0.8)',
                'rgba(108, 117, 125, 0.8)'
            ],
            borderColor: [
                'rgba(40, 167, 69, 1)',
                'rgba(23, 162, 184, 1)',
                'rgba(0, 123, 255, 1)',
                'rgba(255, 193, 7, 1)',
                'rgba(108, 117, 125, 1)'
            ],
            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return 'S/ ' + context.parsed.y.toFixed(2);
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return 'S/ ' + value.toFixed(2);
                    }
                }
            }
        }
    }
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
