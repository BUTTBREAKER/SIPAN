// SIPAN Delivery — Core JS

document.addEventListener('DOMContentLoaded', function () {

    // ── Touch feedback en pedido-cards ────────────────────────────────────
    document.querySelectorAll('.pedido-card').forEach(function (card) {
        card.addEventListener('touchstart', function () {
            card.style.transform  = 'scale(0.98)';
            card.style.transition = 'transform 0.1s ease';
        }, { passive: true });

        card.addEventListener('touchend', function () {
            card.style.transform  = '';
            card.style.transition = '';
        }, { passive: true });

        card.addEventListener('touchcancel', function () {
            card.style.transform  = '';
            card.style.transition = '';
        }, { passive: true });
    });

    // ── Pull to Refresh con indicador visual ──────────────────────────────
    // Solo activar en páginas con listado (dashboard e historial)
    var isRefreshablePage = document.getElementById('listaPedidos') ||
                            document.getElementById('listaHistorial');

    if (isRefreshablePage) {
        var THRESHOLD    = 120;
        var touchStartY  = 0;
        var isPulling    = false;

        // Crear el indicador una sola vez
        var pullEl = document.createElement('div');
        pullEl.id        = 'pullIndicator';
        pullEl.className = 'pull-indicator';
        pullEl.innerHTML = '<i class="fa-solid fa-arrow-down"></i><span>Deslizar para actualizar</span>';
        document.body.appendChild(pullEl);

        document.addEventListener('touchstart', function (e) {
            if (window.scrollY === 0) {
                touchStartY = e.changedTouches[0].screenY;
            }
        }, { passive: true });

        document.addEventListener('touchmove', function (e) {
            if (window.scrollY === 0 && touchStartY > 0) {
                var delta = e.changedTouches[0].screenY - touchStartY;
                if (delta > 40) {
                    isPulling = true;
                    pullEl.classList.add('visible');

                    if (delta > THRESHOLD) {
                        pullEl.classList.add('pull-indicator--releasing');
                        pullEl.querySelector('span').textContent = 'Soltar para actualizar';
                        pullEl.querySelector('i').className      = 'fa-solid fa-rotate-right';
                    } else {
                        pullEl.classList.remove('pull-indicator--releasing');
                        pullEl.querySelector('span').textContent = 'Deslizar para actualizar';
                        pullEl.querySelector('i').className      = 'fa-solid fa-arrow-down';
                    }
                }
            }
        }, { passive: true });

        document.addEventListener('touchend', function (e) {
            if (!isPulling) return;

            var delta = e.changedTouches[0].screenY - touchStartY;
            if (delta > THRESHOLD) {
                pullEl.querySelector('span').textContent = 'Actualizando...';
                pullEl.querySelector('i').className      = 'fa-solid fa-circle-notch fa-spin';
                setTimeout(function () { window.location.reload(); }, 600);
            } else {
                pullEl.classList.remove('visible', 'pull-indicator--releasing');
            }

            isPulling   = false;
            touchStartY = 0;
        }, { passive: true });
    }
});

// ── Toast Global ──────────────────────────────────────────────────────────
window.showToast = function (msg, type) {
    type = type || 'success';

    var toast = document.getElementById('toast');
    if (!toast) {
        toast    = document.createElement('div');
        toast.id = 'toast';
        document.body.appendChild(toast);
    }

    toast.textContent = msg;
    toast.className   = 'toast show ' + type;

    clearTimeout(toast._hideTimer);
    toast._hideTimer = setTimeout(function () {
        toast.className = 'toast ' + type;
    }, 3500);

    return toast;
};
