<?php
$page_title = 'Detalle de Pedido - SIPAN Delivery';
$moneda     = htmlspecialchars($_ENV['moneda_principal'] ?? 'S/');
$estado     = $pedido['estado_pedido'];

// Configuración de estado
$status_configs = [
    'en_camino'  => ['color' => 'var(--status-camino)',     'label' => 'EN CAMINO',            'icon' => 'fa-motorcycle'],
    'entregado'  => ['color' => 'var(--status-entregado)',  'label' => 'ENTREGADO',             'icon' => 'fa-circle-check'],
    'completado' => ['color' => 'var(--status-entregado)',  'label' => 'ENTREGADO',             'icon' => 'fa-circle-check'],
    'cancelado'  => ['color' => 'var(--status-cancelado)',  'label' => 'CANCELADO',             'icon' => 'fa-circle-xmark'],
    'pendiente'  => ['color' => 'var(--status-pendiente)',  'label' => 'PENDIENTE DE DESPACHO', 'icon' => 'fa-clock'],
    'en_proceso' => ['color' => 'var(--status-pendiente)',  'label' => 'PENDIENTE DE DESPACHO', 'icon' => 'fa-clock'],
];

$sc = $status_configs[$estado] ?? $status_configs['pendiente'];

// Header personalizado con botón de retroceso (reemplaza el header del layout)
$custom_header_html = '<header class="detail-header">
    <a href="/delivery/dashboard" class="detail-header__back" aria-label="Volver al listado">
        <i class="fa-solid fa-chevron-left"></i>
    </a>
    <div class="detail-header__title">' . htmlspecialchars($pedido['numero_pedido']) . '</div>
    <div class="detail-header__spacer"></div>
</header>';

ob_start();
?>

<!-- Status Banner -->
<div class="status-banner animate-fade-in" style="background:<?= $sc['color'] ?>; box-shadow: 0 10px 20px -5px <?= str_replace(')', ', 0.28)', str_replace('var(', 'hsla(', $sc['color'])) ?>">
    <i class="fa-solid <?= $sc['icon'] ?>"></i> <?= $sc['label'] ?>
</div>

<!-- Datos de Entrega -->
<div class="detail-card animate-fade-in" style="animation-delay:0.08s">
    <div class="section-title">Datos de Entrega</div>

    <div class="client-header">
        <div class="client-avatar">
            <i class="fa-regular fa-user"></i>
        </div>
        <div class="client-name">
            <?= htmlspecialchars($cliente['nombre'] . ' ' . $cliente['apellido']) ?>
        </div>
    </div>

    <?php if (!empty($cliente['telefono'])): ?>
    <div class="contact-grid">
        <a href="tel:<?= htmlspecialchars($cliente['telefono']) ?>" class="contact-btn contact-btn--call">
            <i class="fa-solid fa-phone"></i> Llamar
        </a>
        <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $cliente['telefono']) ?>"
           target="_blank" rel="noopener"
           class="contact-btn contact-btn--whatsapp">
            <i class="fa-brands fa-whatsapp"></i> WhatsApp
        </a>
    </div>
    <?php endif; ?>

    <?php if (!empty($cliente['direccion'])): ?>
    <div class="address-block">
        <i class="fa-solid fa-location-dot address-block__icon"></i>
        <div><?= htmlspecialchars($cliente['direccion']) ?></div>
    </div>
    <a href="https://www.google.com/maps/search/?api=1&query=<?= urlencode($cliente['direccion']) ?>"
       target="_blank" rel="noopener"
       class="btn btn-maps">
        <i class="fa-solid fa-map-location-dot"></i> Abrir en Google Maps
    </a>
    <?php endif; ?>
</div>

<!-- Notas del Pedido -->
<?php if (!empty($pedido['observaciones'])): ?>
<div class="detail-card notes-card animate-fade-in" style="animation-delay:0.16s">
    <div class="section-title">Notas del Pedido</div>
    <div class="notes-content">"<?= htmlspecialchars($pedido['observaciones']) ?>"</div>
</div>
<?php endif; ?>

