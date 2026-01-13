<?php 
$pageTitle = 'Clientes';
$currentPage = 'clientes';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="container-fluid p-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="page-title display-6 mb-1">Clientes</h2>
            <p class="text-muted m-0">Gestión de cartera de clientes</p>
        </div>
        <a href="/clientes/create" class="btn btn-primary">
            <i class="fas fa-user-plus me-2"></i> Nuevo Cliente
        </a>
    </div>

    <!-- Grid -->
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div id="grid-clientes"></div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const rawData = <?= json_encode($clientes ?? []) ?>;
    const userRol = '<?= $_SESSION['user_rol'] ?? '' ?>';

    const gridData = rawData.map(c => [
        c.id,
        `${c.nombre} ${c.apellido}`,
        c.documento_tipo ? `${c.documento_tipo}: ${c.documento_numero}` : '-',
        c.telefono || '-',
        c.correo || '-',
        c.estado,
        c.id // Action ID
    ]);

    initSipanGrid({
        element: document.getElementById('grid-clientes'),
        data: gridData,
        columns: [
            { id: 'id', name: 'ID', width: '80px' },
            { 
                name: 'Cliente',
                formatter: (cell) => gridjs.html(`<div class="fw-bold">${cell}</div>`)
            },
            { name: 'Documento' },
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
                formatter: (cell) => {
                    let actions = `<div class="d-flex gap-1 justify-content-center">`;
                    actions += `<a href="/clientes/show/${cell}" class="grid-btn-action grid-btn-view" title="Ver"><i class="fas fa-eye"></i></a>`;
                    actions += `<a href="/clientes/edit/${cell}" class="grid-btn-action grid-btn-edit" title="Editar"><i class="fas fa-edit"></i></a>`;
                    
                    if (userRol === 'administrador') {
                        actions += `<button onclick="eliminarCliente(${cell})" class="grid-btn-action grid-btn-delete" title="Eliminar"><i class="fas fa-trash-alt"></i></button>`;
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

async function eliminarCliente(id) {
    const result = await Swal.fire({
        title: '¿Eliminar cliente?',
        text: "Esta acción no se puede deshacer",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    });

    if (result.isConfirmed) {
        try {
            const response = await fetch(`/clientes/delete/${id}`, { method: 'POST' });
            const data = await response.json();
            
            if (data.success) {
                Swal.fire('Eliminado', data.message, 'success').then(() => location.reload());
            } else {
                Swal.fire('Error', data.message, 'error');
            }
        } catch (error) {
            Swal.fire('Error', 'No se pudo conectar con el servidor', 'error');
        }
    }
}
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
