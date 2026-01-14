<?php
$pageTitle = 'Compras y Abastecimiento';
$currentPage = 'compras';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="container-fluid p-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="page-title display-6 mb-1">Compras y Abastecimiento</h2>
            <p class="text-muted m-0">Registro y control de gastos</p>
        </div>
        <a href="/compras/create" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i> Nueva Compra
        </a>
    </div>

    <!-- Grid -->
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div id="grid-compras"></div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const rawData = <?= json_encode($compras ?? []) ?>;

    const gridData = rawData.map(c => [
        c.fecha_compra,
        c.proveedor_nombre || 'Sin Proveedor',
        c.numero_comprobante,
        c.usuario_nombre,
        parseFloat(c.total).toFixed(2),
        c.estado,
        c.id // Action ID
    ]);

    initSipanGrid({
        element: document.getElementById('grid-compras'),
        data: gridData,
        columns: [
            { 
                name: 'Fecha',
                formatter: (cell) => new Date(cell).toLocaleDateString('es-PE', { 
                    day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' 
                })
            },
            { name: 'Proveedor' },
            { name: 'Comprobante' },
            { name: 'Registrado por' },
            { 
                name: 'Total ($)',
                formatter: (cell) => gridjs.html(`<span class="fw-bold text-success">$ ${cell}</span>`)
            },
            { 
                name: 'Estado',
                formatter: (cell) => gridjs.html(`<span class="badge bg-success">${cell.charAt(0).toUpperCase() + cell.slice(1)}</span>`)
            },
            { 
                name: 'Acciones',
                sort: false,
                formatter: (cell) => gridjs.html(`
                    <div class="d-flex gap-1 justify-content-center">
                        <a href="/compras/show/${cell}" class="grid-btn-action grid-btn-view" title="Ver Detalle"><i class="fas fa-eye"></i></a>
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
