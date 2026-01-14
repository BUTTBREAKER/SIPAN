<?php

use App\SIPAN;

$pageTitle = 'Dashboard';
$currentPage = 'dashboard';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="page-header d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="page-title">Dashboard</h2>
        <p class="page-subtitle">Bienvenido al Sistema Integral para Panaderías</p>
    </div>
    <div class="text-end">
        <h5 class="m-0 brand-font" style="font-weight:700;"><?= SIPAN::formatDate(date('Y-m-d')) ?></h5>
        <small class="text-muted" id="reloj">--:--:--</small>
    </div>
</div>

<!-- Bento Grid Layout -->
<div class="bento-grid">
    
    <!-- 1. Estadísticas (Top Row) -->
    <?php if ($user_rol !== 'cajero') : ?>
        <div class="stat-card success span-1">
            <div class="stat-content">
                <div class="stat-label">Ventas Hoy</div>
                <div class="stat-value">$ <?= number_format($data['ventas_hoy'] ?? 0, 2) ?></div>
            </div>
            <div class="stat-icon"><i class="fas fa-dollar-sign"></i></div>
        </div>

        <div class="stat-card info span-1">
            <div class="stat-content">
                <div class="stat-label">Ventas Semana</div>
                <div class="stat-value">$ <?= number_format($data['ventas_semana'] ?? 0, 2) ?></div>
            </div>
            <div class="stat-icon"><i class="fas fa-chart-line"></i></div>
        </div>

        <div class="stat-card warning span-1">
            <div class="stat-content">
                <div class="stat-label">Ventas Mes</div>
                <div class="stat-value">$ <?= number_format($data['ventas_mes'] ?? 0, 2) ?></div>
            </div>
            <div class="stat-icon"><i class="fas fa-calendar-alt"></i></div>
        </div>

        <div class="stat-card danger span-1">
            <div class="stat-content">
                <div class="stat-label">Alertas</div>
                <div class="stat-value">
                     <?= count($data['productos_stock_bajo'] ?? []) + count($data['lotes_por_vencer'] ?? []) ?>
                </div>
            </div>
            <div class="stat-icon"><i class="fas fa-exclamation-triangle"></i></div>
        </div>
    <?php else : ?>
        <div class="stat-card success span-4">
            <div class="stat-content">
                <div class="stat-label">Ventas del Día (Caja)</div>
                <div class="stat-value">$ <?= number_format($data['ventas_hoy'] ?? 0, 2) ?></div>
            </div>
            <div class="stat-icon"><i class="fas fa-cash-register"></i></div>
        </div>
    <?php endif; ?>

    <!-- 2. Alertas Críticas (Full Width if any) -->
    <?php if (!empty($data['lotes_por_vencer'])) : ?>
    <div class="bento-widget span-4" style="background: #FEE2E2; border-color: #EF4444;">
        <div class="d-flex align-items-center text-danger">
            <i class="fas fa-exclamation-circle fa-2x me-3"></i>
            <div>
                <h5 class="alert-heading fw-bold">¡Atención! Insumos/Productos por vencer</h5>
                <p class="mb-0">
                    Hay
                    <strong><?= count($data['lotes_por_vencer']) ?></strong>
                    lote(s) que vencen en los próximos 30 días.
                </p>
            </div>
             <a href="/reportes/vencimientos" class="btn btn-sm btn-danger ms-auto">Ver Detalle</a>
        </div>
    </div>
    <?php endif; ?>

    <!-- 3. Gráfico Principal (Span 3) -->
    <div class="bento-widget span-3 widget-chart">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="card-title mb-0">
                <i class="fas fa-chart-area"></i> Tendencia de Ventas
            </h3>
            <div class="btn-group btn-group-sm">
                <button
                    type="button"
                    class="btn btn-outline-secondary active"
                    onclick="updateChart(7, this)">
                    7 D
                </button>
                <button type="button" class="btn btn-outline-secondary" onclick="updateChart(15, this)">15 D</button>
                <button type="button" class="btn btn-outline-secondary" onclick="updateChart(30, this)">30 D</button>
            </div>
        </div>
        <div class="chart-container">
            <canvas id="ventasChart"></canvas>
        </div>
    </div>

    <!-- 4. Top Productos (Span 1) -->
    <div class="bento-widget span-1">
        <div class="mb-3">
            <h3 class="card-title mx-0">
                <i class="fas fa-crown"></i> Top Productos
            </h3>
        </div>
        <div class="chart-container" style="min-height: 250px;">
            <canvas id="productosChart"></canvas>
        </div>
    </div>

    <!-- 5. Listas de Stock (Span 2 each) -->
    <div class="bento-widget span-2">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="card-title m-0"><i class="fas fa-box"></i> Stock Bajo: Productos</h3>
            <a href="/productos" class="btn btn-sm btn-secondary">Ver Todos</a>
        </div>
        
        <?php if (empty($data['productos_stock_bajo'])) : ?>
            <div class="text-center text-muted py-4">
                <i class="fas fa-check-circle fa-2x mb-2 text-success"></i>
                <p>Todo en orden</p>
            </div>
        <?php else : ?>
            <div class="bento-list">
                <?php foreach (array_slice($data['productos_stock_bajo'], 0, 5) as $producto) : ?>
                <div class="bento-list-item">
                    <div>
                        <strong><?= htmlspecialchars($producto['nombre']) ?></strong>
                        <div class="small text-muted">Mín: <?= $producto['stock_minimo'] ?></div>
                    </div>
                    <span class="badge badge-danger"><?= $producto['stock_actual'] ?></span>
                    <a
                        href="/productos/edit/<?= $producto['id'] ?>"
                        class="text-warning">
                        <i class="fas fa-edit"></i>
                    </a>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="bento-widget span-2">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="card-title m-0"><i class="fas fa-cubes"></i> Stock Bajo: Insumos</h3>
            <a href="/insumos" class="btn btn-sm btn-secondary">Ver Todos</a>
        </div>
        
        <?php if (empty($data['insumos_stock_bajo'])) : ?>
            <div class="text-center text-muted py-4">
                <i class="fas fa-check-circle fa-2x mb-2 text-success"></i>
                <p>Todo en orden</p>
            </div>
        <?php else : ?>
            <div class="bento-list">
                <?php foreach (array_slice($data['insumos_stock_bajo'], 0, 5) as $insumo) : ?>
                <div class="bento-list-item">
                    <div>
                        <strong><?= htmlspecialchars($insumo['nombre']) ?></strong>
                        <div class="small text-muted">Mín: <?= $insumo['stock_minimo'] ?></div>
                    </div>
                    <span class="badge badge-danger"><?= $insumo['stock_actual'] ?></span>
                    <a href="/insumos/edit/<?= $insumo['id'] ?>" class="text-warning"><i class="fas fa-edit"></i></a>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- 6. Accesos Rápidos (Span 4 / Full Row) -->
    <div class="bento-widget span-4">
        <h3 class="card-title mb-4"><i class="fas fa-bolt"></i> Accesos Rápidos</h3>
        <div class="actions-grid">
            <a href="/ventas/create" class="action-btn">
                <i class="fas fa-cash-register"></i>
                <span>Nueva Venta</span>
            </a>
            <a href="/pedidos/create" class="action-btn">
                <i class="fas fa-shopping-cart"></i>
                <span>Nuevo Pedido</span>
            </a>
            <a href="/producciones/create" class="action-btn">
                <i class="fas fa-industry"></i>
                <span>Producción</span>
            </a>
            <a href="/clientes/create" class="action-btn">
                <i class="fas fa-user-plus"></i>
                <span>Cliente</span>
            </a>
            <a href="/sugerencias" class="action-btn">
                <i class="fas fa-lightbulb"></i>
                <span>Sugerencias</span>
            </a>
            <a href="/reportes" class="action-btn">
                <i class="fas fa-file-alt"></i>
                <span>Reportes</span>
            </a>
            <!-- Botón extra para completar grilla visualmente -->
            <a href="/usuarios/perfil" class="action-btn">
                <i class="fas fa-user-circle"></i>
                <span>Mi Perfil</span>
            </a>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
