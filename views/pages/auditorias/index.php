<?php
$pageTitle = 'Auditorías';
$currentPage = 'auditorias';
require_once __DIR__ . '/../layouts/header.php';

// Solo administradores
if ($_SESSION['user_rol'] !== 'administrador') {
    header('Location: ./dashboard');
    exit;
}
?>

<div class="page-header">
    <h2 class="page-title"><i class="fas fa-history"></i> Auditorías del Sistema</h2>
    <p class="page-subtitle">Registro completo de cambios con capacidad de deshacer</p>
</div>

<!-- Filtros -->
<div class="card mb-4">
    <div class="card-header">
        <h5><i class="fas fa-filter"></i> Filtros</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-3">
                <label class="form-label">Tabla</label>
                <select id="filtroTabla" class="form-select" onchange="aplicarFiltros()">
                    <option value="">Todas las tablas</option>
                    <option value="productos">Productos</option>
                    <option value="insumos">Insumos</option>
                    <option value="ventas">Ventas</option>
                    <option value="clientes">Clientes</option>
                    <option value="pedidos">Pedidos</option>
                    <option value="producciones">Producciones</option>
                    <option value="usuarios">Usuarios</option>
                    <option value="recetas">Recetas</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Acción</label>
                <select id="filtroAccion" class="form-select" onchange="aplicarFiltros()">
                    <option value="">Todas las acciones</option>
                    <option value="INSERT">Creación</option>
                    <option value="UPDATE">Actualización</option>
                    <option value="DELETE">Eliminación</option>
                    <option value="UNDO">Deshacer</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Estado</label>
                <select id="filtroEstado" class="form-select" onchange="aplicarFiltros()">
                    <option value="">Todos</option>
                    <option value="activo">Activos</option>
                    <option value="deshecho">Deshechos</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">&nbsp;</label>
                <button onclick="limpiarFiltros()" class="btn btn-secondary w-100">
                    <i class="fas fa-times"></i> Limpiar Filtros
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Tabla de auditorías -->
<div class="card">
    <div class="card-header">
        <h5><i class="fas fa-list"></i> Historial de Cambios</h5>
    </div>
    <div class="card-body">
        <?php if (empty($auditorias)): ?>
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> No hay registros de auditoría disponibles.
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table id="tablaAuditorias" class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Fecha y Hora</th>
                        <th>Usuario</th>
                        <th>Tabla</th>
                        <th>Acción</th>
                        <th>Registro ID</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($auditorias as $auditoria): ?>
                    <tr data-tabla="<?= $auditoria['tabla'] ?>"
                        data-accion="<?= $auditoria['accion'] ?>"
                        data-estado="<?= $auditoria['deshacer'] == 1 ? 'deshecho' : 'activo' ?>">
                        <td><?= $auditoria['id'] ?></td>
                        <td>
                            <small>
                                <?= date('d/m/Y', strtotime($auditoria['fecha_accion'])) ?><br>
                                <?= date('H:i:s', strtotime($auditoria['fecha_accion'])) ?>
                            </small>
                        </td>
                        <td>
                            <strong><?= htmlspecialchars($auditoria['usuario_nombre'] ?? 'Sistema') ?></strong><br>
                            <small class="text-muted"><?= htmlspecialchars($auditoria['usuario_email'] ?? '') ?></small>
                        </td>
                        <td>
                            <span class="badge bg-info"><?= strtoupper($auditoria['tabla']) ?></span>
                        </td>
                        <td>
                            <?php
                            $badges = [
                                'INSERT' => '<span class="badge bg-success"><i class="fas fa-plus"></i> Crear</span>',
                                'UPDATE' => '<span class="badge bg-warning"><i class="fas fa-edit"></i> Actualizar</span>',
                                'DELETE' => '<span class="badge bg-danger"><i class="fas fa-trash"></i> Eliminar</span>',
                                'UNDO' => '<span class="badge bg-secondary"><i class="fas fa-undo"></i> Deshacer</span>'
                            ];
                            echo $badges[$auditoria['accion']] ?? $auditoria['accion'];
                            ?>
                        </td>
                        <td><code>#<?= $auditoria['registro_id'] ?></code></td>
                        <td>
                            <?php if ($auditoria['deshacer'] == 1): ?>
                                <span class="badge bg-secondary">
                                    <i class="fas fa-undo"></i> Deshecho
                                </span>
                                <br>
                                <small class="text-muted">
                                    <?= date('d/m/Y H:i', strtotime($auditoria['fecha_deshacer'])) ?>
                                </small>
                            <?php else: ?>
                                <span class="badge bg-success">
                                    <i class="fas fa-check"></i> Activo
                                </span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="btn-group">
                                <button onclick="verDetalle(<?= $auditoria['id'] ?>)"
                                        class="btn btn-sm btn-info"
                                        title="Ver detalles">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <?php if ($auditoria['deshacer'] == 0 && in_array($auditoria['accion'], ['INSERT', 'UPDATE', 'DELETE'])): ?>
                                <button onclick="verificarYDeshacer(<?= $auditoria['id'] ?>, '<?= $auditoria['accion'] ?>')"
                                        class="btn btn-sm btn-warning"
                                        title="Deshacer cambio">
                                    <i class="fas fa-undo"></i>
                                </button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal de Detalle -->
