<!DOCTYPE html>
<html lang="es">
<head>
    <base href="<?= str_replace('index.php', '', $_SERVER['SCRIPT_NAME']) ?>" />
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'SIPAN - Sistema Integral para Panaderías' ?></title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="./assets/css/style.css">
    <link rel="stylesheet" href="./assets/css/dashboard.css">

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="app-wrapper">
        <?php require_once __DIR__ . '/sidebar.php'; ?>

        <div class="main-content">
            <nav class="navbar">
                <div class="navbar-left">
                    <h1 class="page-title mb-0"><?= $pageTitle ?? 'Dashboard' ?></h1>
                </div>

                <div class="navbar-right" x-data="notificationsApp()">
                    <?php if ($_SESSION['user_rol'] === 'administrador'): ?>
                    <select class="sucursal-selector" onchange="cambiarSucursal(this.value)">
                        <option value="">Seleccionar Sucursal</option>
                        <?php
                        require_once __DIR__ . '/../../Models/Sucursal.php';
                        $sucursalModel = new \SIPAN\Models\Sucursal();
                        $sucursales = $sucursalModel->getActivas();
                        foreach ($sucursales as $sucursal):
                        ?>
                        <option value="<?= $sucursal['id'] ?>" <?= $sucursal['id'] == $_SESSION['sucursal_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($sucursal['nombre']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                    <?php endif; ?>

                    <!-- Notificaciones -->
                    <div class="notifications-wrapper" style="position: relative; margin-left: 1rem;">
                        <button @click="toggleNotifications()" class="btn btn-link" style="position: relative; padding: 0.5rem;">
                            <i class="fas fa-bell" style="font-size: 1.5rem; color: #8B6F47;"></i>
                            <span x-show="count > 0"
                                  x-text="count"
                                  class="badge bg-danger"
                                  style="position: absolute; top: 0; right: 0; font-size: 0.7rem; padding: 0.25rem 0.4rem; border-radius: 50%;">
                            </span>
                        </button>

                        <div x-show="showNotifications"
                             @click.away="showNotifications = false"
                             x-transition
                             class="notifications-dropdown"
                             style="position: absolute; top: 100%; right: 0; margin-top: 10px; width: 350px; background: white; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); z-index: 1000; max-height: 400px; overflow-y: auto;">

                            <div style="padding: 1rem; border-bottom: 1px solid #e0e0e0; display: flex; justify-content: space-between; align-items: center;">
                                <h6 style="margin: 0; font-weight: 600;">Notificaciones</h6>
                                <button @click="markAllAsRead()" class="btn btn-sm btn-link" style="font-size: 0.8rem;">
                                    Marcar todas como leídas
                                </button>
                            </div>

                            <div x-show="notifications.length === 0" style="padding: 2rem; text-align: center; color: #6c757d;">
                                <i class="fas fa-bell-slash fa-2x mb-2"></i>
                                <p>No hay notificaciones nuevas</p>
                            </div>

                            <template x-for="notif in notifications" :key="notif.id">
                                <div style="padding: 1rem; border-bottom: 1px solid #f0f0f0; cursor: pointer;"
                                     @click="markAsRead(notif.id)">
                                    <div style="display: flex; align-items: start; gap: 0.75rem;">
                                        <div :class="'notif-icon notif-' + notif.tipo" style="width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                            <i :class="getIcon(notif.tipo)" style="font-size: 1.2rem;"></i>
                                        </div>
                                        <div style="flex: 1;">
                                            <div style="font-weight: 600; margin-bottom: 0.25rem; color: #333;" x-text="notif.mensaje"></div>
                                            <div style="font-size: 0.75rem; color: #999;" x-text="formatDate(notif.fecha_creacion)"></div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <style>
                        .notif-icon { background: #f0f0f0; color: #666; }
                        .notif-icon.notif-stock_bajo { background: #fff3cd; color: #856404; }
                        .notif-icon.notif-venta { background: #d4edda; color: #155724; }
                        .notif-icon.notif-pedido { background: #d1ecf1; color: #0c5460; }
                        .notif-icon.notif-produccion { background: #e2e3e5; color: #383d41; }
                    </style>

                    <div class="user-info">
                        <div class="user-avatar">
                            <?= strtoupper(substr($_SESSION['user_nombre'], 0, 2)) ?>
                        </div>
                        <div>
                            <div class="user-name"><?= htmlspecialchars($_SESSION['user_nombre']) ?></div>
                            <div class="user-role"><?= ucfirst($_SESSION['user_rol']) ?></div>
                        </div>
                    </div>

                    <a href="./logout" class="btn btn-secondary btn-sm">
                        <i class="fas fa-sign-out-alt"></i> Salir
                    </a>
                </div>
            </nav>

            <div class="content">
                <?php if (isset($_SESSION['flash_message'])): ?>
                <div class="alert alert-<?= $_SESSION['flash_type'] ?? 'info' ?>">
                    <?= $_SESSION['flash_message'] ?>
                </div>
                <?php
                unset($_SESSION['flash_message']);
                unset($_SESSION['flash_type']);
                endif;
                ?>

<script>
function notificationsApp() {
    return {
        showNotifications: false,
        notifications: [],
        count: 0,

        async init() {
            await this.loadNotifications();
            // Actualizar cada 30 segundos
            setInterval(() => this.loadNotifications(), 30000);
        },

        async loadNotifications() {
            try {
                const response = await fetch('./notificaciones/no-leidas');
                const data = await response.json();
                if (data.success) {
                    this.notifications = data.notificaciones;
                    this.count = data.notificaciones.length;
                }
            } catch (error) {
                console.error('Error cargando notificaciones:', error);
            }
        },

        toggleNotifications() {
            this.showNotifications = !this.showNotifications;
        },

        async markAsRead(id) {
            try {
                const response = await fetch(`./notificaciones/marcar-leida/${id}`, {
                    method: 'POST'
                });
                const data = await response.json();
                if (data.success) {
                    await this.loadNotifications();
                }
            } catch (error) {
                console.error('Error marcando notificación:', error);
            }
        },

        async markAllAsRead() {
            try {
                const response = await fetch('./notificaciones/marcar-todas-leidas', {
                    method: 'POST'
                });
                const data = await response.json();
                if (data.success) {
                    await this.loadNotifications();
                    this.showNotifications = false;
                }
            } catch (error) {
                console.error('Error marcando todas:', error);
            }
        },

        getIcon(tipo) {
            const icons = {
                'stock_bajo': 'fas fa-exclamation-triangle',
                'venta': 'fas fa-cash-register',
                'pedido': 'fas fa-shopping-cart',
                'produccion': 'fas fa-industry',
                'sistema': 'fas fa-info-circle'
            };
            return icons[tipo] || 'fas fa-bell';
        },

        formatDate(dateString) {
            const date = new Date(dateString);
            const now = new Date();
            const diff = Math.floor((now - date) / 1000); // segundos

            if (diff < 60) return 'Hace un momento';
            if (diff < 3600) return `Hace ${Math.floor(diff / 60)} min`;
            if (diff < 86400) return `Hace ${Math.floor(diff / 3600)} h`;
            return date.toLocaleDateString('es-ES');
        }
    };
}
</script>
