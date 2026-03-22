<nav class="sidebar" id="appSidebar" aria-label="Navegación principal">
    <?php $currentPage = $currentPage ?? ''; ?>
    <div class="sidebar-header">
        <a href="/dashboard" class="sidebar-logo">
            <i class="fas fa-bread-slice logo-icon"></i>
            <span class="logo-text">SIPAN</span>
        </a>
        <button id="sidebarToggleMobile" class="d-lg-none btn-icon-only text-white bg-transparent border-0 fs-4">
            <i class="fas fa-times"></i>
        </button>
    </div>
    
    <div class="sidebar-content">
        <!-- Grupo: Principal -->
        <div class="nav-group" data-group="main">
            <div class="nav-group-header" title="Principal">
                <i class="fas fa-home group-icon-anchor"></i>
                <span class="group-title">Principal</span>
            </div>
            <ul class="nav-links">
                <li>
                    <a href="/dashboard" class="nav-link <?= $currentPage === 'dashboard' ? 'active' : '' ?>">
                        <i class="fas fa-chart-line"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
            </ul>
        </div>

        <!-- Grupo: Inventario -->
        <div class="nav-group" data-group="inventario">
            <div class="nav-group-header" title="Inventario">
                <i class="fas fa-boxes group-icon-anchor"></i>
                <span class="group-title">Inventario</span>
            </div>
            <ul class="nav-links">
                <li>
                    <a href="/productos" class="nav-link <?= $currentPage === 'productos' ? 'active' : '' ?>">
                        <i class="fas fa-bread-slice"></i>
                        <span>Productos</span>
                    </a>
                </li>
                <li>
                    <a href="/insumos" class="nav-link <?= $currentPage === 'insumos' ? 'active' : '' ?>">
                        <i class="fas fa-cubes"></i>
                        <span>Insumos</span>
                    </a>
                </li>
                <li>
                    <a href="/recetas" class="nav-link <?= $currentPage === 'recetas' ? 'active' : '' ?>">
                        <i class="fas fa-book-open"></i>
                        <span>Recetas</span>
                    </a>
                </li>
            </ul>
        </div>

        <!-- Grupo: Operaciones -->
        <div class="nav-group" data-group="operaciones">
            <div class="nav-group-header" title="Operaciones">
                <i class="fas fa-cash-register group-icon-anchor"></i>
                <span class="group-title">Operaciones</span>
            </div>
            <ul class="nav-links">
                <li>
                    <a href="/ventas" class="nav-link <?= $currentPage === 'ventas' ? 'active' : '' ?>">
                        <i class="fas fa-cash-register"></i>
                        <span>Ventas</span>
                    </a>
                </li>
                <li>
                    <a href="/pedidos" class="nav-link <?= $currentPage === 'pedidos' ? 'active' : '' ?>">
                        <i class="fas fa-shopping-bag"></i>
                        <span>Pedidos</span>
                    </a>
                </li>
                <li>
                    <a href="/producciones" class="nav-link <?= $currentPage === 'producciones' ? 'active' : '' ?>">
                        <i class="fas fa-industry"></i>
                        <span>Producción</span>
                    </a>
                </li>
                <li>
                    <a href="/cajas" class="nav-link <?= ($currentPage ?? '') === 'cajas' ? 'active' : '' ?>">
                        <i class="fas fa-cash-register"></i>
                        <span>Caja Chica</span>
                    </a>
                </li>
            </ul>
        </div>

        <!-- Grupo: Gestión -->
        <div class="nav-group" data-group="gestion">
            <div class="nav-group-header" title="Gestión">
                <i class="fas fa-users group-icon-anchor"></i>
                <span class="group-title">Gestión</span>
            </div>
            <ul class="nav-links">
                <li>
                    <a href="/clientes" class="nav-link <?= $currentPage === 'clientes' ? 'active' : '' ?>">
                        <i class="fas fa-user-friends"></i>
                        <span>Clientes</span>
                    </a>
                </li>
                <li>
                    <a href="/proveedores" class="nav-link <?= ($currentPage ?? '') === 'proveedores' ? 'active' : '' ?>">
                        <i class="fas fa-truck"></i>
                        <span>Proveedores</span>
                    </a>
                </li>
                <li>
                    <a href="/compras" class="nav-link <?= ($currentPage ?? '') === 'compras' ? 'active' : '' ?>">
                        <i class="fas fa-file-invoice"></i>
                        <span>Compras</span>
                    </a>
                </li>
                <li>
                    <a href="/chat" class="nav-link <?= ($currentPage ?? '') === 'chat' ? 'active' : '' ?>" style="position:relative;">
                        <i class="fas fa-comments"></i>
                        <span>Chat</span>
                        <span id="chat-badge" class="position-absolute badge rounded-pill bg-danger" 
                              style="display:none; top:6px; right:8px; font-size:.6rem; padding:3px 6px;">0</span>
                    </a>
                </li>
            </ul>
        </div>

        <!-- Grupo: Inteligencia -->
        <div class="nav-group" data-group="inteligencia">
            <div class="nav-group-header" title="Inteligencia">
                <i class="fas fa-brain group-icon-anchor"></i>
                <span class="group-title">Inteligencia</span>
            </div>
            <ul class="nav-links">
                <li>
                    <a href="/reportes" class="nav-link <?= ($currentPage ?? '') === 'reportes' ? 'active' : '' ?>">
                        <i class="fas fa-chart-pie"></i>
                        <span>Reportes</span>
                    </a>
                </li>
                <li>
                    <a href="/predicciones" class="nav-link <?= ($currentPage ?? '') === 'predicciones' ? 'active' : '' ?>">
                        <i class="fas fa-magic"></i>
                        <span>Predicciones</span>
                    </a>
                </li>
                <li>
                    <a href="/sugerencias" class="nav-link <?= ($currentPage ?? '') === 'sugerencias' ? 'active' : '' ?>">
                        <i class="fas fa-lightbulb"></i>
                        <span>Sugerencias</span>
                    </a>
                </li>
            </ul>
        </div>

        <?php if (($_SESSION['user_rol'] ?? '') === 'administrador') : ?>
        <!-- Grupo: Sistema -->
        <div class="nav-group" data-group="sistema">
            <div class="nav-group-header" title="Sistema">
                <i class="fas fa-cogs group-icon-anchor"></i>
                <span class="group-title">Sistema</span>
            </div>
            <ul class="nav-links">
                <li>
                    <a href="/usuarios" class="nav-link <?= $currentPage === 'usuarios' ? 'active' : '' ?>">
                        <i class="fas fa-users-cog"></i>
                        <span>Usuarios</span>
                    </a>
                </li>
                <li>
                    <a href="/auditorias" class="nav-link <?= $currentPage === 'auditorias' ? 'active' : '' ?>">
                        <i class="fas fa-shield-alt"></i>
                        <span>Auditoría</span>
                    </a>
                </li>
                <li>
                    <a href="/sucursales" class="nav-link <?= $currentPage === 'sucursales' ? 'active' : '' ?>">
                        <i class="fas fa-store-alt"></i>
                        <span>Sucursales</span>
                    </a>
                </li>
                <li>
                    <a href="/respaldos" class="nav-link <?= $currentPage === 'respaldos' ? 'active' : '' ?>">
                        <i class="fas fa-database"></i>
                        <span>Respaldos</span>
                    </a>
                </li>
            </ul>
        </div>
        <?php endif; ?>
        
        <!-- Grupo: Usuario -->
        <div class="nav-group" data-group="usuario">
            <div class="nav-group-header" title="Perfil">
                <i class="fas fa-user-circle group-icon-anchor"></i>
                <span class="group-title">Mi Cuenta</span>
            </div>
            <ul class="nav-links">
                <li>
                    <a href="/usuarios/perfil" class="nav-link <?= $currentPage === 'perfil' ? 'active' : '' ?>">
                        <i class="fas fa-id-card"></i>
                        <span>Mi Perfil</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<script>
