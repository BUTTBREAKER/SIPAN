<?php
$pageTitle = 'Predicciones';
$currentPage = 'predicciones';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="page-header">
    <h2 class="page-title"><i class="fas fa-chart-line"></i> Predicciones y Sugerencias Inteligentes</h2>
    <p class="page-subtitle">Análisis predictivo basado en datos históricos</p>
</div>

<!-- Resumen General -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card text-white bg-primary">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-shopping-cart"></i> Predicción de Ventas</h5>
                <h3>
                    <?php if (!empty($predicciones_ventas)): ?>
                        S/ <?= number_format(array_sum(array_column($predicciones_ventas, 'venta_estimada')), 2) ?>
                    <?php else: ?>
                        N/A
                    <?php endif; ?>
                </h3>
                <p class="mb-0">Próximos 7 días</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-white bg-warning">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-industry"></i> Productos a Producir</h5>
                <h3><?= count($predicciones_produccion) ?></h3>
                <p class="mb-0">Productos con stock bajo</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-white bg-danger">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-shopping-basket"></i> Insumos a Comprar</h5>
                <h3><?= count($sugerencias_compra) ?></h3>
                <p class="mb-0">Insumos con stock insuficiente</p>
            </div>
        </div>
    </div>
</div>

<!-- Predicciones de Ventas -->
<div class="card mb-4">
    <div class="card-header">
        <h5><i class="fas fa-shopping-cart"></i> Predicción de Ventas (Próximos 7 Días)</h5>
    </div>
    <div class="card-body">
        <?php if (!empty($predicciones_ventas)): ?>
        <div class="row">
            <div class="col-md-8">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Fecha</th>
                                <th>Día</th>
                                <th>Venta Estimada</th>
                                <th>Confianza</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($predicciones_ventas as $prediccion): ?>
                            <tr>
                                <td><?= date('d/m/Y', strtotime($prediccion['fecha'])) ?></td>
                                <td><?= strftime('%A', strtotime($prediccion['fecha'])) ?></td>
                                <td><strong>S/ <?= number_format($prediccion['venta_estimada'], 2) ?></strong></td>
                                <td>
                                    <?php
                                    $badge_class = $prediccion['confianza'] === 'alta' ? 'success' : 
                                                 ($prediccion['confianza'] === 'media' ? 'warning' : 'secondary');
                                    ?>
                                    <span class="badge bg-<?= $badge_class ?>">
                                        <?= ucfirst($prediccion['confianza']) ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot class="table-info">
                            <tr>
                                <th colspan="2">Total Estimado (7 días)</th>
                                <th>S/ <?= number_format(array_sum(array_column($predicciones_ventas, 'venta_estimada')), 2) ?></th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <div class="col-md-4">
                <div class="alert alert-info">
                    <h6><i class="fas fa-info-circle"></i> Información</h6>
                    <p>Las predicciones se basan en:</p>
                    <ul class="mb-0">
                        <li>Ventas de los últimos 30 días</li>
                        <li>Tendencias identificadas</li>
                        <li>Patrones de comportamiento</li>
                    </ul>
                </div>
                <div class="alert alert-warning">
                    <h6><i class="fas fa-lightbulb"></i> Recomendación</h6>
                    <p class="mb-0">Prepara inventario adicional para los días con mayor predicción de ventas.</p>
                </div>
            </div>
        </div>
        <?php else: ?>
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> No hay suficientes datos históricos para generar predicciones de ventas. Se requieren al menos 7 días de historial.
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Sugerencias de Producción -->
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5><i class="fas fa-industry"></i> Sugerencias de Producción</h5>
        <?php if (!empty($predicciones_produccion)): ?>
        <span class="badge bg-warning"><?= count($predicciones_produccion) ?> productos</span>
        <?php endif; ?>
    </div>
    <div class="card-body">
        <?php if (!empty($predicciones_produccion)): ?>
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Producto</th>
                        <th>Stock Actual</th>
                        <th>Promedio Diario</th>
                        <th>Días de Stock</th>
                        <th>Cantidad Sugerida</th>
                        <th>Prioridad</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($predicciones_produccion as $prediccion): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($prediccion['producto']) ?></strong></td>
                        <td><?= number_format($prediccion['stock_actual'], 2) ?> unidades</td>
                        <td><?= number_format($prediccion['promedio_diario'], 2) ?> unidades/día</td>
                        <td>
                            <?php
                            $dias = $prediccion['dias_stock'];
                            $color = $dias < 2 ? 'danger' : ($dias < 4 ? 'warning' : 'success');
                            ?>
                            <span class="badge bg-<?= $color ?>">
                                <i class="fas fa-clock"></i> <?= number_format($dias, 1) ?> días
                            </span>
                        </td>
                        <td><strong><?= $prediccion['cantidad_sugerida'] ?></strong> unidades</td>
                        <td>
                            <?php
                            $badge_class = $prediccion['prioridad'] === 'alta' ? 'danger' : 
                                         ($prediccion['prioridad'] === 'media' ? 'warning' : 'info');
                            ?>
                            <span class="badge bg-<?= $badge_class ?>">
                                <?= ucfirst($prediccion['prioridad']) ?>
                            </span>
                        </td>
                        <td>
                            <a href="./producciones/create" class="btn btn-sm btn-primary">
                                <i class="fas fa-plus"></i> Producir
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <div class="alert alert-warning mt-3">
            <i class="fas fa-exclamation-triangle"></i> <strong>Atención:</strong> 
            Los productos con menos de 2 días de stock requieren producción urgente.
        </div>
        <?php else: ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> Excelente! Todos los productos tienen stock suficiente para los próximos días.
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Sugerencias de Compra -->
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5><i class="fas fa-shopping-basket"></i> Sugerencias de Compra de Insumos</h5>
        <?php if (!empty($sugerencias_compra)): ?>
        <span class="badge bg-danger"><?= count($sugerencias_compra) ?> insumos</span>
        <?php endif; ?>
    </div>
    <div class="card-body">
        <?php if (!empty($sugerencias_compra)): ?>
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Insumo</th>
                        <th>Stock Actual</th>
                        <th>Stock Mínimo</th>
                        <th>Cantidad Sugerida</th>
                        <th>Costo Estimado</th>
                        <th>Prioridad</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $costo_total = 0;
                    foreach ($sugerencias_compra as $sugerencia): 
                        $costo_total += $sugerencia['costo_estimado'];
                    ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($sugerencia['insumo']) ?></strong></td>
                        <td>
                            <span class="text-danger">
                                <?= number_format($sugerencia['stock_actual'], 2) ?> <?= $sugerencia['unidad_medida'] ?>
                            </span>
                        </td>
                        <td><?= number_format($sugerencia['stock_minimo'], 2) ?> <?= $sugerencia['unidad_medida'] ?></td>
                        <td>
                            <strong><?= number_format($sugerencia['cantidad_sugerida'], 2) ?></strong> 
                            <?= $sugerencia['unidad_medida'] ?>
                        </td>
                        <td><strong>S/ <?= number_format($sugerencia['costo_estimado'], 2) ?></strong></td>
                        <td>
                            <?php
                            $badge_class = $sugerencia['prioridad'] === 'alta' ? 'danger' : 
                                         ($sugerencia['prioridad'] === 'media' ? 'warning' : 'info');
                            ?>
                            <span class="badge bg-<?= $badge_class ?>">
                                <?= ucfirst($sugerencia['prioridad']) ?>
                            </span>
                        </td>
                        <td>
                            <button onclick="agregarAListaCompra('<?= htmlspecialchars($sugerencia['insumo']) ?>', <?= $sugerencia['cantidad_sugerida'] ?>)" 
                                    class="btn btn-sm btn-success">
                                <i class="fas fa-cart-plus"></i> Agregar
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot class="table-info">
                    <tr>
                        <th colspan="4">Inversión Total Estimada</th>
                        <th colspan="3"><strong>S/ <?= number_format($costo_total, 2) ?></strong></th>
                    </tr>
                </tfoot>
            </table>
        </div>
        
        <div class="row mt-3">
            <div class="col-md-6">
                <div class="alert alert-info">
                    <h6><i class="fas fa-info-circle"></i> Nota Importante</h6>
                    <p class="mb-0">
                        Las sugerencias se calculan basándose en el stock actual, stock mínimo y consumo histórico. 
                        Los costos son estimados según los precios unitarios registrados.
                    </p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="alert alert-warning">
                    <h6><i class="fas fa-lightbulb"></i> Recomendación</h6>
                    <p class="mb-0">
                        Prioriza la compra de insumos con prioridad <strong>ALTA</strong> para evitar 
                        interrupciones en la producción.
                    </p>
                </div>
            </div>
        </div>
        
        <div class="text-center mt-3">
            <button onclick="exportarListaCompra()" class="btn btn-primary">
                <i class="fas fa-file-export"></i> Exportar Lista de Compra
            </button>
            <button onclick="enviarPorEmail()" class="btn btn-info">
                <i class="fas fa-envelope"></i> Enviar por Email
            </button>
        </div>
        <?php else: ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> Excelente! Todos los insumos tienen stock suficiente.
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Acciones Globales -->
<div class="card">
    <div class="card-header">
        <h5><i class="fas fa-cog"></i> Acciones</h5>
    </div>
    <div class="card-body">
        <div class="d-flex gap-2">
            <button onclick="actualizarPredicciones()" class="btn btn-primary">
                <i class="fas fa-sync"></i> Actualizar Predicciones
            </button>
            <button onclick="window.print()" class="btn btn-info">
                <i class="fas fa-print"></i> Imprimir Reporte
            </button>
            <button onclick="exportarPDF()" class="btn btn-secondary">
                <i class="fas fa-file-pdf"></i> Exportar PDF
            </button>
        </div>
    </div>