<!-- Resumen de Cuenta -->
<div class="detail-card animate-fade-in" style="animation-delay:0.24s">
    <div class="section-title">Resumen de Cuenta</div>

    <div class="product-list">
        <?php foreach ($productos as $prod): ?>
        <div class="product-row">
            <span class="product-qty"><?= (int)$prod['cantidad'] ?></span>
            <span class="product-name"><?= htmlspecialchars($prod['producto_nombre']) ?></span>
            <span class="product-subtotal"><?= $moneda ?><?= number_format($prod['subtotal'], 2) ?></span>
        </div>
        <?php endforeach; ?>
    </div>

    <div class="summary-row">
        <span>Subtotal Bruto</span>
        <span><?= $moneda ?><?= number_format($pedido['subtotal'], 2) ?></span>
    </div>

    <?php if ($pedido['descuento'] > 0): ?>
    <div class="summary-row summary-row--discount">
        <span>Descuento Aplicado</span>
        <span>-<?= $moneda ?><?= number_format($pedido['descuento'], 2) ?></span>
    </div>
    <?php endif; ?>

    <div class="summary-total">
        <span class="summary-total__label">Total a Pagar</span>
        <span class="summary-total__amount"><?= $moneda ?><?= number_format($pedido['total'], 2) ?></span>
    </div>

    <div class="pago-indicator <?= $pedido['estado_pago'] === 'pagado' ? 'pago-indicator--pagado' : 'pago-indicator--deuda' ?>">
        <i class="fa-solid <?= $pedido['estado_pago'] === 'pagado' ? 'fa-circle-check' : 'fa-triangle-exclamation' ?>"></i>
        <?= $pedido['estado_pago'] === 'pagado'
            ? 'PAGO CONFIRMADO'
            : 'COBRAR AL CLIENTE: ' . $moneda . number_format($pedido['monto_deuda'] ?? 0, 2) ?>
    </div>
</div>

<!-- Acciones del Repartidor -->
<?php if ($estado !== 'entregado' && $estado !== 'completado' && $estado !== 'cancelado'): ?>
<div class="detail-card animate-fade-in" style="animation-delay:0.32s; border:2px solid var(--primary-light); margin-bottom:40px">
    <div class="section-title">Acciones de Entrega</div>

    <input type="hidden" id="pedido_id" value="<?= (int)$pedido['id'] ?>">

    <!-- Formulario de Cobro (Si hay deuda) -->
    <?php if ($pedido['monto_deuda'] > 0 && ($estado === 'en_camino' || $estado === 'en_proceso' || $estado === 'pendiente')): ?>
    <div class="payment-collection-form" style="background:var(--bg-secondary); padding:15px; border-radius:12px; margin-bottom:20px; border:1px solid var(--border-color);">
        <h4 style="margin:0 0 10px 0; font-size:15px; color:var(--text-secondary);">
            <i class="fa-solid fa-hand-holding-dollar"></i> Cobrar al Cliente
        </h4>
        
        <div class="form-group" style="margin-bottom:12px;">
            <label class="form-label" for="cobro_monto">Monto Efectivo a Cobrar</label>
            <div style="position:relative; display:flex; align-items:center;">
                <span style="position:absolute; left:12px; font-weight:600; color:var(--text-secondary);"><?= $moneda ?></span>
                <input type="number" id="cobro_monto" class="form-control" step="0.01" min="0.1" max="<?= floatval($pedido['monto_deuda']) ?>" 
                       value="<?= floatval($pedido['monto_deuda']) ?>" 
                       style="padding-left:35px; font-size:18px; font-weight:600;">
            </div>
        </div>

        <div class="form-group" style="margin-bottom:15px;">
            <label class="form-label" for="cobro_metodo">Método de Pago</label>
            <select id="cobro_metodo" class="form-control">
                <option value="efectivo">Efectivo 💵</option>
                <option value="yape">Yape 📱</option>
                <option value="plin">Plin 📱</option>
                <option value="transferencia">Transferencia 🏦</option>
                <option value="tarjeta">Tarjeta (POS) 💳</option>
            </select>
        </div>

        <button type="button" class="btn btn-primary" onclick="mostrarConfirmacion('cobro')" style="width:100%; font-weight:600;">
            Registrar Cobro
        </button>
    </div>
    <?php endif; ?>

    <div class="form-group">
        <label class="form-label" for="observaciones_repartidor">Observaciones del Reparto (Opcional)</label>
        <textarea id="observaciones_repartidor" class="form-control" rows="2"
                  placeholder="Incidencias o notas..."></textarea>
    </div>

    <?php if ($estado === 'pendiente' || $estado === 'en_proceso'): ?>
        <button type="button" id="btn-en-camino"
                class="action-btn action-btn--camino"
                onclick="mostrarConfirmacion('en_camino')">
            <i class="fa-solid fa-motorcycle"></i> Iniciar Recorrido de Entrega
        </button>
    <?php endif; ?>

    <?php if ($estado === 'en_camino'): ?>
        <?php if ($pedido['monto_deuda'] > 0): ?>
            <!-- Botón deshabilitado si hay deuda -->
            <button type="button" class="action-btn" disabled 
                    style="opacity:0.5; cursor:not-allowed; background:var(--bg-secondary); color:var(--text-primary); border:1px dashed var(--border-color);">
                <i class="fa-solid fa-lock"></i> Cobra la deuda antes de confirmar entrega
            </button>
        <?php else: ?>
            <button type="button" id="btn-entregado"
                    class="action-btn action-btn--entregado"
                    onclick="mostrarConfirmacion('entregado')">
                <i class="fa-solid fa-check-double"></i> Confirmar Entrega Exitosa
            </button>
        <?php endif; ?>

        <button type="button" id="btn-fallo"
                class="action-btn action-btn--fallo"
                onclick="mostrarConfirmacion('no_entregado')"
                style="margin-top:10px;">
            <i class="fa-solid fa-circle-xmark"></i> Notificar Fallo en Entrega
        </button>
    <?php endif; ?>
