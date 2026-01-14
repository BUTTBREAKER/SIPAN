<?php
$pageTitle = 'Proveedores';
$currentPage = 'proveedores';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="container-fluid p-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="page-title display-6 mb-1">Proveedores</h2>
            <p class="text-muted m-0">Administración de proveedores del negocio</p>
        </div>
        <a href="/proveedores/create" class="btn btn-primary">
            <i class="fas fa-truck me-2"></i> Nuevo Proveedor
        </a>
    </div>

    <!-- Grid -->
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div id="grid-proveedores"></div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const rawData = <?= json_encode($proveedores ?? []) ?>;
    
    // For delete action, we check session here inside render if needed, or just allow all logged in users?
    // Original code allowed delete form blindly, checking server side.
    
    const gridData = rawData.map(p => [
        p.id, // Hidden
        p.nombre,
        p.rif || '—',
        p.telefono || '—',
        p.correo || '—',
        p.estado,
        p.id // Action
    ]);

    initSipanGrid({
        element: document.getElementById('grid-proveedores'),
        data: gridData,
        columns: [
            { id: 'id', hidden: true },
            { 
                name: 'Nombre',
                formatter: (cell) => gridjs.html(`<div class="fw-bold">${cell}</div>`)
            },
            { name: 'RIF/RUC' },
            { name: 'Teléfono' },
            { name: 'Correo' },
            { 
                name: 'Estado',
                formatter: (cell) => {
                    const color = cell === 'activo' ? 'success' : 'danger';
                    const text = cell === 'activo' ? 'Activo' : 'Inactivo';
                    return gridjs.html(`<span class="badge bg-${color}">${text}</span>`);
                }
            },
            { 
                name: 'Acciones',
                sort: false,
                formatter: (cell) => gridjs.html(`
                    <div class="d-flex gap-1 justify-content-center">
                        <a href="/proveedores/show/${cell}" class="grid-btn-action grid-btn-view" title="Ver"><i class="fas fa-eye"></i></a>
                        <a href="/proveedores/edit/${cell}" class="grid-btn-action grid-btn-edit" title="Editar"><i class="fas fa-edit"></i></a>
                        <button onclick="eliminarProveedor(${cell})" class="grid-btn-action grid-btn-delete" title="Eliminar"><i class="fas fa-trash-alt"></i></button>
                    </div>
                `)
            }
        ],
        search: true,
        pagination: true,
        sort: true
    });
});

async function eliminarProveedor(id) {
    const result = await Swal.fire({
        title: '¿Eliminar proveedor?',
        text: "Esta acción no se puede deshacer",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    });

    if (result.isConfirmed) {
        // Create form submission programmatically to match original behavior or use AJAX
        // Original was FORM POST. Let's use AJAX for consistency with other modules.
        try {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/proveedores/delete/${id}`;
            document.body.appendChild(form);
            form.submit();
        } catch (e) {
            Swal.fire('Error', 'No se pudo eliminar', 'error');
        }
    }
}
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
