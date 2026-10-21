<?php

use App\SIPAN;

$pageTitle = 'Dashboard';
$currentPage = 'dashboard';

require_once __DIR__ . '/../layouts/header.php';

$user_rol ??= '';
/** @var array $data */
$data ??= [];

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
        </div>    <?php else : ?>
        <div class="stat-card info span-2">
            <div class="stat-content">
                <div class="stat-label">Stock Bajo: Productos</div>
                <div class="stat-value"><?= count($data['productos_stock_bajo'] ?? []) ?></div>
            </div>
            <div class="stat-icon"><i class="fas fa-box-open"></i></div>
        </div>

        <div class="stat-card warning span-2">
            <div class="stat-content">
                <div class="stat-label">Lotes por Vencer (30 Días)</div>
                <div class="stat-value"><?= count($data['lotes_por_vencer'] ?? []) ?></div>
            </div>
            <div class="stat-icon"><i class="fas fa-calendar-times"></i></div>
        </div>
    <?php endif; ?>

    <!-- 2. Alertas Críticas (Full Width if any) -->
    <?php if (!empty($data['lotes_por_vencer'])) : 
        $prods_venc = array_filter($data['lotes_por_vencer'], fn($l) => ($l['tipo'] ?? '') === 'producto');
        $ins_venc = array_filter($data['lotes_por_vencer'], fn($l) => ($l['tipo'] ?? '') === 'insumo');
    ?>
    <div class="bento-widget span-4" style="background: #FFFBEB; border: 1px solid #F59E0B; border-left: 5px solid #D97706;">
        <div class="d-flex align-items-center">
            <i class="fas fa-exclamation-triangle fa-2x text-warning me-3"></i>
            <div class="flex-grow-1">
                <h5 class="alert-heading fw-bold mb-1 text-dark">
                    Estimado de Vencimiento y Tiempo Máximo de Venta
                </h5>
                <p class="mb-0 small text-muted">
                    <?php if (!empty($prods_venc)) : ?>
                        <span class="badge bg-primary me-1"><i class="fas fa-bread-slice me-1"></i> <?= count($prods_venc) ?> producto(s)</span> límite de venta
                    <?php endif; ?>
                    <?php if (!empty($ins_venc)) : ?>
                        <span class="badge bg-warning text-dark me-1"><i class="fas fa-boxes me-1"></i> <?= count($ins_venc) ?> insumo(s)</span> próximos a caducar
                    <?php endif; ?>
                    (en los próximos 30 días).
                </p>
            </div>
            <a href="/reportes/vencimientos" class="btn btn-sm btn-outline-warning text-dark fw-bold ms-auto">
                <i class="fas fa-calendar-times me-1"></i> Ver Reporte de Vencimientos
            </a>
        </div>
    </div>
    <?php endif; ?>

    <?php if ($user_rol !== 'cajero') : ?>
    <!-- 3. Gráfico Principal (Span 3) -->
    <div class="bento-widget span-3 widget-chart">
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <h3 class="card-title mb-0">
                <i class="fas fa-chart-area"></i> Tendencia de Ventas
            </h3>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <div class="btn-group btn-group-sm">
                    <button type="button" class="btn btn-outline-secondary active" onclick="updateChart(7, this)">7 D</button>
                    <button type="button" class="btn btn-outline-secondary" onclick="updateChart(15, this)">15 D</button>
                    <button type="button" class="btn btn-outline-secondary" onclick="updateChart(30, this)">30 D</button>
                </div>
                <div class="d-flex align-items-center gap-1">
                    <input type="date" id="chartFechaInicio" class="form-control form-control-sm" style="width:140px" value="<?= date('Y-m-01') ?>">
                    <span class="text-muted small">al</span>
                    <input type="date" id="chartFechaFin" class="form-control form-control-sm" style="width:140px" value="<?= date('Y-m-d') ?>">
                    <button type="button" class="btn btn-sm btn-primary" onclick="updateChartRango()">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="chart-container">
            <canvas id="ventasChart"></canvas>
        </div>
    </div>
    <?php endif; ?>

    <?php if ($user_rol !== 'cajero') : ?>
    <!-- 4. Top Productos (Span 1) -->
    <div class="bento-widget span-1">
        <div class="mb-3">
            <h3 class="card-title mx-0">
                <i class="fas fa-crown"></i> Top Productos
            </h3>
        </div>
        <?php if (empty($data['productos_mas_vendidos'])) : ?>
            <div class="text-center text-muted py-4 d-flex flex-column align-items-center justify-content-center" style="min-height: 200px;">
                <i class="fas fa-chart-pie fa-3x mb-2" style="opacity: 0.25; color: #D4A574;"></i>
                <p class="small mb-0">Sin ventas en los últimos 30 días</p>
                <small class="text-muted mt-1" style="font-size: 0.75rem;">Los productos más vendidos aparecerán aquí.</small>
            </div>
        <?php else : ?>
            <div class="chart-container" style="min-height: 250px;">
                <canvas id="productosChart"></canvas>
            </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- 5. Listas de Stock -->
    <div class="bento-widget <?= $user_rol === 'cajero' ? 'span-4' : 'span-2' ?>">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="card-title m-0"><i class="fas fa-box"></i> Stock de Productos</h3>
            <a href="/productos" class="btn btn-sm btn-secondary"><?= $user_rol === 'cajero' ? 'Ver Catálogo' : 'Ver Todos' ?></a>
        </div>
        
        <?php if (empty($data['productos_stock_bajo'])) : ?>
            <div class="text-center text-muted py-4">
                <i class="fas fa-check-circle fa-2x mb-2 text-success"></i>
                <p>Nivel de stock óptimo</p>
            </div>
        <?php else : ?>
            <div class="bento-list">
                <?php foreach (array_slice($data['productos_stock_bajo'] ?? [], 0, 5) as $producto) : ?>
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

    <?php if ($user_rol !== 'cajero') : ?>
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
                <?php foreach (array_slice($data['insumos_stock_bajo'] ?? [], 0, 5) as $insumo) : ?>
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
    <?php endif; ?>

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

    <?php if ($user_rol !== 'cajero') : ?>
    // 1. Gráfico de Ventas
    const ctxVentas = document.getElementById('ventasChart').getContext('2d');
    const ventasData = <?= json_encode($data['ventas_ultimos_dias'] ?? []) ?>;
    
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

    // 2. Gráfico Productos (si hay datos y elemento)
    const canvasProd = document.getElementById('productosChart');
    const prodData = <?= json_encode($data['productos_mas_vendidos'] ?? []) ?>;
    
    if (canvasProd && prodData && prodData.length > 0) {
        new Chart(canvasProd.getContext('2d'), {
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
    }
    <?php endif; ?>
});

