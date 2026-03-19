<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">

    <!-- Meta TAGS para App Móvil PWA -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="theme-color" content="#D4A574">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <title><?= $page_title ?? 'SIPAN Delivery' ?></title>

    <!-- PWA Manifest -->
    <link rel="manifest" href="/delivery-manifest.json">
    <link rel="apple-touch-icon" href="/assets/delivery/icons/icon-192x192.png">

    <!-- Fuentes y CSS -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/assets/delivery/css/delivery.css?v=<?= time() ?>">
</head>
<body>

    <?php if (!isset($hide_layout) || !$hide_layout): ?>

        <!-- App Header: si la vista define $custom_header_html se usa ese; de lo contrario el header por defecto -->
        <?php if (isset($custom_header_html)): ?>
            <?= $custom_header_html ?>
        <?php else: ?>
        <header class="app-header">
            <div class="app-title">SIPAN DELIVERY</div>
            <?php if (isset($_SESSION['user_nombre'])): ?>
                <div class="app-header__user">
                    <i class="fa-solid fa-user-circle"></i>
                    <?= htmlspecialchars(explode(' ', $_SESSION['user_nombre'])[0]) ?>
                </div>
            <?php endif; ?>
        </header>
        <?php endif; ?>

        <!-- Main Content wrapper -->
        <main class="main-content">

    <?php endif; ?>

        <!-- CONTENIDO DINÁMICO -->
        <?php
        if (!isset($content_html)) {
            echo '<div style="padding:20px;text-align:center;">Cargando contenido...</div>';
        } else {
            echo $content_html;
        }
        ?>

    <?php if (!isset($hide_layout) || !$hide_layout): ?>

        </main>

        <!-- Bottom Navigation -->
        <nav class="bottom-nav">
            <?php $current_path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); ?>
            <a href="/delivery/dashboard" class="nav-item <?= strpos($current_path, '/dashboard') !== false ? 'active' : '' ?>">
                <i class="fa-solid fa-list-check"></i>
                Pedidos
            </a>
            <a href="/delivery/estadisticas" class="nav-item <?= strpos($current_path, '/estadisticas') !== false ? 'active' : '' ?>">
                <i class="fa-solid fa-chart-simple"></i>
                Stats
            </a>
            <a href="/delivery/historial" class="nav-item <?= strpos($current_path, '/historial') !== false ? 'active' : '' ?>">
                <i class="fa-solid fa-clock-rotate-left"></i>
                Historial
            </a>
            <a href="/delivery/logout" class="nav-item">
                <i class="fa-solid fa-right-from-bracket"></i>
                Salir
            </a>
        </nav>

    <?php endif; ?>

    <!-- Toast Notification Container -->
    <div id="toast" class="toast"></div>

    <!-- PWA Service Worker Registration -->
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/delivery-sw.js')
                    .then(reg  => console.log('SW registrado. Scope:', reg.scope))
                    .catch(err => console.log('SW registro fallido:', err));
            });
        }
    </script>

    <!-- Core JS -->
    <script src="/assets/delivery/js/delivery.js?v=<?= time() ?>"></script>
</body>
</html>
