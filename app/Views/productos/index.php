<?php
$pageTitle = 'Productos';
$currentPage = 'productos';
require_once __DIR__ . '/../layouts/header.php';

// Prepare data for Alpine
$productosJson = json_encode($productos ?? []);
$userRol = $_SESSION['user_rol'] ?? '';
?>

<div class="container-fluid p-4">
    <!-- Header Actions -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="page-title display-6 mb-1">Productos</h2>
            <p class="text-muted m-0">Catálogo general e inventario</p>
        </div>
        <a href="/productos/create" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i> Nuevo Producto
        </a>
    </div>

    <!-- Grid.js Container -->
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div id="grid-productos"></div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // Data from PHP
    const rawData = <?= json_encode($productos ?? []) ?>;
    const userRol = '<?= $_SESSION['user_rol'] ?? '' ?>';

    // Map data for Grid.js
    const gridData = rawData.map(p => [
        p.id, // Hidden ID
        p.nombre,
        p.descripcion || '',
        parseFloat(p.precio_actual).toFixed(2),
        parseInt(p.stock_actual),
        p.id // Action ID
    ]);

    initSipanGrid({
        element: document.getElementById('grid-productos'),
        data: gridData,
        columns: [
            { id: 'id', name: 'ID', hidden: true },
            { name: 'Producto' },
            { name: 'Descripción' },
            { 
                name: 'Precio ($)',
                formatter: (cell) => gridjs.html(`<span class="fw-bold text-success">$ ${cell}</span>`)
            },
            { 
                name: 'Stock',
                formatter: (cell) => {
                    const color = cell < 10 ? 'danger' : 'success'; // Example threshold
                    return gridjs.html(`<span class="badge bg-${color}">${cell} un.</span>`);
                }
            },
            { 
                name: 'Acciones',
                sort: false,
                formatter: (cell) => {
                    let actions = `<a href="/productos/edit/${cell}" class="grid-btn-action grid-btn-edit" title="Editar"><i class="fas fa-pencil-alt"></i></a>`;
                    
                    if (userRol === 'administrador') {
                        actions += `<button onclick="eliminarProducto(${cell})" class="grid-btn-action grid-btn-delete" title="Eliminar"><i class="fas fa-trash-alt"></i></button>`;
                    }
                    return gridjs.html(actions);
                }
            }
        ],
        search: true,
        pagination: true,
        sort: true
    });
});

// Global function for actions (outside module scope)
async function eliminarProducto(id) {
    const result = await Swal.fire({
        title: '¿Eliminar producto?',
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
            const response = await fetch(`/productos/delete/${id}`, { method: 'POST' });
            const data = await response.json();
            
            if (data.success) {
                Swal.fire('Eliminado', data.message, 'success').then(() => {
                    location.reload(); // Simple reload for now to refresh grid
                });
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

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
