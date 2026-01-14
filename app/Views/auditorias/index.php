<?php
$pageTitle = 'Auditorías';
$currentPage = 'auditorias';
require_once __DIR__ . '/../layouts/header.php';

// Solo administradores
if ($_SESSION['user_rol'] !== 'administrador') {
    header('Location: /dashboard');
    exit;
}

// Funciones de traducción
function traducirAccion($accion)
{
    $traducciones = [
        'INSERT' => 'Creó',
        'UPDATE' => 'Actualizó',
        'DELETE' => 'Eliminó',
        'UNDO' => 'Deshizo'
    ];
    return $traducciones[strtoupper($accion)] ?? ucfirst(strtolower($accion));
}

function traducirTabla($tabla)
{
    $traducciones = [
        'ventas' => 'Venta',
        'productos' => 'Producto',
        'insumos' => 'Insumo',
        'usuarios' => 'Usuario',
        'clientes' => 'Cliente',
        'proveedores' => 'Proveedor',
        'compras' => 'Compra',
        'pedidos' => 'Pedido',
        'producciones' => 'Producción',
        'recetas' => 'Receta',
        'sugerencias_compra' => 'Sugerencia de Compra',
        'auditoria' => 'Auditoría'
    ];
    return $traducciones[$tabla] ?? ucfirst($tabla);
}

function obtenerCambiosRelevantes($datos_anteriores, $datos_nuevos)
{
    $anterior = json_decode($datos_anteriores, true);
    $nuevo = json_decode($datos_nuevos, true);

    if (!$anterior || !$nuevo) {
        return [];
    }

    $cambios = [];
    foreach ($nuevo as $campo => $valor_nuevo) {
        if (isset($anterior[$campo]) && $anterior[$campo] != $valor_nuevo && $campo !== 'id') {
            $cambios[$campo] = [
                'anterior' => $anterior[$campo],
                'nuevo' => $valor_nuevo
            ];
        }
    }

    return $cambios;
}

function formatearCampo($campo)
{
    $traducciones = [
        'stock_actual' => 'Stock Actual',
        'stock_minimo' => 'Stock Mínimo',
        'precio_actual' => 'Precio',
        'precio_unitario' => 'Precio Unitario',
        'nombre' => 'Nombre',
        'descripcion' => 'Descripción',
        'total' => 'Total',
        'metodo_pago' => 'Método de Pago',
        'estado' => 'Estado',
        'cantidad' => 'Cantidad',
        'correo' => 'Correo',
        'telefono' => 'Teléfono',
        'direccion' => 'Dirección',
        'primer_nombre' => 'Primer Nombre',
        'apellido_paterno' => 'Apellido Paterno'
    ];

    return $traducciones[$campo] ?? ucwords(str_replace('_', ' ', $campo));
}
?>

<div class="page-header">
    <h2 class="page-title"><i class="fas fa-shield-alt"></i> Auditorías del Sistema</h2>
    <p class="page-subtitle">Registro completo de cambios con capacidad de deshacer acciones</p>
</div>

