<?php
$page_title = 'Historial de Entregas - SIPAN Delivery';
$moneda     = htmlspecialchars($_ENV['moneda_principal'] ?? 'S/');

// Variables para el filtro preseleccionadas o valores por defecto
$f_desde    = $_GET['desde'] ?? date('Y-m-01');
$f_hasta    = $_GET['hasta'] ?? date('Y-m-d');

ob_start();
?>

<div class="page-header animate-fade-in">
    <h2 class="page-header__title">Mis Entregas</h2>
    <p class="page-header__subtitle">Registro histórico de repartos</p>
</div>

<!-- Filtros de Fecha -->
<div class="filter-card animate-fade-in" style="background:var(--bg-secondary); padding:15px; border-radius:12px; margin-bottom:20px; border:1px solid var(--border-color);">
    <form method="GET" action="/delivery/historial" style="display:flex; flex-direction:column; gap:10px;">
        <div style="display:flex; gap:10px;">
            <div style="flex:1;">
                <label style="font-size:12px; color:var(--text-secondary); margin-bottom:5px; display:block;">Desde</label>
                <input type="date" name="desde" value="<?= htmlspecialchars($f_desde) ?>" class="form-control" style="font-size:14px; padding:8px;" required>
            </div>
            <div style="flex:1;">
                <label style="font-size:12px; color:var(--text-secondary); margin-bottom:5px; display:block;">Hasta</label>
                <input type="date" name="hasta" value="<?= htmlspecialchars($f_hasta) ?>" class="form-control" style="font-size:14px; padding:8px;" required>
            </div>
        </div>
        <button type="submit" class="btn btn-outline" style="width:100%; padding:8px; display:flex; justify-content:center; align-items:center; gap:8px;">
            <i class="fa-solid fa-filter"></i> Filtrar Historial
        </button>
    </form>
</div>

<!-- Resumen del Período -->
<div class="summary-card animate-fade-in" style="animation-delay:0.05s; display:flex; background:var(--bg-secondary); border-radius:12px; margin-bottom:20px; border:1px solid var(--border-color);">
    <div style="flex:1; padding:15px; text-align:center; border-right:1px solid var(--border-color);">
        <div style="font-size:20px; font-weight:800; color:var(--primary-color);"><?= (int)($resumen['total_entregas'] ?? 0) ?></div>
        <div style="font-size:11px; text-transform:uppercase; color:var(--text-secondary);">Entregas</div>
    </div>
    <div style="flex:1; padding:15px; text-align:center;">
        <div style="font-size:20px; font-weight:800; color:var(--status-entregado);"><?= $moneda ?><?= number_format($resumen['total_cobrado'] ?? 0, 2) ?></div>
        <div style="font-size:11px; text-transform:uppercase; color:var(--text-secondary);">Cobrado</div>
    </div>
</div>

<div id="listaHistorial">
    <?php if (empty($pedidos)): ?>

        <div class="empty-state animate-fade-in" style="animation-delay:0.1s">
            <div class="empty-state__icon empty-state__icon--success">
                <i class="fa-solid fa-clipboard-check"></i>
            </div>
            <h3 class="empty-state__title">Sin historial</h3>
            <p class="empty-state__desc">No hay entregas para las fechas seleccionadas.</p>
        </div>

    <?php else: ?>

        <?php
        $fecha_actual = '';
        $i = 0;
        foreach ($pedidos as $p):
            $fecha_pedido = date('Y-m-d', strtotime($p['fecha_pedido']));

            if ($fecha_actual !== $fecha_pedido) {
                $fecha_actual  = $fecha_pedido;
                $es_hoy        = $fecha_actual === date('Y-m-d');
                $texto_fecha   = $es_hoy ? 'Hoy' : date('d M, Y', strtotime($fecha_actual));
                echo "<div class='historial-group-label animate-fade-in'>
                        <span>{$texto_fecha}</span>
                        <div class='historial-group-label__line'></div>
                      </div>";
            }
            $i++;
        ?>

        <a href="/delivery/pedido/<?= (int)$p['id'] ?>"
           class="pedido-card status-entregado pedido-card--row animate-fade-in"
           style="animation-delay:<?= $i * 0.05 ?>s">

            <div class="historial-card__left">
                <div class="historial-card__number"><?= htmlspecialchars($p['numero_pedido']) ?></div>
                <div class="historial-card__name">
                    <?= htmlspecialchars($p['cliente_nombre'] . ' ' . $p['cliente_apellido']) ?>
                </div>
                <div class="historial-card__time">
                    <i class="fa-regular fa-clock"></i>
                    <?= date('h:i A', strtotime($p['fecha_entrega'] ?? $p['fecha_pedido'])) ?>
                </div>
            </div>

            <div class="historial-card__right">
                <div class="historial-card__total"><?= $moneda ?><?= number_format($p['total'], 2) ?></div>
                <span class="badge badge-entregado">Exitosa</span>
            </div>

        </a>

        <?php endforeach; ?>

    <?php endif; ?>
</div>

<?php
$content_html = ob_get_clean();
require_once __DIR__ . '/layout.php';
?>