</div>
<?php endif; ?>

<!-- Confirm Bottom Sheet -->
<div id="confirmOverlay" class="confirm-overlay" role="dialog" aria-modal="true" aria-labelledby="confirmTitle">
    <div class="confirm-sheet">
        <div class="confirm-sheet__icon" id="confirmIcon"></div>
        <div class="confirm-sheet__title" id="confirmTitle"></div>
        <div class="confirm-sheet__desc"  id="confirmDesc"></div>
        <div class="confirm-sheet__actions">
            <button class="btn-cancel" id="confirmCancel">
                <i class="fa-solid fa-xmark"></i> Cancelar
            </button>
            <button class="action-btn" id="confirmBtn"></button>
        </div>
    </div>
</div>

<script>
(function () {
    var pendingEstado = null;

    var configs = {
        'cobro': {
            icon:       'fa-hand-holding-dollar',
            iconBg:     'hsla(210, 100%, 50%, 0.1)',
            iconColor:  'var(--primary-light)',
            title:      '¿Confirmar Cobro?',
            desc:       'Se registrará el cobro ingresado en el sistema.',
            btnText:    'Sí, registrar cobro',
            btnClass:   'action-btn',
        },
        'en_camino': {
            icon:       'fa-motorcycle',
            iconBg:     'hsla(283, 39%, 53%, 0.12)',
            iconColor:  'var(--status-camino)',
            title:      '¿Iniciar recorrido?',
            desc:       'El pedido cambiará a estado "En Camino" y se registrará en el sistema.',
            btnText:    'Sí, iniciar',
            btnClass:   'action-btn action-btn--camino',
        },
        'entregado': {
            icon:       'fa-check-double',
            iconBg:     'hsla(145, 63%, 49%, 0.12)',
            iconColor:  'var(--status-entregado)',
            title:      '¿Confirmar entrega?',
            desc:       'Se registrará la entrega como completada. Esta acción no se puede deshacer.',
            btnText:    'Sí, confirmar',
            btnClass:   'action-btn action-btn--entregado',
        },
        'no_entregado': {
            icon:       'fa-circle-xmark',
            iconBg:     'hsla(6, 78%, 57%, 0.1)',
            iconColor:  'var(--status-cancelado)',
            title:      '¿Notificar fallo?',
            desc:       'Se registrará un intento fallido de entrega para este pedido.',
            btnText:    'Sí, notificar',
            btnClass:   'action-btn action-btn--fallo',
        }
    };

    window.mostrarConfirmacion = function (accion) {
        var cfg = configs[accion];
        if (!cfg) return;
        pendingEstado = accion;

        var iconEl = document.getElementById('confirmIcon');
        iconEl.innerHTML          = '<i class="fa-solid ' + cfg.icon + '"></i>';
        iconEl.style.background   = cfg.iconBg;
        iconEl.style.color        = cfg.iconColor;

        if (accion === 'cobro') {
            var mto = document.getElementById('cobro_monto').value;
            var mtd = document.getElementById('cobro_metodo').options[document.getElementById('cobro_metodo').selectedIndex].text;
            document.getElementById('confirmDesc').innerHTML = 'Se registrará un cobro de <strong><?= $moneda ?>' + parseFloat(mto).toFixed(2) + '</strong> mediante <strong>' + mtd + '</strong>.';
        } else {
            document.getElementById('confirmDesc').textContent  = cfg.desc;
        }

        document.getElementById('confirmTitle').textContent = cfg.title;

        var btn       = document.getElementById('confirmBtn');
        btn.className = cfg.btnClass;
        btn.innerHTML = cfg.btnText;

        document.getElementById('confirmOverlay').classList.add('active');
    };

    document.getElementById('confirmCancel').addEventListener('click', function () {
        document.getElementById('confirmOverlay').classList.remove('active');
        pendingEstado = null;
    });

    document.getElementById('confirmOverlay').addEventListener('click', function (e) {
        if (e.target === this) {
            this.classList.remove('active');
            pendingEstado = null;
        }
    });

    document.getElementById('confirmBtn').addEventListener('click', async function () {
        if (!pendingEstado) return;

        var accion = pendingEstado;
        pendingEstado = null;
        document.getElementById('confirmOverlay').classList.remove('active');

        // Deshabilitar todos los botones
        document.querySelectorAll('button').forEach(function (b) {
            b.disabled = true;
        });

        // Loading en el confirmBtn
        var confirmBtn = document.getElementById('confirmBtn');
        confirmBtn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i> Procesando...';

        var id  = document.getElementById('pedido_id').value;
        var formData = new FormData();

        if (accion === 'cobro') {
            formData.append('monto', document.getElementById('cobro_monto').value);
            formData.append('metodo_pago', document.getElementById('cobro_metodo').value);
            
            try {
                var response = await fetch('/delivery/pedido/' + id + '/cobro', {
                    method: 'POST',
                    body:   formData
                });
                var data = await response.json();

                if (data.success) {
                    showToast(data.message, 'success');
                    setTimeout(function () { window.location.reload(); }, 1200);
                } else {
                    showToast(data.message || 'Error al registrar cobro', 'error');
                    document.querySelectorAll('button').forEach(function (b) { b.disabled = false; });
                }
            } catch (e) {
                showToast('Error de conexión', 'error');
                document.querySelectorAll('button').forEach(function (b) { b.disabled = false; });
            }
        } else {
            // Actualización de estado
            var obs = (document.getElementById('observaciones_repartidor') || {}).value || '';
            formData.append('estado', accion);
            if (obs) formData.append('observaciones', obs);

            try {
                var response = await fetch('/delivery/pedido/' + id + '/estado', {
                    method: 'POST',
                    body:   formData
                });
                var data = await response.json();

                if (data.success) {
                    showToast('Estado actualizado correctamente', 'success');
                    setTimeout(function () { window.location.reload(); }, 1200);
                } else {
                    showToast(data.message || 'Error al actualizar', 'error');
                    document.querySelectorAll('button').forEach(function (b) { b.disabled = false; });
                }
            } catch (e) {
                showToast('Error de conexión con el servidor', 'error');
                document.querySelectorAll('button').forEach(function (b) { b.disabled = false; });
            }
        }
    });
}());
</script>

<?php
$content_html = ob_get_clean();
require_once __DIR__ . '/layout.php';
?>
