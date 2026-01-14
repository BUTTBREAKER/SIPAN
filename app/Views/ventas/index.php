<?php
$pageTitle = 'Ventas';
$currentPage = 'ventas';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="container-fluid p-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="page-title display-6 mb-1">Ventas Realizadas</h2>
            <p class="text-muted m-0">Historial de transacciones y facturación</p>
        </div>
        <a href="/ventas/create" class="btn btn-primary">
            <i class="fas fa-cash-register me-2"></i> Nueva Venta
        </a>
    </div>

    <!-- Grid -->
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div id="grid-ventas"></div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const rawData = <?= json_encode($ventas ?? []) ?>;
    
    // Método badge helper
    const getPaymentBadge = (method) => {
        const badges = {
            'efectivo_usd': '<span class="badge bg-success">Efectivo ($)</span>',
            'efectivo_bs': '<span class="badge bg-success bg-opacity-75">Efectivo (Bs)</span>',
            'tarjeta': '<span class="badge bg-info text-dark">Punto</span>',
            'punto_usd': '<span class="badge bg-info text-dark">Punto ($)</span>',
            'transferencia': '<span class="badge bg-primary">Transferencia</span>',
            'pago_movil': '<span class="badge bg-primary">Pago Móvil</span>',
            'biopago': '<span class="badge bg-secondary">Biopago</span>',
            'zelle': '<span class="badge" style="background-color: #5821c9;">Zelle</span>',
            'mixto': '<span class="badge bg-dark">Mixto</span>'
        };
        // Fallback for legacy
        if (method === 'efectivo') return '<span class="badge bg-success">Efectivo</span>';
        
        return badges[method] || `<span class="badge bg-secondary">${method}</span>`;
    };

    const gridData = rawData.map(v => [
        v.id || 0,
        v.id || 0,
        v.fecha_venta || new Date().toISOString(),
        (v.cliente_nombre && v.cliente_nombre.trim()) ? v.cliente_nombre : 'Cliente General',
        parseFloat(v.total || 0).toFixed(2),
        v.metodo_pago || 'efectivo_usd',
        v.usuario_nombre || 'Sistema',
        v.id || 0
    ]);

    initSipanGrid({
        element: document.getElementById('grid-ventas'),
        data: gridData,
        columns: [
            { id: 'id', name: 'ID', hidden: true },
            { 
                name: 'Ticket',
                formatter: (cell) => gridjs.html(`<span class="fw-bold font-monospace">#${String(cell).padStart(6, '0')}</span>`)
            },
            { 
                name: 'Fecha',
                formatter: (cell) => new Date(cell).toLocaleDateString('es-PE', { 
                    day: '2-digit', month: '2-digit', year: 'numeric', 
                    hour: '2-digit', minute: '2-digit' 
                })
            },
            { name: 'Cliente' },
            { 
                name: 'Total ($)',
                formatter: (cell) => gridjs.html(`<span class="fw-bold text-success">$ ${cell}</span>`)
            },
            { 
                name: 'Pago',
                formatter: (cell) => gridjs.html(getPaymentBadge(cell))
            },
            { name: 'Vendedor' },
            { 
                name: 'Acciones',
                sort: false,
                formatter: (cell) => gridjs.html(`
                    <div class="d-flex gap-1 justify-content-center">
                        <a href="/ventas/show/${cell}" class="grid-btn-action grid-btn-view" title="Ver detalle"><i class="fas fa-eye"></i></a>
                        <a href="/ventas/ticket/${cell}" class="grid-btn-action bg-secondary text-white" title="Imprimir" target="_blank"><i class="fas fa-print"></i></a>
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
