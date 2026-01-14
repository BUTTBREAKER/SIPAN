<?php
$pageTitle = 'Producciones';
$currentPage = 'producciones';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="container-fluid p-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="page-title display-6 mb-1">Producciones</h2>
            <p class="text-muted m-0">Historial de lotes producidos</p>
        </div>
        <a href="/producciones/create" class="btn btn-primary">
            <i class="fas fa-industry me-2"></i> Nueva Producción
        </a>
    </div>

    <!-- Grid -->
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div id="grid-producciones"></div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const rawData = <?= json_encode($producciones ?? []) ?>;

    const gridData = rawData.map(p => [
        p.id,
        p.id, // Display ID
        p.fecha_produccion,
        p.producto_nombre || 'Producto #' + p.id_producto,
        p.cantidad_producida + ' un.',
        parseFloat(p.costo_total).toFixed(2),
        p.usuario_nombre || '-',
        p.id // Action
    ]);

    initSipanGrid({
        element: document.getElementById('grid-producciones'),
        data: gridData,
        columns: [
            { id: 'id', hidden: true },
            { 
                name: 'Lote',
                formatter: (cell) => gridjs.html(`<span class="fw-bold font-monospace">#${String(cell).padStart(6, '0')}</span>`)
            },
            { 
                name: 'Fecha',
                formatter: (cell) => new Date(cell).toLocaleDateString('es-PE', { 
                    day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' 
                })
            },
            { name: 'Producto' },
            { 
                name: 'Cantidad',
                formatter: (cell) => gridjs.html(`<span class="badge bg-light text-dark border">${cell}</span>`)
            },
            { 
                name: 'Costo Total',
                formatter: (cell) => gridjs.html(`<span class="fw-bold text-danger">$ ${cell}</span>`)
            },
            { name: 'Encargado' },
            { 
                name: 'Acciones',
                sort: false,
                formatter: (cell) => gridjs.html(`
                    <div class="d-flex gap-1 justify-content-center">
                        <a href="/producciones/show/${cell}" class="grid-btn-action grid-btn-view" title="Ver Detalle"><i class="fas fa-eye"></i></a>
                    </div>
                `)
            }
        ],
        search: true,
        pagination: true,
        sort: true
    });
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
