<?php
$pageTitle = 'Pedidos';
$currentPage = 'pedidos';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="container-fluid p-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="page-title display-6 mb-1">Pedidos</h2>
            <p class="text-muted m-0">Gestión de pedidos de clientes</p>
        </div>
        <a href="/pedidos/create" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i> Nuevo Pedido
        </a>
    </div>

    <!-- Grid -->
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div id="grid-pedidos"></div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const rawData = <?= json_encode($pedidos ?? []) ?>;

    const gridData = rawData.map(p => [
        p.id,
        p.id, // Display ID
        p.fecha_pedido,
        p.cliente_nombre || 'Cliente #' + p.id_cliente,
        parseFloat(p.total).toFixed(2),
        p.estado_pedido,
        p.estado_pago,
        p.fecha_entrega,
        p.id // Action ID
    ]);

    initSipanGrid({
        element: document.getElementById('grid-pedidos'),
        data: gridData,
        columns: [
            { id: 'id', hidden: true },
            { 
                name: 'Pedido',
                formatter: (cell) => gridjs.html(`<span class="fw-bold font-monospace">#${String(cell).padStart(6, '0')}</span>`)
            },
            { 
                name: 'Fecha',
                formatter: (cell) => new Date(cell).toLocaleDateString('es-PE', { day: '2-digit', month: '2-digit', year: 'numeric' })
            },
            { name: 'Cliente' },
            { 
                name: 'Total ($)',
                formatter: (cell) => gridjs.html(`<span class="fw-bold text-success">$ ${cell}</span>`)
            },
            { 
                name: 'Estado',
                formatter: (cell) => {
                    const statusColors = {
                        'pendiente': 'warning',
                        'en_proceso': 'info',
                        'completado': 'success',
                        'cancelado': 'danger'
                    };
                    const color = statusColors[cell] || 'secondary';
                    const text = cell.replace('_', ' ').replace(/\b\w/g, c => c.toUpperCase());
                    return gridjs.html(`<span class="badge bg-${color}">${text}</span>`);
                }
            },
            { 
                name: 'Pago',
                formatter: (cell) => {
                    const payColors = {
                        'pendiente': 'danger',
                        'abonado': 'warning',
                        'pagado': 'success'
                    };
                    const color = payColors[cell] || 'secondary';
                    const text = cell.charAt(0).toUpperCase() + cell.slice(1);
                    return gridjs.html(`<span class="badge bg-${color}">${text}</span>`);
                }
            },
            { 
                name: 'Entrega',
                formatter: (cell) => new Date(cell).toLocaleDateString('es-PE', { day: '2-digit', month: '2-digit', year: 'numeric' })
            },
            { 
                name: 'Acciones',
                sort: false,
                formatter: (cell) => gridjs.html(`
                    <div class="d-flex gap-1 justify-content-center">
                        <a href="/pedidos/show/${cell}" class="grid-btn-action grid-btn-view" title="Ver detalle"><i class="fas fa-eye"></i></a>
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