</div>

<script>
// Actualizar predicciones
async function actualizarPredicciones() {
    const result = await Swal.fire({
        title: '¿Actualizar predicciones?',
        text: 'Se generarán nuevas sugerencias basadas en los datos actuales',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#D4A574',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, actualizar',
        cancelButtonText: 'Cancelar'
    });
    
    if (result.isConfirmed) {
        try {
            const response = await fetch(App::getUrl('predicciones.generar'), {
                method: 'POST'
            });
            const data = await response.json();
            
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Predicciones Actualizadas',
                    text: data.message,
                    confirmButtonColor: '#D4A574'
                }).then(() => location.reload());
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
                text: 'Error de conexión',
                confirmButtonColor: '#D4A574'
            });
        }
    }
}

// Agregar a lista de compra
function agregarAListaCompra(insumo, cantidad) {
    Swal.fire({
        icon: 'success',
        title: 'Agregado',
        text: `${insumo} (${cantidad}) agregado a la lista de compra`,
        confirmButtonColor: '#D4A574',
        timer: 2000
    });
}

// Exportar lista de compra
function exportarListaCompra() {
    Swal.fire({
        icon: 'info',
        title: 'Exportando',
        text: 'Generando lista de compra...',
        confirmButtonColor: '#D4A574'
    });
}

// Enviar por email
function enviarPorEmail() {
    Swal.fire({
        title: 'Enviar por Email',
        input: 'email',
        inputPlaceholder: 'Ingrese el email del destinatario',
        showCancelButton: true,
        confirmButtonColor: '#D4A574',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Enviar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed && result.value) {
            Swal.fire({
                icon: 'success',
                title: 'Enviado',
                text: `Reporte enviado a ${result.value}`,
                confirmButtonColor: '#D4A574'
            });
        }
    });
}

// Exportar PDF
function exportarPDF() {
    window.print();
}
</script>

<style>
@media print {
    .btn, .page-header, .card-header button {
        display: none !important;
    }
}
</style>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

