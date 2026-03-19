<?php
$page_title = 'Mis Estadísticas - SIPAN Delivery';
$moneda = htmlspecialchars($_ENV['moneda_principal'] ?? 'S/');

ob_start();
?>

<div class="page-header animate-fade-in">
    <h2 class="page-header__title">Mis Estadísticas</h2>
    <p class="page-header__subtitle">Tu rendimiento y eficiencia en tiempo real.</p>
</div>

<!-- Tarjetas de KPIs Principales -->
<div class="stats-grid animate-fade-in" style="animation-delay:0.1s">
    <div class="stat-card stat-card--primary">
        <div class="stat-card__icon"><i class="fa-solid fa-box-open"></i></div>
        <div class="stat-card__content">
            <div class="stat-card__value"><?= (int)($stats['hoy']['entregas'] ?? 0) ?></div>
            <div class="stat-card__label">Entregas Hoy</div>
        </div>
    </div>
    <div class="stat-card stat-card--success">
        <div class="stat-card__icon"><i class="fa-solid fa-hand-holding-dollar"></i></div>
        <div class="stat-card__content">
            <div class="stat-card__value"><?= $moneda ?><?= number_format($stats['hoy']['cobrado'] ?? 0, 2) ?></div>
            <div class="stat-card__label">Cobrado Hoy</div>
        </div>
    </div>
</div>

<div class="stats-grid animate-fade-in" style="animation-delay:0.15s; margin-top:15px">
    <div class="stat-card">
        <div class="stat-card__icon"><i class="fa-solid fa-calendar-week"></i></div>
        <div class="stat-card__content">
            <div class="stat-card__value"><?= (int)($stats['semana']['entregas'] ?? 0) ?></div>
            <div class="stat-card__label">Esta Semana</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-card__icon"><i class="fa-solid fa-percent"></i></div>
        <div class="stat-card__content">
            <div class="stat-card__value"><?= (int)$stats['ratio'] ?>%</div>
            <div class="stat-card__label">Ratio de Éxito</div>
        </div>
    </div>
</div>

<!-- Resumen Mensual -->
<div class="detail-card animate-fade-in" style="animation-delay:0.2s; margin-top:20px;">
    <div class="section-title">
        <i class="fa-solid fa-chart-pie" style="color:var(--primary-color);"></i> Resumen del Mes
    </div>
    
    <div class="summary-row">
        <span>Total Entregas</span>
        <span style="font-weight:700;"><?= (int)($stats['mes']['entregas'] ?? 0) ?></span>
    </div>
    <div class="summary-row">
        <span>Monto Repartido</span>
        <span><?= $moneda ?><?= number_format($stats['mes']['monto'] ?? 0, 2) ?></span>
    </div>
    <div class="summary-row" style="border-bottom:none; margin-bottom:0; padding-bottom:0;">
        <span>Cobros Registrados</span>
        <span style="color:var(--status-entregado); font-weight:700;"><?= $moneda ?><?= number_format($stats['mes']['cobrado'] ?? 0, 2) ?></span>
    </div>
</div>

<!-- Actividad Reciente (Gráfico Simplificado) -->
<div class="detail-card animate-fade-in" style="animation-delay:0.25s; margin-top:20px; margin-bottom:40px;">
    <div class="section-title">
        <i class="fa-solid fa-chart-line" style="color:var(--primary-color);"></i> Últimos 7 Días
    </div>
    
    <div class="mini-chart">
        <?php if(empty($stats['chart'])): ?>
            <p style="text-align:center; color:var(--text-secondary); margin:20px 0;">No hay datos recientes.</p>
        <?php else: 
            $max_entregas = max(array_column($stats['chart'], 'entregas')) ?: 1;
        ?>
            <div class="chart-bars" style="display:flex; align-items:flex-end; gap:8px; height:120px; margin-top:15px;">
                <?php foreach($stats['chart'] as $day): 
                    $height = ($day['entregas'] / $max_entregas) * 100;
                ?>
                <div class="chart-bar-group" style="flex:1; display:flex; flex-direction:column; align-items:center;">
                    <span style="font-size:11px; font-weight:600; margin-bottom:5px; color:var(--primary-color);"><?= $day['entregas'] ?></span>
                    <div class="bar" style="width:100%; height:<?= $height ?>%; background:var(--primary-light); border-radius:4px 4px 0 0; min-height:4px;"></div>
                    <span style="font-size:10px; margin-top:5px; color:var(--text-secondary); text-transform:uppercase;"><?= date('D', strtotime($day['dia'])) ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
.stats-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
}
.stat-card {
    background: var(--bg-secondary);
    border-radius: 16px;
    padding: 20px 15px;
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    box-shadow: 0 4px 12px rgba(0,0,0,0.03);
    border: 1px solid var(--border-color);
}
.stat-card--primary {
    background: var(--primary-color);
    color: white;
    border-color: transparent;
}
.stat-card--success {
    background: var(--status-entregado);
    color: white;
    border-color: transparent;
}
.stat-card__icon {
    font-size: 24px;
    margin-bottom: 10px;
    opacity: 0.9;
}
.stat-card__value {
    font-size: 22px;
    font-weight: 800;
    margin-bottom: 4px;
    line-height: 1.1;
}
.stat-card__label {
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    opacity: 0.8;
}
.stat-card:not(.stat-card--primary):not(.stat-card--success) .stat-card__icon {
    color: var(--primary-color);
}
.stat-card:not(.stat-card--primary):not(.stat-card--success) .stat-card__value {
    color: var(--text-primary);
}
</style>

<?php
$content_html = ob_get_clean();
require_once __DIR__ . '/layout.php';
?>
