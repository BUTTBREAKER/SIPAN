<?php
$page_title = 'Pedidos - SIPAN Delivery';
ob_start();
?>

<!-- Filtros/Tabs Scrollable -->
<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px; padding:0 5px;">
    <div style="font-size:12px; color:var(--text-secondary);">
        <i class="fa-solid fa-rotate"></i> Actualizado: <span id="lastUpdateTime"><?= date('H:i:s') ?></span>
    </div>
    <div id="refreshIndicator" style="width:8px; height:8px; border-radius:50%; background:var(--status-entregado); opacity:1; transition:opacity 0.5s;"></div>
</div>

<div class="tab-scroller">
    <div class="tab-pill active" onclick="filterPedidos('todos', this)">
        Todos <span class="count" id="count-todos"><?= $total_pedidos ?></span>
    </div>
    <div class="tab-pill" onclick="filterPedidos('pendientes', this)">
        Pendientes <span class="count" id="count-pendientes"><?= $total_pendientes ?></span>
    </div>
    <div class="tab-pill" onclick="filterPedidos('en_camino', this)">
        En Camino <span class="count" id="count-camino"><?= $total_en_camino ?></span>
    </div>
</div>

<!-- Lista de Pedidos -->
<div id="listaPedidos">
    <?php if (empty($pedidos)): ?>

        <div class="empty-state animate-fade-in">
            <div class="empty-state__icon empty-state__icon--primary">
                <i class="fa-solid fa-box-open"></i>
            </div>
            <h3 class="empty-state__title">Sin Pedidos</h3>
            <p class="empty-state__desc">No tienes pedidos asignados actualmente.</p>
            <div class="empty-state__action">
                <button class="btn btn-outline" onclick="location.reload()">
                    <i class="fa-solid fa-rotate-right"></i> Actualizar
                </button>
            </div>
        </div>

    <?php else: ?>

        <?php foreach ($pedidos as $i => $p):
            $status_class    = '';
            $badge_class     = '';
            $status_label    = '';
            $filter_category = '';

            switch ($p['estado_pedido']) {
                case 'pendiente':
                case 'en_proceso':
                    $status_class    = 'status-pendiente';
                    $badge_class     = 'badge-pendiente';
                    $status_label    = 'Pendiente';
                    $filter_category = 'pendientes';
                    break;
                case 'en_camino':
                    $status_class    = 'status-camino';
                    $badge_class     = 'badge-camino';
                    $status_label    = 'En Camino';
                    $filter_category = 'en_camino';
                    break;
                case 'entregado':
                case 'completado':
                    $status_class    = 'status-entregado';
                    $badge_class     = 'badge-entregado';
                    $status_label    = 'Entregado';
                    $filter_category = 'entregados';
                    break;
            }
        ?>

        <a href="/delivery/pedido/<?= (int)$p['id'] ?>"
           class="pedido-card <?= $status_class ?> animate-fade-in"
           data-category="<?= $filter_category ?>"
           style="animation-delay:<?= $i * 0.08 ?>s">

            <div class="pedido-card__header">
                <div class="pedido-id"><?= htmlspecialchars($p['numero_pedido']) ?></div>
                <span class="badge <?= $badge_class ?>"><?= $status_label ?></span>
            </div>

            <div class="pedido-cliente">
                <?= htmlspecialchars($p['cliente_nombre'] . ' ' . $p['cliente_apellido']) ?>
            </div>

            <?php if (!empty($p['cliente_direccion'])): ?>
            <div class="pedido-address">
                <i class="fa-solid fa-location-dot"></i>
                <span><?= htmlspecialchars($p['cliente_direccion']) ?></span>
            </div>
            <?php endif; ?>

            <div class="pedido-meta">
                <div class="pedido-time">
                    <i class="fa-regular fa-clock"></i>
                    <?= date('h:i A', strtotime($p['fecha_pedido'])) ?>
                </div>
                <div class="pedido-total">
                    <?= htmlspecialchars($_ENV['moneda_principal'] ?? 'S/') ?><?= number_format($p['total'], 2) ?>
                </div>
            </div>

        </a>

        <?php endforeach; ?>

    <?php endif; ?>
</div>

<script>
function filterPedidos(category, btnElement) {
    document.querySelectorAll('.tab-pill').forEach(btn => btn.classList.remove('active'));
    btnElement.classList.add('active');

    document.querySelectorAll('.pedido-card').forEach(card => {
        if (category === 'todos') {
            card.style.display = 'block';
        } else {
            const match = card.dataset.category === category ||
                (category === 'pendientes' && card.dataset.category === 'en_proceso');
            card.style.display = match ? 'block' : 'none';
        }
    });
}
</script>

<?php
$content_html = ob_get_clean();
require_once __DIR__ . '/layout.php';
?>