function cambiarSucursal(sucursalId) {
    if (!sucursalId) return;
    
    const formData = new FormData();
    formData.append('sucursal_id', sucursalId);
    
    fetch('/auth/cambiar-sucursal', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            SIPAN.success(data.message);
            setTimeout(() => window.location.reload(), 1000);
        } else {
            SIPAN.error(data.message);
        }
    })
    .catch(error => {
        SIPAN.error('Error al cambiar de sucursal');
        console.error('Error:', error);
    });
}
</script>

<!-- Chat Badge: Pulse Animation + Global Polling (60s) -->
<style>
.chat-has-unread {
    position: relative;
}
.chat-has-unread i.fa-comments {
    animation: chatPulse 2s ease-in-out infinite;
    color: #4e6bff !important;
}
@keyframes chatPulse {
    0%, 100% { transform: scale(1); opacity: 1; }
    50% { transform: scale(1.2); opacity: .7; }
}
.chat-has-unread::after {
    content: '';
    position: absolute;
    top: 8px;
    right: 8px;
    width: 8px;
    height: 8px;
    background: #e74c3c;
    border-radius: 50%;
    animation: dotBlink 1.5s ease-in-out infinite;
}
@keyframes dotBlink {
    0%, 100% { opacity: 1; }
    50% { opacity: .3; }
}
</style>
<script>
// Global chat badge sync — 1 petición cada 60s desde CUALQUIER página
(function() {
    // No ejecutar en la página de chat (ya tiene su propio sync)
    if (window.location.pathname === '/chat') return;

    let globalChatTimer = null;

    async function syncChatBadge() {
        try {
            const res = await fetch('/chat/sync?');
            const data = await res.json();
            if (data.success) {
                const badge = document.getElementById('chat-badge');
                if (badge) {
                    badge.textContent = data.no_leidos;
                    badge.style.display = data.no_leidos > 0 ? 'flex' : 'none';
                }
                const chatLink = document.querySelector('a[href="/chat"]');
                if (chatLink) {
                    if (data.no_leidos > 0) {
                        chatLink.classList.add('chat-has-unread');
                    } else {
                        chatLink.classList.remove('chat-has-unread');
                    }
                }
                // Push notification si la tab está oculta
                if (data.no_leidos > 0 && document.hidden && window.notificationManager) {
                    window.notificationManager.showNotification('💬 SIPAN Chat', 'Tienes ' + data.no_leidos + ' mensaje(s) sin leer');
                }
            }
        } catch(e) { /* silencioso */ }
    }

    function startGlobalSync() {
        if (globalChatTimer) clearInterval(globalChatTimer);
        syncChatBadge(); // Inicial
        globalChatTimer = setInterval(syncChatBadge, 60000); // Cada 60s
    }

    // Page Visibility API
    document.addEventListener('visibilitychange', () => {
        if (document.hidden) {
            if (globalChatTimer) clearInterval(globalChatTimer);
        } else {
            startGlobalSync();
        }
    });

    if (document.readyState === 'complete' || document.readyState === 'interactive') {
        startGlobalSync();
    } else {
        document.addEventListener('DOMContentLoaded', startGlobalSync);
    }
})();
</script>
