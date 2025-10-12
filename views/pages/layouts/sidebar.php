<aside class="sidebar">
    <div class="sidebar-header">
        <a href="./dashboard" class="sidebar-logo">
            <i class="fas fa-bread-slice"></i> SIPAN
        </a>
        <p style="font-size: 0.8rem; opacity: 0.8; margin-top: 0.5rem;">Sistema Integral para Panaderías</p>
    </div>
    
    <ul class="sidebar-nav">
        <li class="nav-item">
            <a href="./dashboard" class="nav-link <?= $currentPage === 'dashboard' ? 'active' : '' ?>">
                <i class="fas fa-home"></i>
                <span>Dashboard</span>
            </a>
        </li>
        
        <li class="nav-item">
            <a href="./productos" class="nav-link <?= $currentPage === 'productos' ? 'active' : '' ?>">
                <i class="fas fa-box"></i>
                <span>Productos</span>
            </a>
        </li>
        
        <li class="nav-item">
            <a href="./insumos" class="nav-link <?= $currentPage === 'insumos' ? 'active' : '' ?>">
                <i class="fas fa-cubes"></i>
                <span>Insumos</span>
            </a>
        </li>
        
        <li class="nav-item">
            <a href="./recetas" class="nav-link <?= $currentPage === 'recetas' ? 'active' : '' ?>">
                <i class="fas fa-book"></i>
                <span>Recetas</span>
            </a>
        </li>
        
        <li class="nav-item">
            <a href="./producciones" class="nav-link <?= $currentPage === 'producciones' ? 'active' : '' ?>">
                <i class="fas fa-industry"></i>
                <span>Producciones</span>
            </a>
        </li>
        
        <li class="nav-item">
            <a href="./ventas" class="nav-link <?= $currentPage === 'ventas' ? 'active' : '' ?>">
                <i class="fas fa-cash-register"></i>
                <span>Ventas</span>
            </a>
        </li>
        
        <li class="nav-item">
            <a href="./clientes" class="nav-link <?= $currentPage === 'clientes' ? 'active' : '' ?>">
                <i class="fas fa-users"></i>
                <span>Clientes</span>
            </a>
        </li>
        
        <li class="nav-item">
            <a href="./pedidos" class="nav-link <?= $currentPage === 'pedidos' ? 'active' : '' ?>">
                <i class="fas fa-shopping-cart"></i>
                <span>Pedidos</span>
            </a>
        </li>
        
        <li class="nav-item">
            <a href="./sugerencias" class="nav-link <?= $currentPage === 'sugerencias' ? 'active' : '' ?>">
                <i class="fas fa-lightbulb"></i>
                <span>Sugerencias de Compra</span>
             </a>
        </li>
        
        <li class="nav-item">
            <a href="./usuarios/perfil" class="nav-link <?= $currentPage === 'usuarios' ? 'active' : '' ?>">
                <i class="fas fa-user-circle"></i>
                <span>Mi Perfil</span>
            </a>
        </li>
        
        <?php if ($_SESSION['user_rol'] === 'administrador'): ?>
        <li class="nav-item">
            <a href="./auditorias" class="nav-link <?= $currentPage === 'auditorias' ? 'active' : '' ?>">
                <i class="fas fa-history"></i>
                <span>Auditorías</span>
            </a>
        <li class="nav-item">
            <a href="./usuarios" class="nav-link <?= $currentPage === 'usuarios' ? 'active' : '' ?>">
                <i class="fas fa-users-cog"></i>
                <span>Usuarios</span>
            </a>
        </li>
        
        <li class="nav-item">
            <a href="./sucursales" class="nav-link <?= $currentPage === 'sucursales' ? 'active' : '' ?>">
                <i class="fas fa-store"></i>
                <span>Sucursales</span>
            </a>
        </li>
        
                <span>Auditorías</span>
            </a>
        </li>
        
        <li class="nav-item">
            <a href="./respaldos" class="nav-link <?= $currentPage === 'respaldos' ? 'active' : '' ?>">
                <i class="fas fa-database"></i>
                <span>Respaldos</span>
            </a>
        </li>
        <?php endif; ?>
    </ul>
</aside>

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