let ventasChart; 

document.addEventListener('DOMContentLoaded', function() {
    Chart.defaults.font.family = "'Outfit', sans-serif";
    Chart.defaults.color = '#78716C';
    Chart.defaults.maintainAspectRatio = false;

    // 1. Gráfico de Ventas
    const ctxVentas = document.getElementById('ventasChart').getContext('2d');
    const ventasData = <?= json_encode($data['ventas_ultimos_dias']) ?>;
    
    // Gradiente para el gráfico
    const gradient = ctxVentas.createLinearGradient(0, 0, 0, 400);
    gradient.addColorStop(0, 'rgba(210, 105, 30, 0.2)');
    gradient.addColorStop(1, 'rgba(210, 105, 30, 0)');

    ventasChart = new Chart(ctxVentas, {
        type: 'line',
        data: {
            labels: ventasData.map(v => v.fecha),
            datasets: [{
                label: 'Ventas ($)',
                data: ventasData.map(v => v.total),
                borderColor: '#D2691E',
                backgroundColor: gradient,
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#fff',
                pointBorderColor: '#D2691E',
                pointRadius: 4,
                pointHoverRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: { 
                    backgroundColor: '#3E2723',
                    titleColor: '#FFF',
                    bodyColor: '#FFF',
                    padding: 10,
                    cornerRadius: 8,
                    callbacks: { label: (c) => '$ ' + c.parsed.y.toFixed(2) } 
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { borderDash: [4, 4], color: 'rgba(0,0,0,0.05)' },
                    ticks: { callback: (v) => '$ ' + v }
                },
                x: {
                    grid: { display: false }
                }
            }
        }
    });

    // 2. Gráfico Productos
    const ctxProd = document.getElementById('productosChart').getContext('2d');
    const prodData = <?= json_encode($data['productos_mas_vendidos']) ?>;
    
    new Chart(ctxProd, {
        type: 'doughnut',
        data: {
            labels: prodData.map(p => p.nombre),
            datasets: [{
                data: prodData.map(p => p.cantidad),
                backgroundColor: ['#D2691E', '#CD853F', '#DEB887', '#A0522D', '#8B4513'],
                borderWidth: 0,
                hoverOffset: 10
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '70%',
            plugins: {
                legend: { position: 'bottom', labels: { usePointStyle: true, padding: 20 } }
            },
            layout: { padding: 10 }
        }
    });
});

function updateChart(dias, btn) {
    document.querySelectorAll('.btn-group .btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');

    fetch(`/dashboard/ventas-chart?dias=${dias}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                ventasChart.data.labels = data.data.map(v => v.fecha);
                ventasChart.data.datasets[0].data = data.data.map(v => v.total);
                ventasChart.update();
            }
        })
        .catch(error => console.error('Error al actualizar gráfico:', error));
}

// Alertas Stock
document.addEventListener('DOMContentLoaded', () => {
    <?php if (!empty($data['productos_stock_bajo']) || !empty($data['insumos_stock_bajo'])) : ?>
    const totalBajo = <?= count($data['productos_stock_bajo'] ?? []) + count($data['insumos_stock_bajo'] ?? []) ?>;
    Swal.fire({
        icon: 'warning',
        title: 'Stock Bajo Detectado',
        text: `Hay ${totalBajo} ítems que requieren atención.`,
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 5000,
        timerProgressBar: true
    });
    <?php endif; ?>
});

// Reloj en tiempo real
function actualizarReloj() {
    const ahora = new Date();
    const horas = String(ahora.getHours()).padStart(2, '0');
    const minutos = String(ahora.getMinutes()).padStart(2, '0');
    const segundos = String(ahora.getSeconds()).padStart(2, '0');
    document.getElementById('reloj').textContent = `${horas}:${minutos}:${segundos}`;
}
setInterval(actualizarReloj, 1000);
actualizarReloj();
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

