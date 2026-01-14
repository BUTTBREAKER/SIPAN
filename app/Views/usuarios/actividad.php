<?php
$pageTitle = 'Actividad de Usuario';
$currentPage = 'usuarios';
require_once __DIR__ . '/../layouts/header.php';

// Función para traducir acciones
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

// Función para traducir nombres de tablas
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
        'sugerencias_compra' => 'Sugerencia de Compra',
        'auditoria' => 'Auditoría'
    ];
    return $traducciones[$tabla] ?? ucfirst($tabla);
}

// Función para obtener los cambios importantes en un UPDATE
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

// Función para formatear nombres de campos
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
        'direccion' => 'Dirección'
    ];

    return $traducciones[$campo] ?? ucwords(str_replace('_', ' ', $campo));
}
?>

<div class="page-header">
    <h2 class="page-title">Actividad de <?= htmlspecialchars($usuario['nombre'] ?? 'Usuario') ?></h2>
    <p class="page-subtitle">Historial completo de cambios realizados</p>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title"><i class="fas fa-history"></i> Historial de Actividad</h3>
        <a href="<?= $_SESSION['user_rol'] === 'administrador' ? '/usuarios' : '/usuarios/perfil' ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>
    <div class="card-body">
        <?php if (empty($actividad)) : ?>
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> No hay actividad registrada para este usuario
        </div>
        <?php else : ?>
        <div class="activity-timeline">
            <?php foreach ($actividad as $act) : ?>
                <?php
                $accion_upper = strtoupper($act['accion']);
                $es_update = $accion_upper === 'UPDATE';
                $es_insert = $accion_upper === 'INSERT';
                $es_delete = $accion_upper === 'DELETE';
                ?>
            <div class="activity-item">
                <div class="activity-icon <?= $es_insert ? 'icon-success' : ($es_delete ? 'icon-danger' : 'icon-warning') ?>">
                    <i class="fas fa-<?= $es_insert ? 'plus-circle' : ($es_delete ? 'trash-alt' : 'edit') ?>"></i>
                </div>
                
                <div class="activity-card">
                    <div class="activity-header">
                        <div>
                            <span class="activity-action"><?= traducirAccion($act['accion']) ?></span>
                            <span class="activity-table"><?= traducirTabla($act['tabla']) ?></span>
                            <span class="activity-id">#<?= $act['registro_id'] ?? 'N/A' ?></span>
                        </div>
                        <div class="activity-date">
                            <i class="far fa-clock"></i> <?= date('d/m/Y', strtotime($act['fecha_accion'])) ?>
                            <span class="text-muted">a las</span> <?= date('H:i', strtotime($act['fecha_accion'])) ?>
                        </div>
                    </div>
                    
                    <div class="activity-content">
                        <?php if ($es_update && !empty($act['datos_anteriores']) && !empty($act['datos_nuevos'])) : ?>
                            <?php $cambios = obtenerCambiosRelevantes($act['datos_anteriores'], $act['datos_nuevos']); ?>
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
                            
                        <?php elseif ($es_insert && !empty($act['datos_nuevos'])) : ?>
                            <?php $datos = json_decode($act['datos_nuevos'], true); ?>
                            <?php if ($datos) : ?>
                                <div class="datos-creados">
                                    <?php foreach ($datos as $campo => $valor) : ?>
                                        <?php if ($campo !== 'id') : ?>
                                        <div class="dato-item">
                                            <strong><?= formatearCampo($campo) ?>:</strong>
                                            <span><?= htmlspecialchars($valor) ?></span>
                                        </div>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                            
                        <?php elseif ($es_delete && !empty($act['datos_anteriores'])) : ?>
                            <?php $datos = json_decode($act['datos_anteriores'], true); ?>
                            <?php if ($datos) : ?>
                                <div class="alert alert-danger mb-0">
                                    <strong><i class="fas fa-exclamation-triangle"></i> Registro eliminado</strong>
                                    <?php if (isset($datos['nombre'])) : ?>
                                        <p class="mb-0 mt-2">Nombre: <strong><?= htmlspecialchars($datos['nombre']) ?></strong></p>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                        
                        <?php if (!empty($act['ip_address'])) : ?>
                        <div class="activity-meta mt-2">
                            <small class="text-muted">
                                <i class="fas fa-globe"></i> IP: <?= htmlspecialchars($act['ip_address']) ?>
                            </small>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<style>
.activity-timeline {
    position: relative;
    padding: 20px 0;
}

.activity-item {
    position: relative;
    display: flex;
    gap: 20px;
    margin-bottom: 25px;
}

.activity-item::before {
    content: '';
    position: absolute;
    left: 19px;
    top: 40px;
    bottom: -25px;
    width: 2px;
    background: #e9ecef;
}

.activity-item:last-child::before {
    display: none;
}

.activity-icon {
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

.activity-card {
    flex: 1;
    background: white;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    transition: all 0.3s ease;
}

.activity-card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    transform: translateY(-2px);
}

.activity-header {
    background: #f8f9fa;
    padding: 15px 20px;
    border-bottom: 1px solid #e9ecef;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px;
}

.activity-action {
    font-weight: 600;
    color: #495057;
    font-size: 16px;
}

.activity-table {
    color: #D4A574;
    font-weight: 600;
    margin-left: 5px;
}

.activity-id {
    background: #e9ecef;
    padding: 2px 8px;
    border-radius: 4px;
    font-size: 12px;
    color: #6c757d;
    margin-left: 8px;
}

.activity-date {
    color: #6c757d;
    font-size: 14px;
}

.activity-content {
    padding: 20px;
}

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

.activity-meta {
    padding-top: 12px;
    border-top: 1px solid #e9ecef;
}

@media (max-width: 768px) {
    .activity-item {
        gap: 15px;
    }
    
    .activity-icon {
        width: 35px;
        height: 35px;
        font-size: 16px;
    }
    
    .activity-item::before {
        left: 16.5px;
    }
    
    .activity-header {
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
}
</style>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