<div class="modal fade" id="modalDetalle" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-info-circle"></i> Detalle de Auditoría</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="contenidoDetalle">
                <!-- Contenido dinámico -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script>
// Inicializar DataTable
let dataTable;
document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('tablaAuditorias')) {
        dataTable = new simpleDatatables.DataTable('#tablaAuditorias', {
            searchable: true,
            perPageSelect: [10, 25, 50, 100],
            perPage: 25,
            labels: {
                placeholder: "Buscar...",
                perPage: "registros por página",
                noRows: "No se encontraron registros",
                info: "Mostrando {start} a {end} de {rows} registros"
            }
        });
    }
});

// Aplicar filtros
function aplicarFiltros() {
    const tabla = document.getElementById('filtroTabla').value.toLowerCase();
    const accion = document.getElementById('filtroAccion').value;
    const estado = document.getElementById('filtroEstado').value;

    const filas = document.querySelectorAll('#tablaAuditorias tbody tr');

    filas.forEach(fila => {
        const filaTabla = fila.dataset.tabla;
        const filaAccion = fila.dataset.accion;
        const filaEstado = fila.dataset.estado;

        let mostrar = true;

        if (tabla && !filaTabla.includes(tabla)) mostrar = false;
        if (accion && filaAccion !== accion) mostrar = false;
        if (estado && filaEstado !== estado) mostrar = false;

        fila.style.display = mostrar ? '' : 'none';
    });
}

// Limpiar filtros
function limpiarFiltros() {
    document.getElementById('filtroTabla').value = '';
    document.getElementById('filtroAccion').value = '';
    document.getElementById('filtroEstado').value = '';
    aplicarFiltros();
}

