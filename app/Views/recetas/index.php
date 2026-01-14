<?php
$pageTitle = 'Recetas';
$currentPage = 'recetas';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="container-fluid p-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="page-title display-6 mb-1">Recetas</h2>
            <p class="text-muted m-0">Fórmulas maestras de producción</p>
        </div>
        <a href="/recetas/create" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i> Nueva Receta
        </a>
    </div>

    <!-- Grid -->
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div id="grid-recetas"></div>
        </div>
    </div>
</div>

<?php
// Prepare data including Ingredient Count which was previously queried in-view
// Ideally this should be in Controller, but doing it here to minimize impact
require_once __DIR__ . '/../../Models/Receta.php';
$recetaModel = new \App\Models\Receta();

$recetasData = array_map(function ($r) use ($recetaModel) {
    // We fetch insumos count per row
    $insumos = $recetaModel->getInsumos($r['id']);
    $r['insumos_count'] = count($insumos);
    return $r;
}, $recetas ?? []);
?>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const rawData = <?= json_encode($recetasData) ?>;
    const userRol = '<?= $_SESSION['user_rol'] ?? '' ?>';

    const gridData = rawData.map(r => [
        r.id, // Hidden
        r.producto_nombre || 'Producto #' + r.id_producto,
        r.rendimiento + ' unidades',
        r.insumos_count + ' insumo(s)',
        r.id // Action
    ]);

    initSipanGrid({
        element: document.getElementById('grid-recetas'),
        data: gridData,
        columns: [
            { id: 'id', hidden: true },
            { 
                name: 'Producto',
                formatter: (cell) => gridjs.html(`<div class="fw-bold">${cell}</div>`)
            },
            { name: 'Rendimiento' },
            { 
                name: 'Composición',
                formatter: (cell) => gridjs.html(`<span class="badge bg-light text-dark border"><i class="fas fa-cubes me-1"></i>${cell}</span>`)
            },
            { 
                name: 'Acciones',
                sort: false,
                formatter: (cell) => {
                    let actions = `<div class="d-flex gap-1 justify-content-center">`;
                    actions += `<a href="/recetas/show/${cell}" class="grid-btn-action grid-btn-view" title="Ver Detalles"><i class="fas fa-calculator"></i></a>`;
                    actions += `<a href="/recetas/edit/${cell}" class="grid-btn-action grid-btn-edit" title="Editar"><i class="fas fa-edit"></i></a>`;
                    
                    if (userRol === 'administrador') {
                        actions += `<button onclick="eliminarReceta(${cell})" class="grid-btn-action grid-btn-delete" title="Eliminar"><i class="fas fa-trash-alt"></i></button>`;
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

async function eliminarReceta(id) {
    const result = await Swal.fire({
        title: '¿Eliminar receta?',
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
            const response = await fetch(`/recetas/delete/${id}`, { method: 'POST' });
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
