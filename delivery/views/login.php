<?php
$page_title  = 'Login - SIPAN Delivery';
$hide_layout = true;
ob_start();
?>

<div class="login-wrap">

    <div class="login-logo animate-fade-in">
        <div class="login-logo__icon">
            <i class="fa-solid fa-motorcycle"></i>
        </div>
        <h1 class="login-logo__title">SIPAN<br><span>Delivery</span></h1>
        <p class="login-logo__subtitle">Gestión Inteligente de Repartos</p>
    </div>

    <div class="login-card">
        <h2 class="login-card__title">Bienvenido</h2>

        <form id="loginForm" novalidate>

            <div class="form-group">
                <label class="form-label" for="correo">Correo Electrónico</label>
                <input type="email" name="correo" id="correo"
                       class="form-control" required
                       placeholder="tu@correo.com"
                       autocomplete="email">
            </div>

            <div class="form-group">
                <label class="form-label" for="clave">Contraseña</label>
                <input type="password" name="clave" id="clave"
                       class="form-control" required
                       placeholder="••••••••"
                       autocomplete="current-password">
            </div>

            <button type="submit" class="btn btn-primary" id="btnSubmit">
                <span>Acceder al Sistema</span>
                <i class="fa-solid fa-arrow-right"></i>
            </button>

        </form>

        <p class="login-card__footer">
            SIPAN &copy; <?= date('Y') ?> &mdash; Todos los derechos reservados
        </p>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var form      = document.getElementById('loginForm');
    var btnSubmit = document.getElementById('btnSubmit');

    form.addEventListener('submit', async function (e) {
        e.preventDefault();

        btnSubmit.disabled = true;
        btnSubmit.classList.add('btn--loading');
        btnSubmit.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i> Ingresando...';

        try {
            var response = await fetch('/delivery/login', {
                method: 'POST',
                body:   new FormData(form)
            });
            var data = await response.json();

            if (data.success) {
                window.location.href = data.redirect;
            } else {
                showToast(data.message, 'error');
                btnSubmit.disabled = false;
                btnSubmit.classList.remove('btn--loading');
                btnSubmit.innerHTML = '<span>Acceder al Sistema</span><i class="fa-solid fa-arrow-right"></i>';
            }
        } catch (err) {
            showToast('Error de conexión con el servidor', 'error');
            btnSubmit.disabled = false;
            btnSubmit.classList.remove('btn--loading');
            btnSubmit.innerHTML = '<span>Acceder al Sistema</span><i class="fa-solid fa-arrow-right"></i>';
        }
    });
});
</script>

<?php
$content_html = ob_get_clean();
require_once __DIR__ . '/layout.php';
?>