function updateChart(dias, btn) {
    document.querySelectorAll('.btn-group .btn').forEach(b => b.classList.remove('active'));
    if (btn) btn.classList.add('active');

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

function updateChartRango() {
    const fi = document.getElementById('chartFechaInicio').value;
    const ff = document.getElementById('chartFechaFin').value;
    if (!fi || !ff || fi > ff) {
        Swal.fire({ icon: 'warning', title: 'Fechas inválidas', toast: true, position: 'top-end', showConfirmButton: false, timer: 2500 });
        return;
    }
    // Quitar 'active' de los botones rápidos
    document.querySelectorAll('.btn-group .btn').forEach(b => b.classList.remove('active'));

    fetch(`/dashboard/ventas-chart?fecha_inicio=${fi}&fecha_fin=${ff}`)
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                ventasChart.data.labels = data.data.map(v => v.fecha);
                ventasChart.data.datasets[0].data = data.data.map(v => v.total);
                ventasChart.update();
            }
        })
        .catch(e => console.error('Error:', e));
}

// Alertas Stock y Vencimientos
document.addEventListener('DOMContentLoaded', () => {
    <?php if (!empty($data['lotes_por_vencer'])) : ?>
    const totalVencer = <?= count($data['lotes_por_vencer'] ?? []) ?>;
    Swal.fire({
        icon: 'warning',
        title: '¡Alerta de Vencimiento de Productos/Insumos!',
        html: `Se detectaron <strong>${totalVencer}</strong> lote(s) próximos a su fecha de caducidad o tiempo máximo de venta estimado.`,
        confirmButtonText: '<i class="fas fa-calendar-times me-1"></i> Ver Lotes por Vencer',
        showCancelButton: true,
        cancelButtonText: 'Entendido',
        confirmButtonColor: '#e02424',
        cancelButtonColor: '#6b7280'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = '/reportes/vencimientos';
        }
    });
    <?php elseif (!empty($data['productos_stock_bajo']) || !empty($data['insumos_stock_bajo'])) : ?>
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

<?php require_once __DIR__ . '/../layouts/footer.php';
