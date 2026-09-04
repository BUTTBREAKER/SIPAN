<?php
$pageTitle = 'Estadísticas de Auditoría';
$currentPage = 'auditorias';
require_once __DIR__ . '/../layouts/header.php';

// Solo administradores
if (($_SESSION['user_rol'] ?? '') !== 'administrador') {
    header('Location: /dashboard');
    exit;
}

if (!function_exists('traducirAccion')) {
    function traducirAccion($accion)
    {
        $traducciones = [
            'INSERT' => 'Creó',
            'UPDATE' => 'Actualizó',
            'DELETE' => 'Eliminó',
            'UNDO'   => 'Deshizo'
        ];
        return $traducciones[strtoupper($accion)] ?? ucfirst(strtolower($accion));
    }
}

if (!function_exists('traducirTabla')) {
    function traducirTabla($tabla)
    {
        $traducciones = [
            'ventas'             => 'Venta',
            'productos'          => 'Producto',
            'insumos'            => 'Insumo',
            'usuarios'           => 'Usuario',
            'clientes'           => 'Cliente',
            'proveedores'        => 'Proveedor',
            'compras'            => 'Compra',
            'pedidos'            => 'Pedido',
            'producciones'       => 'Producción',
            'recetas'            => 'Receta',
            'sugerencias_compra' => 'Sugerencia de Compra',
            'auditoria'          => 'Auditoría'
        ];
        return $traducciones[$tabla] ?? ucfirst($tabla);
    }
}

// Calcular resúmenes
$totalOperaciones = 0;
$totalInserts = 0;
$totalUpdates = 0;
$totalDeletes = 0;

if (!empty($estadisticas)) {
    foreach ($estadisticas as $item) {
        $cant = (int) ($item['total'] ?? 0);
        $totalOperaciones += $cant;
        $accion = strtoupper($item['accion'] ?? '');
        if ($accion === 'INSERT') {
            $totalInserts += $cant;
        } elseif ($accion === 'UPDATE') {
            $totalUpdates += $cant;
        } elseif ($accion === 'DELETE') {
            $totalDeletes += $cant;
        }
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="page-title mb-1"><i class="fas fa-chart-pie me-2"></i>Estadísticas de Auditoría</h2>
        <p class="text-muted mb-0">Resumen y métricas de actividades registradas en el sistema</p>
    </div>
    <div>
        <a href="/auditorias" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Volver a Auditorías
        </a>
    </div>
</div>

<!-- Tarjetas de métricas -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card shadow-sm border-0 border-start border-primary border-4 h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small text-uppercase fw-bold">Total Operaciones</div>
                        <div class="fs-4 fw-bold text-primary mt-1"><?= number_format($totalOperaciones) ?></div>
                    </div>
                    <div class="fs-2 text-primary opacity-50">
                        <i class="fas fa-history"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card shadow-sm border-0 border-start border-success border-4 h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small text-uppercase fw-bold">Creaciones</div>
                        <div class="fs-4 fw-bold text-success mt-1"><?= number_format($totalInserts) ?></div>
                    </div>
                    <div class="fs-2 text-success opacity-50">
                        <i class="fas fa-plus-circle"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card shadow-sm border-0 border-start border-warning border-4 h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small text-uppercase fw-bold">Actualizaciones</div>
                        <div class="fs-4 fw-bold text-warning mt-1"><?= number_format($totalUpdates) ?></div>
                    </div>
                    <div class="fs-2 text-warning opacity-50">
                        <i class="fas fa-edit"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card shadow-sm border-0 border-start border-danger border-4 h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small text-uppercase fw-bold">Eliminaciones</div>
                        <div class="fs-4 fw-bold text-danger mt-1"><?= number_format($totalDeletes) ?></div>
                    </div>
                    <div class="fs-2 text-danger opacity-50">
                        <i class="fas fa-trash-alt"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tabla de detalle -->
<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3">
        <h5 class="card-title mb-0">
            <i class="fas fa-table me-2 text-primary"></i>Desglose por Fecha, Módulo y Acción
        </h5>
    </div>
    <div class="card-body p-0">
        <?php if (empty($estadisticas)) : ?>
            <div class="text-center py-5">
                <i class="fas fa-info-circle fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No hay registros de estadísticas disponibles</h5>
                <p class="text-muted small">Las acciones registradas aparecerán aquí automáticamente.</p>
            </div>
        <?php else : ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Fecha</th>
                            <th>Módulo / Tabla</th>
                            <th>Acción</th>
                            <th class="text-end">Total de Eventos</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($estadisticas as $fila) : ?>
                            <?php
                            $accionUpper = strtoupper($fila['accion'] ?? '');
                            $badgeClass = match ($accionUpper) {
                                'INSERT' => 'bg-success',
                                'UPDATE' => 'bg-warning text-dark',
                                'DELETE' => 'bg-danger',
                                'UNDO'   => 'bg-info text-dark',
                                default  => 'bg-secondary'
                            };
    ?>
                            <tr>
                                <td>
                                    <i class="far fa-calendar me-1 text-muted"></i>
                                    <?= !empty($fila['fecha']) ? date('d/m/Y', strtotime($fila['fecha'])) : 'N/A' ?>
                                </td>
                                <td>
                                    <span class="fw-semibold">
                                        <?= htmlspecialchars(traducirTabla($fila['tabla'] ?? '')) ?>
                                    </span>
                                    <small class="text-muted d-block">
                                        <?= htmlspecialchars($fila['tabla'] ?? '') ?>
                                    </small>
                                </td>
                                <td>
                                    <span class="badge <?= $badgeClass ?>">
                                        <?= htmlspecialchars(traducirAccion($accionUpper)) ?>
                                    </span>
                                </td>
                                <td class="text-end fw-bold">
                                    <?= number_format((int) ($fila['total'] ?? 0)) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