// Ver detalle de auditoría
async function verDetalle(id) {
    try {
        const response = await fetch(`/auditorias/show/${id}`);
        const data = await response.json();

        if (data.success) {
            const auditoria = data.auditoria;

            let html = `
                <div class="row">
                    <div class="col-md-6">
                        <h6>Información General</h6>
                        <table class="table table-sm table-bordered">
                            <tr><th>ID:</th><td>${auditoria.id}</td></tr>
                            <tr><th>Fecha:</th><td>${auditoria.fecha_accion}</td></tr>
                            <tr><th>Usuario:</th><td>${auditoria.usuario_nombre || 'N/A'}</td></tr>
                            <tr><th>Tabla:</th><td>${auditoria.tabla}</td></tr>
                            <tr><th>Acción:</th><td>${auditoria.accion}</td></tr>
                            <tr><th>Registro ID:</th><td>${auditoria.registro_id}</td></tr>
                            <tr><th>IP:</th><td>${auditoria.ip_address || 'N/A'}</td></tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6>Estado</h6>
                        <table class="table table-sm table-bordered">
                            <tr><th>Deshecho:</th><td>${auditoria.deshacer == 1 ? 'Sí' : 'No'}</td></tr>
                            ${auditoria.deshacer == 1 ? `
                            <tr><th>Fecha Deshacer:</th><td>${auditoria.fecha_deshacer}</td></tr>
                            <tr><th>Usuario Deshacer:</th><td>${auditoria.usuario_deshacer_nombre || 'N/A'}</td></tr>
                            ` : ''}
                        </table>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-6">
                        <h6>Datos Anteriores</h6>
                        <pre class="bg-light p-3" style="max-height: 300px; overflow-y: auto;">${JSON.stringify(JSON.parse(auditoria.datos_anteriores || '{}'), null, 2)}</pre>
                    </div>
                    <div class="col-md-6">
                        <h6>Datos Nuevos</h6>
                        <pre class="bg-light p-3" style="max-height: 300px; overflow-y: auto;">${JSON.stringify(JSON.parse(auditoria.datos_nuevos || '{}'), null, 2)}</pre>
                    </div>
                </div>
            `;

            document.getElementById('contenidoDetalle').innerHTML = html;
            const modal = new bootstrap.Modal(document.getElementById('modalDetalle'));
            modal.show();
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message,
                confirmButtonColor: '#D4A574'
            });
        }
    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Error al cargar detalle',
            confirmButtonColor: '#D4A574'
        });
    }
}

// Verificar y deshacer cambio
async function verificarYDeshacer(id, accion) {
    // Verificar si se puede deshacer
    try {
        const verificacion = await fetch(`/auditorias/verificar-deshacer/${id}`);
        const dataVerif = await verificacion.json();

        if (!dataVerif.puede_deshacer) {
            Swal.fire({
                icon: 'warning',
                title: 'No se puede deshacer',
                text: 'Esta acción no puede ser deshecha (ya fue revertida o han pasado más de 24 horas)',
                confirmButtonColor: '#D4A574'
            });
            return;
        }

        // Mensajes personalizados según la acción
        let mensaje = '';
        switch(accion) {
            case 'INSERT':
                mensaje = 'Se eliminará el registro que fue creado';
                break;
            case 'UPDATE':
                mensaje = 'Se restaurarán los valores anteriores del registro';
                break;
            case 'DELETE':
                mensaje = 'Se restaurará el registro que fue eliminado';
                break;
        }

        const result = await Swal.fire({
            title: '¿Deshacer esta acción?',
            html: `<p>${mensaje}</p><p class="text-warning"><strong>Esta operación no se puede revertir</strong></p>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#D4A574',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, deshacer',
            cancelButtonText: 'Cancelar'
        });

        if (result.isConfirmed) {
            await deshacerCambio(id);
        }
    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Error al verificar el cambio',
            confirmButtonColor: '#D4A574'
        });
    }
}

// Deshacer cambio
async function deshacerCambio(id) {
    try {
        const response = await fetch('/auditorias/deshacer', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ auditoria_id: id })
        });

        const data = await response.json();

        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Cambio Deshecho',
                text: data.message,
                confirmButtonColor: '#D4A574'
            }).then(() => {
                location.reload();
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message,
                confirmButtonColor: '#D4A574'
            });
        }
    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Error al deshacer cambio',
            confirmButtonColor: '#D4A574'
        });
    }
}
</script>

<style>
.table tbody tr:hover {
    background-color: #f8f9fa;
}

.btn-group .btn {
    margin-right: 2px;
}

pre {
    font-size: 0.85rem;
    border-radius: 4px;
}
</style>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