<!-- Filtros mejorados -->
<div class="card mb-4 shadow-sm">
    <div class="card-header bg-light">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-filter"></i> Filtros de Búsqueda</h5>
            <button onclick="toggleFiltros()" class="btn btn-sm btn-outline-secondary" id="btnToggleFiltros">
                <i class="fas fa-chevron-up"></i>
            </button>
        </div>
    </div>
    <div class="card-body" id="panelFiltros">
        <div class="row g-3">
            <div class="col-md-3">
                <label class="form-label"><i class="fas fa-table"></i> Tabla</label>
                <select id="filtroTabla" class="form-select">
                    <option value="">Todas las tablas</option>
                    <option value="productos">Productos</option>
                    <option value="insumos">Insumos</option>
                    <option value="ventas">Ventas</option>
                    <option value="clientes">Clientes</option>
                    <option value="pedidos">Pedidos</option>
                    <option value="producciones">Producciones</option>
                    <option value="usuarios">Usuarios</option>
                    <option value="recetas">Recetas</option>
                    <option value="compras">Compras</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label"><i class="fas fa-bolt"></i> Acción</label>
                <select id="filtroAccion" class="form-select">
                    <option value="">Todas las acciones</option>
                    <option value="INSERT">Creación</option>
                    <option value="UPDATE">Actualización</option>
                    <option value="DELETE">Eliminación</option>
                    <option value="UNDO">Deshacer</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label"><i class="fas fa-flag"></i> Estado</label>
                <select id="filtroEstado" class="form-select">
                    <option value="">Todos</option>
                    <option value="activo">Activos</option>
                    <option value="deshecho">Deshechos</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">&nbsp;</label>
                <div class="d-grid gap-2">
                    <button onclick="aplicarFiltros()" class="btn btn-primary">
                        <i class="fas fa-search"></i> Aplicar
                    </button>
                    <button onclick="limpiarFiltros()" class="btn btn-outline-secondary">
                        <i class="fas fa-times"></i> Limpiar
                    </button>
                </div>
            </div>
        </div>

        <div class="mt-3">
            <div class="alert alert-info mb-0">
                <i class="fas fa-info-circle"></i>
                <strong>Nota:</strong> Solo se pueden deshacer cambios realizados en las últimas 24 horas.
            </div>
        </div>
    </div>
</div>

