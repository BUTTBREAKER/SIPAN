<?php
$pageTitle = 'Insumos';
$currentPage = 'insumos';
require_once __DIR__ . '/../layouts/header.php';

// Data for Alpine
$insumosJson = json_encode($insumos ?? []);
$userRol = $_SESSION['user_rol'] ?? '';
?>

<div class="container-fluid p-4">
    <!-- Header Actions -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="page-title display-6 mb-1">Insumos & Materia Prima</h2>
            <p class="text-muted m-0">Control de inventario de suministros</p>
        </div>
        <a href="/insumos/create" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i> Nuevo Insumo
        </a>
    </div>

    <!-- Grid.js Container -->
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div id="grid-insumos"></div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // Data passed from Controller
    const rawData = <?= json_encode($insumos ?? []) ?>;
    const userRol = '<?= $_SESSION['user_rol'] ?? '' ?>';

    const gridData = rawData.map(i => [
        i.id,
        i.nombre,
        i.proveedor_nombre || 'No asignado',
        parseFloat(i.stock_actual) + ' ' + i.unidad_medida,
        parseFloat(i.precio_unitario).toFixed(2),
        i.id // Action ID
    ]);

    initSipanGrid({
        element: document.getElementById('grid-insumos'),
        data: gridData,
        columns: [
            { id: 'id', name: 'ID', hidden: true },
            { 
                name: 'Insumo',
                formatter: (cell) => gridjs.html(`<div class="fw-bold text-dark">${cell}</div>`)
            },
            { 
                name: 'Proveedor',
                formatter: (cell) => {
                    return cell === 'No asignado' 
                        ? gridjs.html(`<span class="badge bg-light text-muted border fw-normal">Sin asignar</span>`)
                        : gridjs.html(`<span class="badge bg-white text-dark border fw-normal"><i class="fas fa-truck me-1 text-muted"></i>${cell}</span>`);
                }
            },
            { 
                name: 'Stock Actual',
                formatter: (cell) => {
                    // Primitive check: if numeric part is low. 
                    const val = parseFloat(cell);
                    const color = val < 5 ? 'danger' : 'success'; // You might want dynamic MinStock here if available in data
                    return gridjs.html(`<span class="badge bg-${color}">${cell}</span>`);
                }
            },
            { 
                name: 'Precio ($)',
                formatter: (cell) => gridjs.html(`<span class="fw-bold text-muted">$ ${cell}</span>`)
            },
            { 
                name: 'Acciones',
                sort: false,
                formatter: (cell) => {
                    let actions = `<a href="/insumos/edit/${cell}" class="grid-btn-action grid-btn-edit" title="Editar"><i class="fas fa-pencil-alt"></i></a>`;
                    
                    if (userRol === 'administrador') {
                        actions += `<button onclick="eliminarInsumo(${cell})" class="grid-btn-action grid-btn-delete" title="Eliminar"><i class="fas fa-trash-alt"></i></button>`;
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

async function eliminarInsumo(id) {
    const result = await Swal.fire({
        title: '¿Eliminar insumo?',
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
            const response = await fetch(`/insumos/delete/${id}`, { method: 'POST' });
            const data = await response.json();
            
            if (data.success) {
                Swal.fire('Eliminado', data.message, 'success').then(() => location.reload());
            } else {
                Swal.fire('Error', data.message, 'error');
            }
        } catch (error) {
            Swal.fire('Error', 'No se pudo conectar', 'error');
        }
    }
}
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
