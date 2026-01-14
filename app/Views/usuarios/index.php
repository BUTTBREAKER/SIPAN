<?php
$pageTitle = 'Usuarios';
$currentPage = 'usuarios';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="container-fluid p-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="page-title display-6 mb-1">Gestión de Usuarios</h2>
            <p class="text-muted m-0">Usuarios de la sucursal actual</p>
        </div>
        <a href="/register" class="btn btn-primary">
            <i class="fas fa-user-plus me-2"></i> Nuevo Usuario
        </a>
    </div>

    <!-- Grid -->
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div id="grid-usuarios"></div>
        </div>
    </div>
</div>

<style>
.user-avatar-small {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: linear-gradient(135deg, #D4A574, #8B6F47);
    color: white;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    font-weight: bold;
    text-transform: uppercase;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const rawData = <?= json_encode($usuarios ?? []) ?>;
    const sessionUserId = <?= json_encode($_SESSION['user_id'] ?? null) ?>;

    const gridData = rawData.map(u => [
        u.id, 
        u.nombre,
        u.correo,
        u.rol,
        u.telefono || '-',
        u.estado,
        u.id // Action ID
    ]);

    initSipanGrid({
        element: document.getElementById('grid-usuarios'),
        data: gridData,
        columns: [
            { 
                name: 'ID', 
                formatter: (cell) => gridjs.html(`<span class="font-monospace text-muted">#${cell}</span>`)
            },
            { 
                name: 'Usuario',
                formatter: (cell, row) => {
                    const name = cell;
                    const initials = name.substring(0, 2);
                    return gridjs.html(`
                        <div class="d-flex align-items-center">
                            <div class="user-avatar-small me-2">${initials}</div>
                            <span class="fw-bold">${name}</span>
                        </div>
                    `);
                }
            },
            { name: 'Correo' },
            { 
                name: 'Rol',
                formatter: (cell) => {
                    const roles = {
                        'administrador': 'danger',
                        'cajero': 'primary',
                        'empleado': 'secondary'
                    };
                    const color = roles[cell] || 'secondary';
                    return gridjs.html(`<span class="badge bg-${color}">${cell.charAt(0).toUpperCase() + cell.slice(1)}</span>`);
                }
            },
            { name: 'Teléfono' },
            { 
                name: 'Estado',
                formatter: (cell) => {
                    const color = cell === 'activo' ? 'success' : 'danger';
                    return gridjs.html(`<span class="badge bg-${color}">${cell.charAt(0).toUpperCase() + cell.slice(1)}</span>`);
                }
            },
            { 
                name: 'Acciones',
                sort: false,
                formatter: (cell, row) => {
                    const estado = row.cells[5].data; // Accessing 'estado' cell
                    const isSelf = String(cell) === String(sessionUserId);
                    
                    let actions = `<div class="d-flex gap-1 justify-content-center">`;
                    actions += `<a href="/usuarios/actividad?usuario_id=${cell}" class="grid-btn-action grid-btn-view" title="Actividad"><i class="fas fa-history"></i></a>`;
                    
                    if (!isSelf) {
                        const toggleIcon = estado === 'activo' ? 'ban' : 'check';
                        const toggleClass = estado === 'activo' ? 'btn-warning' : 'btn-success';
                        const toggleTitle = estado === 'activo' ? 'Desactivar' : 'Activar';
                        const toggleColor = estado === 'activo' ? 'orange' : 'green'; 
                        
                        // We use a button with inline style overrides or just reuse existing classes but customized:
                        // Since grid-btn-action has fixed colors, we might want manual style for this toggle button
                        // Or just use 'grid-btn-edit' as base and override icon.
                        actions += `<button onclick="cambiarEstado(${cell}, '${estado === 'activo' ? 'inactivo' : 'activo'}')" 
                                    class="grid-btn-action" 
                                    style="background-color: ${toggleColor === 'orange' ? '#ffc107' : '#28a745'}; color: white;" 
                                    title="${toggleTitle}"><i class="fas fa-${toggleIcon}"></i></button>`;
                    }
                    actions += `</div>`;
                    return gridjs.html(actions);
                }
            }
        ],
        search: true,
        pagination: true,
        sort: true
    });
});

async function cambiarEstado(usuarioId, nuevoEstado) {
    const result = await Swal.fire({
        title: '¿Estás seguro?',
        text: `¿Deseas ${nuevoEstado === 'activo' ? 'activar' : 'desactivar'} este usuario?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#D4A574',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, continuar',
        cancelButtonText: 'Cancelar'
    });
    
    if (result.isConfirmed) {
        try {
            const response = await fetch('/usuarios/cambiar-estado', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({
                    usuario_id: usuarioId,
                    estado: nuevoEstado
                })
            });
            
            const data = await response.json();
            
            if (data.success) {
                Swal.fire('Éxito', data.message, 'success').then(() => {
                    location.reload();
                });
            } else {
                Swal.fire('Error', data.message, 'error');
            }
        } catch (error) {
            Swal.fire('Error', 'Error al cambiar el estado del usuario', 'error');
        }
    }
}
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