<!-- Timeline de auditorías -->
<div class="card shadow-sm">
    <div class="card-header bg-light">
        <h5 class="mb-0"><i class="fas fa-history"></i> Historial de Cambios</h5>
    </div>
    <div class="card-body">
        <?php if (empty($auditorias)) : ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> No hay registros de auditoría disponibles.
            </div>
        <?php else : ?>
            <div class="audit-timeline" id="timelineAuditorias">
                <?php foreach ($auditorias as $auditoria) : ?>
                    <?php
                    $accion_upper = strtoupper($auditoria['accion']);
                    $es_update = $accion_upper === 'UPDATE';
                    $es_insert = $accion_upper === 'INSERT';
                    $es_delete = $accion_upper === 'DELETE';
                    $es_undo = $accion_upper === 'UNDO';
                    $fue_deshecho = $auditoria['deshacer'] == 1;
                    ?>
                    <div class="audit-item"
                        data-tabla="<?= $auditoria['tabla'] ?>"
                        data-accion="<?= $auditoria['accion'] ?>"
                        data-estado="<?= $fue_deshecho ? 'deshecho' : 'activo' ?>">

                        <div class="audit-icon <?= $es_insert ? 'icon-success' : ($es_delete ? 'icon-danger' : ($es_undo ? 'icon-secondary' : 'icon-warning')) ?>">
                            <i class="fas fa-<?= $es_insert ? 'plus-circle' : ($es_delete ? 'trash-alt' : ($es_undo ? 'undo-alt' : 'edit')) ?>"></i>
                        </div>

                        <div class="audit-card <?= $fue_deshecho ? 'audit-undone' : '' ?>">
                            <div class="audit-header">
                                <div class="audit-title">
                                    <span class="audit-action"><?= traducirAccion($auditoria['accion']) ?></span>
                                    <span class="audit-table"><?= traducirTabla($auditoria['tabla']) ?></span>
                                    <span class="audit-id">#<?= $auditoria['registro_id'] ?? 'N/A' ?></span>

                                    <?php if ($fue_deshecho) : ?>
                                        <span class="badge bg-secondary ms-2">
                                            <i class="fas fa-undo"></i> Deshecho
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <div class="audit-meta">
                                    <div class="audit-user">
                                        <i class="fas fa-user"></i>
                                        <strong><?= htmlspecialchars($auditoria['usuario_nombre'] ?? 'Sistema') ?></strong>
                                    </div>
                                    <div class="audit-date">
                                        <i class="far fa-clock"></i>
                                        <?= date('d/m/Y', strtotime($auditoria['fecha_accion'])) ?>
                                        <span class="text-muted">a las</span>
                                        <?= date('H:i', strtotime($auditoria['fecha_accion'])) ?>
                                    </div>
                                </div>
                            </div>

                            <div class="audit-content">
                                <?php if ($es_update && !empty($auditoria['datos_anteriores']) && !empty($auditoria['datos_nuevos'])) : ?>
                                    <?php $cambios = obtenerCambiosRelevantes($auditoria['datos_anteriores'], $auditoria['datos_nuevos']); ?>
                                    <?php if (!empty($cambios)) : ?>
                                        <div class="cambios-grid">
                                            <?php foreach ($cambios as $campo => $valores) : ?>
                                                <div class="cambio-item">
                                                    <div class="cambio-campo"><?= formatearCampo($campo) ?></div>
                                                    <div class="cambio-valores">
                                                        <span class="valor-anterior"><?= htmlspecialchars($valores['anterior']) ?></span>
                                                        <i class="fas fa-arrow-right mx-2"></i>
                                                        <span class="valor-nuevo"><?= htmlspecialchars($valores['nuevo']) ?></span>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php else : ?>
                                        <p class="text-muted mb-0"><i class="fas fa-info-circle"></i> Sin cambios visibles en los campos principales</p>
                                    <?php endif; ?>

                                <?php elseif ($es_insert && !empty($auditoria['datos_nuevos'])) : ?>
                                    <?php $datos = json_decode($auditoria['datos_nuevos'], true); ?>
                                    <?php if ($datos) : ?>
                                        <div class="datos-creados">
                                            <?php $count = 0; ?>
                                            <?php foreach ($datos as $campo => $valor) : ?>
                                                <?php if ($campo !== 'id' && $count < 6) : ?>
                                                    <div class="dato-item">
                                                        <strong><?= formatearCampo($campo) ?>:</strong>
                                                        <span><?= htmlspecialchars($valor) ?></span>
                                                    </div>
                                                    <?php $count++;
                                                endif; ?>
                                            <?php endforeach; ?>
                                            <?php if (count($datos) > 7) : ?>
                                                <div class="dato-item text-muted">
                                                    <small>+ <?= count($datos) - 7 ?> campos más...</small>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>

                                <?php elseif ($es_delete && !empty($auditoria['datos_anteriores'])) : ?>
                                    <?php $datos = json_decode($auditoria['datos_anteriores'], true); ?>
                                    <?php if ($datos) : ?>
                                        <div class="alert alert-danger mb-0">
                                            <strong><i class="fas fa-exclamation-triangle"></i> Registro eliminado</strong>
                                            <?php if (isset($datos['nombre'])) : ?>
                                                <p class="mb-0 mt-2">Nombre: <strong><?= htmlspecialchars($datos['nombre']) ?></strong></p>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>

                                <?php elseif ($es_undo) : ?>
                                    <div class="alert alert-secondary mb-0">
                                        <i class="fas fa-undo"></i> Acción deshecha correctamente
                                    </div>
                                <?php endif; ?>

                                <?php if ($fue_deshecho && $auditoria['fecha_deshacer']) : ?>
                                    <div class="alert alert-warning mb-0 mt-2">
                                        <small>
                                            <i class="fas fa-info-circle"></i>
                                            <strong>Deshecho el:</strong> <?= date('d/m/Y H:i', strtotime($auditoria['fecha_deshacer'])) ?>
                                        </small>
                                    </div>
                                <?php endif; ?>

                                <div class="audit-actions mt-3">
                                    <button onclick="verDetalle(<?= $auditoria['id'] ?>)"
                                        class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i> Ver Detalle Completo
                                    </button>

                                    <?php /* Funcionalidad Deshacer Deshabilitada
                                    <?php if (!$fue_deshecho && in_array($accion_upper, ['INSERT', 'UPDATE', 'DELETE'])): ?>
                                        <button onclick="verificarYDeshacer(<?= $auditoria['id'] ?>, '<?= $auditoria['accion'] ?>', '<?= traducirTabla($auditoria['tabla']) ?>')"
                                            class="btn btn-sm btn-warning">
                                            <i class="fas fa-undo"></i> Deshacer Cambio
                                        </button>
                                    <?php endif; ?>
                                    */ ?>

                                    <?php if (!empty($auditoria['ip_address'])) : ?>
                                        <span class="text-muted ms-3">
                                            <small><i class="fas fa-globe"></i> IP: <?= htmlspecialchars($auditoria['ip_address']) ?></small>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal de Detalle -->
