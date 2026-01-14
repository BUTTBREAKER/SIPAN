<!DOCTYPE html>
<html lang="es">
<head>
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
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/dashboard.css">
    <link rel="stylesheet" href="/assets/css/driver.css">
    <link rel="stylesheet" href="/assets/css/tables.css">
    
    <!-- Scripts -->
    <script src="/assets/js/driver.js.iife.js"></script>
    <script src="/assets/js/tour.js" defer></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="/assets/js/notifications.js"></script>
    <!-- app.js moved to footer to prevent duplicate executions -->
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Grid.js -->
    <link href="https://unpkg.com/gridjs/dist/theme/mermaid.min.css" rel="stylesheet" />
    <script src="https://unpkg.com/gridjs/dist/gridjs.umd.js"></script>
    <script src="/assets/js/grid-init.js" defer></script>
</head>
<body>
    <!-- Page Loader -->
    <div id="page-loader">
        <div class="loader-content">
            <div class="spinner"></div>
            <h5 class="brand-font" style="color: var(--text-main);">Cargando Sipan...</h5>
        </div>
    </div>
    <script>
        window.addEventListener('load', function() {
            const loader = document.getElementById('page-loader');
            loader.classList.add('loader-hidden');
            setTimeout(() => {
                loader.style.display = 'none';
            }, 500);
        });
    </script>

    <div class="app-wrapper">
        <div class="sidebar-overlay" id="sidebarOverlay"></div>
        <?php require_once __DIR__ . '/sidebar.php'; ?>
        
        <div class="main-content">
            <nav class="navbar">
                <div class="d-flex align-items-center gap-2 flex-grow-1">
                    <!-- Mobile Toggle -->
                    <button class="btn btn-link d-lg-none p-0 text-dark me-2 flex-shrink-0" id="sidebarToggleHeader" aria-label="Abrir Menú">
                        <i class="fas fa-bars fs-3"></i>
                    </button>

                    <h1 class="page-title mb-0 fs-4 d-none d-sm-block"><?= $pageTitle ?? 'Dashboard' ?></h1>
                    
                    <!-- Logo for very small screens -->
                    <div class="d-sm-none brand-font fs-4 text-primary fw-bold flex-shrink-0">SIPAN</div>

                    <!-- Tasa BCV Global -->
                    <?php
                    if (isset($_SESSION['user_id'])) {
                        require_once __DIR__ . '/../../Models/Configuracion.php';
                        $tasaGlobal = (new \App\Models\Configuracion())->getTasaBCV();
                    }
                    ?>
                    <?php if (isset($tasaGlobal)) : ?>
                    <div class="d-none d-sm-flex align-items-center bg-white px-3 py-1 rounded shadow-sm border ms-3">
                        <span class="text-muted small text-uppercase me-2 fw-bold" style="font-size: 0.7rem;">Tasa BCV</span>
                        <span class="fw-bold text-success" id="header-tasa">Bs <?= number_format($tasaGlobal, 2) ?></span>
                        <button class="btn btn-sm btn-link text-primary p-0 ms-2" onclick="refreshTasaBCV()" title="Actualizar Tasa" id="btn-refresh-tasa">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                    </div>
                    <?php endif; ?>
                </div>
                
                <div class="navbar-right d-flex align-items-center gap-4" x-data="notificationsApp()">

                    <?php if (($_SESSION['user_rol'] ?? '') === 'administrador') : ?>
                    <div class="position-relative">
                        <select class="form-select form-select-sm border-0 bg-light fw-medium" 
                                style="min-width: 140px; cursor: pointer; box-shadow: none;"
                                onchange="cambiarSucursal(this.value)">
                            <option value="">Sucursal...</option>
                            <?php
                            require_once __DIR__ . '/../../Models/Sucursal.php';
                            $sucursalModel = new \App\Models\Sucursal();
                            $sucursales = $sucursalModel->getActivas();
                            foreach ($sucursales as $sucursal) :
                                ?>
                            <option value="<?= $sucursal['id'] ?>" <?= $sucursal['id'] == $_SESSION['sucursal_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($sucursal['nombre']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Botón de Guía/Tour -->
                    <button onclick="startContextualTour('<?= $currentPage ?? 'dashboard' ?>')" 
                            class="btn btn-link p-0 text-secondary" 
                            title="Ver guía de esta página"
                            id="btn-tour-guide">
                        <i class="fas fa-question-circle" style="font-size: 1.25rem;"></i>
                    </button>

                    <!-- Notificaciones -->
                    <div class="notifications-wrapper position-relative">
                        <button @click="toggleNotifications()" class="btn btn-link p-0 position-relative text-secondary">
                            <i class="fas fa-bell" style="font-size: 1.25rem;"></i>
                            <span x-show="count > 0" 
                                  x-text="count" 
                                  class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-light" 
                                  style="font-size: 0.65rem; padding: 0.25em 0.4em;">
                            </span>
                        </button>
                        
                        <div x-show="showNotifications" 
                             @click.away="showNotifications = false"
                             x-transition
                             class="dropdown-menu show p-0 border-0 shadow-lg mt-3 end-0"
                             style="position: absolute; width: 320px; max-height: 400px; overflow-y: auto; z-index: 1000; right: -10px;">
                            
                            <div class="p-3 border-bottom d-flex justify-content-between align-items-center bg-white sticky-top">
                                <h6 class="m-0 fw-bold text-dark">Notificaciones</h6>
                                <button @click="markAllAsRead()" class="btn btn-sm text-primary text-decoration-none p-0" style="font-size: 0.8rem;">
                                    Marcar leídas
                                </button>
                            </div>
                            
                            <div x-show="notifications.length === 0" class="p-4 text-center text-muted">
                                <i class="fas fa-bell-slash fa-2x mb-2 opacity-50"></i>
                                <p class="small m-0">Todo al día</p>
                            </div>
                            
                            <template x-for="notif in notifications" :key="notif.id">
                                <div class="p-3 border-bottom hover-bg-light cursor-pointer transition-all"
                                     @click="markAsRead(notif.id)">
                                    <div class="d-flex gap-3">
                                        <div :class="'rounded-circle p-2 d-flex align-items-center justify-content-center flex-shrink-0 bg-light text-' + getContextColor(notif.tipo)" 
                                             style="width: 36px; height: 36px;">
                                            <i :class="getIcon(notif.tipo)"></i>
                                        </div>
                                        <div>
                                            <p class="mb-1 text-dark small" style="line-height: 1.4;" x-text="notif.mensaje"></p>
                                            <span class="text-muted" style="font-size: 0.7rem;" x-text="formatDate(notif.fecha_creacion)"></span>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                    
                    <div class="d-flex align-items-center gap-2 ps-2 ps-md-3 border-start">
                        <div class="text-end d-none d-lg-block" style="line-height: 1.2;">
                            <div class="fw-bold text-dark small"><?= htmlspecialchars($_SESSION['user_nombre'] ?? 'Usuario') ?></div>
                            <div class="text-muted" style="font-size: 0.75rem;"><?= ucfirst($_SESSION['user_rol'] ?? 'Invitado') ?></div>
                        </div>
                        <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center fw-bold shadow-sm"
                             style="width: 38px; height: 38px; font-size: 0.9rem;">
                            <?php
                                $nombre = $_SESSION['user_nombre'] ?? 'U';
                                echo strtoupper(substr($nombre, 0, 2));
                            ?>
                        </div>
                        <a href="/logout" class="text-muted ms-1 d-none d-sm-inline-block" title="Cerrar sesión">
                            <i class="fas fa-sign-out-alt"></i>
                        </a>
                    </div>
                </div>
            </nav>
            
            <div class="content">
                <?php if (isset($_SESSION['flash_message'])) : ?>
                    <?php
                    $flashMessage = $_SESSION['flash_message'];
                    $flashType = $_SESSION['flash_type'] ?? 'info';

                    if (is_array($flashMessage)) {
                        $flashType = $flashMessage['type'] ?? $flashType;
                        $flashMessage = $flashMessage['content'] ?? '';
                    }
                    ?>
                <div class="alert alert-<?= $flashType ?> alert-dismissible fade show border-0 shadow-sm mb-4 mx-4" role="alert">
                    <?= $flashMessage ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
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
        lastCount: 0, // Para detectar nuevas
        
        async init() {
            await this.loadNotifications();
            // Actualizar cada 30 segundos
            setInterval(() => this.loadNotifications(), 30000);
            
            // Solicitar permiso al interactuar (opcional, o automático si se prefiere)
            // window.notificationManager.requestPermission();
        },
        
        async loadNotifications() {
            try {
                const response = await fetch('/notificaciones/no-leidas');
                const data = await response.json();
                if (data.success) {
                    this.notifications = data.notificaciones;
                    this.count = data.notificaciones.length;
                    
                    // Si hay más notificaciones que antes, lanzar alerta
                    if (this.count > this.lastCount && this.count > 0) {
                        const nueva = this.notifications[0]; // La más reciente
                        window.notificationManager.showNotification('Nueva Notificación SIPAN', nueva.mensaje);
                    }
                    this.lastCount = this.count;
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
                const response = await fetch(`/notificaciones/marcar-leida/${id}`, {
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
                const response = await fetch('/notificaciones/marcar-todas-leidas', {
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
        
        getContextColor(tipo) {
            const colors = {
                'stock_bajo': 'warning',
                'venta': 'success',
                'pedido': 'info',
                'produccion': 'secondary',
                'sistema': 'primary'
            };
            return colors[tipo] || 'secondary';
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

<script>
async function checkInsumosSinProveedor() {
    try {
        const res = await fetch('/proveedores/insumos-sin-proveedor', { method: 'GET' });
        const data = await res.json();
        if (data.success && data.insumos && data.insumos.length) {
            // Construir lista breve
            let html = '<ul style="text-align:left;">';
            data.insumos.forEach(i => {
                html += `<li><strong>${i.nombre}</strong> — Stock: ${i.stock_actual} ${i.unidad_medida} (mín: ${i.stock_minimo}) 
                         <a href="/insumos/edit/${i.id}" class="ms-2">Editar insumo</a>
                         <a href="/proveedores/create?prefill_insumo=${i.id}" class="ms-2">Asignar proveedor</a>
                         </li>`;
            });
            html += '</ul>';

            Swal.fire({
                title: 'Insumos sin proveedor',
                html: html,
                icon: 'warning',
                confirmButtonText: 'Ir a insumos',
                confirmButtonColor: '#D4A574'
            });
        }
    } catch (err) {
        console.error('Error verificando insumos sin proveedor', err);
    }
}

// Ejecutar al cargar (solo en páginas relevantes, por ejemplo dashboard)
document.addEventListener('DOMContentLoaded', () => {
    // opcional: correr solo en dashboard
    if (window.location.pathname === '/dashboard' || window.location.pathname === '/') {
        checkInsumosSinProveedor();
    }
});

async function refreshTasaBCV() {
    const btn = document.getElementById('btn-refresh-tasa');
    const icon = btn.querySelector('i');
    const label = document.getElementById('header-tasa');
    
    if(icon) icon.classList.add('fa-spin');
    if(btn) btn.disabled = true;
    
    try {
        const response = await fetch('/config/refresh-tasa'); 
        const data = await response.json();
        
        if (data.success) {
            if(label) label.textContent = 'Bs ' + parseFloat(data.rate).toFixed(2);
            if (typeof SIPAN !== 'undefined') SIPAN.success('Tasa actualizada');
        } else {
             if (typeof SIPAN !== 'undefined') SIPAN.error(data.message || 'Error al actualizar');
        }
    } catch(e) {
        console.error(e);
        if (typeof SIPAN !== 'undefined') SIPAN.error('Error de conexión');
    } finally {
        if(icon) icon.classList.remove('fa-spin');
        if(btn) btn.disabled = false;
    }
}
</script>