<div class="modal fade" id="modalDetalle" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-info-circle me-2"></i> Detalle del Cambio</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="contenidoDetalle">
                <!-- Contenido dinámico -->
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="fas fa-times me-2"></i>Cerrar</button>
            </div>
        </div>
    </div>
</div>

<style>
    /* Timeline de auditorías */
    .audit-timeline {
        position: relative;
        padding: 20px 0;
    }

    .audit-item {
        position: relative;
        display: flex;
        gap: 20px;
        margin-bottom: 25px;
    }

    .audit-item::before {
        content: '';
        position: absolute;
        left: 19px;
        top: 40px;
        bottom: -25px;
        width: 2px;
        background: #e9ecef;
    }

    .audit-item:last-child::before {
        display: none;
    }

    .audit-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 18px;
        flex-shrink: 0;
        z-index: 1;
    }

    .icon-success {
        background: linear-gradient(135deg, #28a745, #20c997);
        box-shadow: 0 2px 8px rgba(40, 167, 69, 0.3);
    }

    .icon-warning {
        background: linear-gradient(135deg, #ffc107, #ff9800);
        box-shadow: 0 2px 8px rgba(255, 193, 7, 0.3);
    }

    .icon-danger {
        background: linear-gradient(135deg, #dc3545, #c82333);
        box-shadow: 0 2px 8px rgba(220, 53, 69, 0.3);
    }

    .icon-secondary {
        background: linear-gradient(135deg, #6c757d, #5a6268);
        box-shadow: 0 2px 8px rgba(108, 117, 125, 0.3);
    }

    .audit-card {
        flex: 1;
        background: white;
        border: 1px solid #e9ecef;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
    }

    .audit-card:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        transform: translateY(-2px);
    }

    .audit-card.audit-undone {
        opacity: 0.7;
        border-color: #6c757d;
    }

    .audit-header {
        background: #f8f9fa;
        padding: 15px 20px;
        border-bottom: 1px solid #e9ecef;
    }

    .audit-title {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 8px;
        margin-bottom: 10px;
    }

    .audit-action {
        font-weight: 600;
        color: #495057;
        font-size: 16px;
    }

    .audit-table {
        color: #D4A574;
        font-weight: 600;
    }

    .audit-id {
        background: #e9ecef;
        padding: 2px 8px;
        border-radius: 4px;
        font-size: 12px;
        color: #6c757d;
        font-family: monospace;
    }

    .audit-meta {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
        font-size: 14px;
    }

    .audit-user {
        color: #495057;
    }

    .audit-date {
        color: #6c757d;
    }

    .audit-content {
        padding: 20px;
    }

    /* Reutilizar estilos de cambios de la vista anterior */
    .cambios-grid {
        display: grid;
        gap: 15px;
    }

    .cambio-item {
        background: #f8f9fa;
        padding: 12px 15px;
        border-radius: 6px;
        border-left: 3px solid #D4A574;
    }

    .cambio-campo {
        font-weight: 600;
        color: #495057;
        margin-bottom: 8px;
        font-size: 14px;
    }

    .cambio-valores {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
    }

    .valor-anterior {
        background: #fff3cd;
        color: #856404;
        padding: 4px 12px;
        border-radius: 4px;
        font-size: 14px;
        text-decoration: line-through;
    }

    .valor-nuevo {
        background: #d4edda;
        color: #155724;
        padding: 4px 12px;
        border-radius: 4px;
        font-size: 14px;
        font-weight: 600;
    }

    .datos-creados {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 12px;
    }

    .dato-item {
        background: #f8f9fa;
        padding: 10px 15px;
        border-radius: 6px;
        font-size: 14px;
    }

    .dato-item strong {
        color: #495057;
        display: block;
        margin-bottom: 4px;
        font-size: 13px;
    }

    .dato-item span {
        color: #6c757d;
    }

    .audit-actions {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
        padding-top: 15px;
        border-top: 1px solid #e9ecef;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .audit-item {
            gap: 15px;
        }

        .audit-icon {
            width: 35px;
            height: 35px;
            font-size: 16px;
        }

        .audit-item::before {
            left: 16.5px;
        }

        .audit-meta {
            flex-direction: column;
            align-items: flex-start;
        }

        .cambio-valores {
            flex-direction: column;
            align-items: flex-start;
        }

        .datos-creados {
            grid-template-columns: 1fr;
        }

        .audit-actions {
            flex-direction: column;
            align-items: stretch;
        }

        .audit-actions button {
            width: 100%;
        }
    }

    /* Estilos adicionales para la modal */
    .modal-body .summary {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
        font-size: 16px;
        color: #495057;
    }

    .modal-body .list-group-item {
        border: none;
        padding: 10px 0;
    }

    .modal-body .list-group-item strong {
        width: 150px;
        color: #6c757d;
    }

    .modal-body .section-title {
        font-size: 18px;
        color: #D4A574;
        margin-bottom: 15px;
        border-bottom: 1px solid #e9ecef;
        padding-bottom: 10px;
    }
</style>

<script>
    // Toggle panel de filtros
    function toggleFiltros() {
        const panel = document.getElementById('panelFiltros');
        const btn = document.getElementById('btnToggleFiltros');
        const icon = btn.querySelector('i');

        if (panel.style.display === 'none') {
            panel.style.display = 'block';
            icon.classList.remove('fa-chevron-down');
            icon.classList.add('fa-chevron-up');
        } else {
            panel.style.display = 'none';
            icon.classList.remove('fa-chevron-up');
            icon.classList.add('fa-chevron-down');
        }
    }

    // Aplicar filtros
    function aplicarFiltros() {
        const tabla = document.getElementById('filtroTabla').value.toLowerCase();
        const accion = document.getElementById('filtroAccion').value;
        const estado = document.getElementById('filtroEstado').value;

        const items = document.querySelectorAll('.audit-item');
        let visibles = 0;

        items.forEach(item => {
            const itemTabla = item.dataset.tabla;
            const itemAccion = item.dataset.accion;
            const itemEstado = item.dataset.estado;

            let mostrar = true;

            if (tabla && !itemTabla.includes(tabla)) mostrar = false;
            if (accion && itemAccion !== accion) mostrar = false;
            if (estado && itemEstado !== estado) mostrar = false;

            item.style.display = mostrar ? 'flex' : 'none';
            if (mostrar) visibles++;
        });

        // Mostrar mensaje si no hay resultados
        const timeline = document.getElementById('timelineAuditorias');
        let noResults = timeline.querySelector('.no-results-message');

        if (visibles === 0) {
            if (!noResults) {
                noResults = document.createElement('div');
                noResults.className = 'alert alert-warning no-results-message';
                noResults.innerHTML = '<i class="fas fa-search"></i> No se encontraron resultados con los filtros aplicados.';
                timeline.appendChild(noResults);
            }
        } else {
            if (noResults) {
                noResults.remove();
            }
        }
    }

    // Limpiar filtros
    function limpiarFiltros() {
        document.getElementById('filtroTabla').value = '';
        document.getElementById('filtroAccion').value = '';
        document.getElementById('filtroEstado').value = '';

        const items = document.querySelectorAll('.audit-item');
        items.forEach(item => {
            item.style.display = 'flex';
        });

        const noResults = document.querySelector('.no-results-message');
        if (noResults) {
            noResults.remove();
        }
    }

    // Ver detalle de auditoría (mejorado para UX)
    async function verDetalle(id) {
        try {
            const response = await fetch(`/auditorias/show/${id}`);
            if (!response.ok) {
                throw new Error(`Error HTTP: ${response.status} - ${response.statusText}`);
            }
            const data = await response.json();

            if (data.success) {
                const auditoria = data.auditoria;
                const accionUpper = auditoria.accion.toUpperCase();
                const esUpdate = accionUpper === 'UPDATE';
                const esInsert = accionUpper === 'INSERT';
                const esDelete = accionUpper === 'DELETE';

                let contenidoCambios = '';

                if (esUpdate && auditoria.datos_anteriores && auditoria.datos_nuevos) {
                    const cambios = obtenerCambiosRelevantes(auditoria.datos_anteriores, auditoria.datos_nuevos);
                    if (Object.keys(cambios).length > 0) {
                        contenidoCambios = '<div class="cambios-grid">';
                        for (const [campo, valores] of Object.entries(cambios)) {
                            contenidoCambios += `
                                <div class="cambio-item">
                                    <div class="cambio-campo">${formatearCampo(campo)}</div>
                                    <div class="cambio-valores">
                                        <span class="valor-anterior">${valores.anterior}</span>
                                        <i class="fas fa-arrow-right mx-2"></i>
                                        <span class="valor-nuevo">${valores.nuevo}</span>
                                    </div>
                                </div>
                            `;
                        }
                        contenidoCambios += '</div>';
                    } else {
                        contenidoCambios = '<p class="text-muted"><i class="fas fa-info-circle me-2"></i>Sin cambios visibles en los campos principales</p>';
                    }
                } else if (esInsert && auditoria.datos_nuevos) {
                    const datos = JSON.parse(auditoria.datos_nuevos);
                    contenidoCambios = '<div class="datos-creados">';
                    let count = 0;
                    for (const [campo, valor] of Object.entries(datos)) {
                        if (campo !== 'id' && count < 10) {
                            contenidoCambios += `
                                <div class="dato-item">
                                    <strong>${formatearCampo(campo)}:</strong>
                                    <span>${valor}</span>
                                </div>
                            `;
                            count++;
                        }
                    }
                    if (Object.keys(datos).length > 11) {
                        contenidoCambios += '<div class="dato-item text-muted"><small>+ ' + (Object.keys(datos).length - 11) + ' campos más...</small></div>';
                    }
                    contenidoCambios += '</div>';
                } else if (esDelete && auditoria.datos_anteriores) {
                    const datos = JSON.parse(auditoria.datos_anteriores);
                    contenidoCambios = '<div class="alert alert-danger mb-0">';
                    contenidoCambios += '<strong><i class="fas fa-exclamation-triangle me-2"></i>Registro eliminado</strong>';
                    if (datos.nombre) {
                        contenidoCambios += `<p class="mb-0 mt-2">Nombre: <strong>${datos.nombre}</strong></p>`;
                    }
                    contenidoCambios += '</div>';
                } else {
                    contenidoCambios = '<p class="text-muted"><i class="fas fa-info-circle me-2"></i>No hay detalles adicionales disponibles.</p>';
                }

                const summary = `${traducirAccion(auditoria.accion)} en ${traducirTabla(auditoria.tabla)} #${auditoria.registro_id} por ${auditoria.usuario_nombre || 'Sistema'} el ${new Date(auditoria.fecha_accion).toLocaleDateString('es-PE')} a las ${new Date(auditoria.fecha_accion).toLocaleTimeString('es-PE', {hour: '2-digit', minute: '2-digit'})}`;

                let html = `
                    <div class="summary">
                        <i class="fas fa-summary-icon me-2"></i><strong>Resumen:</strong> ${summary}
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="section-title"><i class="fas fa-info-circle me-2"></i>Información General</div>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex"><strong>Fecha:</strong> <span class="ms-auto">${new Date(auditoria.fecha_accion).toLocaleString('es-PE')}</span></li>
                                <li class="list-group-item d-flex"><strong>Usuario:</strong> <span class="ms-auto">${auditoria.usuario_nombre || 'Sistema'}</span></li>
                                <li class="list-group-item d-flex"><strong>Tabla:</strong> <span class="ms-auto">${traducirTabla(auditoria.tabla)}</span></li>
                                <li class="list-group-item d-flex"><strong>Acción:</strong> <span class="ms-auto">${traducirAccion(auditoria.accion)}</span></li>
                                <li class="list-group-item d-flex"><strong>Registro ID:</strong> <span class="ms-auto">#${auditoria.registro_id}</span></li>
                                <li class="list-group-item d-flex"><strong>IP:</strong> <span class="ms-auto">${auditoria.ip_address || 'N/A'}</span></li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <div class="section-title"><i class="fas fa-flag me-2"></i>Estado</div>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex"><strong>Estado:</strong> <span class="ms-auto">${auditoria.deshacer == 1 ? '<span class="badge bg-secondary">Deshecho</span>' : '<span class="badge bg-success">Activo</span>'}</span></li>
                                ${auditoria.deshacer == 1 ? `<li class="list-group-item d-flex"><strong>Fecha Deshacer:</strong> <span class="ms-auto">${new Date(auditoria.fecha_deshacer).toLocaleString('es-PE')}</span></li>` : ''}
                            </ul>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <div class="section-title"><i class="fas fa-exchange-alt me-2"></i>Detalles del Cambio</div>
                        ${contenidoCambios}
                    </div>
                `;

                document.getElementById('contenidoDetalle').innerHTML = html;
                const modal = new bootstrap.Modal(document.getElementById('modalDetalle'));
                modal.show();
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.message || 'Respuesta inesperada del servidor',
                    confirmButtonColor: '#D4A574'
                });
            }
        } catch (error) {
            console.error('Error al cargar detalle:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: error.message || 'Error al cargar el detalle. Verifica la conexión o contacta al administrador.',
                confirmButtonColor: '#D4A574'
            });
        }
    }

    // Función auxiliar para obtener cambios relevantes (en JS)
    function obtenerCambiosRelevantes(datos_anteriores, datos_nuevos) {
        const anterior = JSON.parse(datos_anteriores);
        const nuevo = JSON.parse(datos_nuevos);

        if (!anterior || !nuevo) return {};

        const cambios = {};
        for (const [campo, valor_nuevo] of Object.entries(nuevo)) {
            if (anterior.hasOwnProperty(campo) && anterior[campo] != valor_nuevo && campo !== 'id') {
                cambios[campo] = {
                    anterior: anterior[campo],
                    nuevo: valor_nuevo
                };
            }
        }

        return cambios;
    }

    // Función auxiliar para formatear campos (en JS)
    function formatearCampo(campo) {
        const traducciones = {
            'stock_actual': 'Stock Actual',
            'stock_minimo': 'Stock Mínimo',
            'precio_actual': 'Precio',
            'precio_unitario': 'Precio Unitario',
            'nombre': 'Nombre',
            'descripcion': 'Descripción',
            'total': 'Total',
            'metodo_pago': 'Método de Pago',
            'estado': 'Estado',
            'cantidad': 'Cantidad',
            'correo': 'Correo',
            'telefono': 'Teléfono',
            'direccion': 'Dirección',
            'primer_nombre': 'Primer Nombre',
            'apellido_paterno': 'Apellido Paterno'
        };

        return traducciones[campo] || campo.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
    }

    // Función auxiliar para traducir tabla y acción (en JS)
    function traducirTabla(tabla) {
        const traducciones = {
            'ventas': 'Venta',
            'productos': 'Producto',
            'insumos': 'Insumo',
            'usuarios': 'Usuario',
            'clientes': 'Cliente',
            'proveedores': 'Proveedor',
            'compras': 'Compra',
            'pedidos': 'Pedido',
            'producciones': 'Producción',
            'recetas': 'Receta',
            'sugerencias_compra': 'Sugerencia de Compra',
            'auditoria': 'Auditoría'
        };
        return traducciones[tabla] || tabla.charAt(0).toUpperCase() + tabla.slice(1);
    }

    function traducirAccion(accion) {
        const traducciones = {
            'INSERT': 'Creó',
            'UPDATE': 'Actualizó',
            'DELETE': 'Eliminó',
            'UNDO': 'Deshizo'
        };
        return traducciones[accion.toUpperCase()] || accion.toLowerCase().charAt(0).toUpperCase() + accion.slice(1).toLowerCase();
    }

    // Verificar y deshacer cambio (mejorado con chequeo de response.ok)
    async function verificarYDeshacer(id, accion, tabla) {
        try {
            const response = await fetch(`/auditorias/verificar-deshacer/${id}`);
            if (!response.ok) {
                throw new Error(`Error HTTP: ${response.status} - ${response.statusText}`);
            }
            const dataVerif = await response.json();

            if (!dataVerif.success || !dataVerif.puede_deshacer) {
                Swal.fire({
                    icon: 'warning',
                    title: 'No se puede deshacer',
                    text: dataVerif.message || 'Esta acción no puede ser deshecha (ya fue revertida o han pasado más de 24 horas).',
                    confirmButtonColor: '#D4A574'
                });
                return;
            }

            let mensaje = '';
            switch (accion.toUpperCase()) {
                case 'INSERT':
                    mensaje = `Se eliminará el registro de <strong>${tabla}</strong> que fue creado.`;
                    break;
                case 'UPDATE':
                    mensaje = `Se restaurarán los valores anteriores del registro de <strong>${tabla}</strong>.`;
                    break;
                case 'DELETE':
                    mensaje = `Se restaurará el registro de <strong>${tabla}</strong> que fue eliminado.`;
                    break;
            }

            const result = await Swal.fire({
                title: '¿Deshacer esta acción?',
                html: `
                    <p>${mensaje}</p>
                    <div class="alert alert-warning mt-3">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Advertencia:</strong> Esta operación no se puede revertir.
                    </div>
                `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#D4A574',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fas fa-undo me-2"></i>Sí, deshacer',
                cancelButtonText: 'Cancelar'
            });

            if (result.isConfirmed) {
                await deshacerCambio(id);
            }
        } catch (error) {
            console.error('Error en verificación:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: error.message || 'Error al verificar el cambio. Verifica la conexión, el endpoint del backend o contacta al administrador.',
                confirmButtonColor: '#D4A574'
            });
        }
    }

    // Deshacer cambio (mejorado con chequeo de response.ok)
    async function deshacerCambio(id) {
        try {
            const response = await fetch('/auditorias/deshacer', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    // Si tu sistema usa CSRF, agrégalo aquí, e.g., 'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    auditoria_id: id
                })
            });

            if (!response.ok) {
                throw new Error(`Error HTTP: ${response.status} - ${response.statusText}`);
            }

            const data = await response.json();

            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: '✓ Cambio Deshecho',
                    text: data.message || 'El cambio ha sido deshecho exitosamente.',
                    confirmButtonColor: '#D4A574'
                }).then(() => {
                    location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.message || 'Respuesta inesperada del servidor.',
                    confirmButtonColor: '#D4A574'
                });
            }
        } catch (error) {
            console.error('Error al deshacer:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: error.message || 'Error al deshacer el cambio. Verifica la conexión, el endpoint del backend o contacta al administrador.',
                confirmButtonColor: '#D4A574'
            });
        }
    }
</script>
<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
