<?php 
$pageTitle = 'Reportes';
$currentPage = 'reportes';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="page-header">
    <h2 class="page-title">Reportes</h2>
    <p class="page-subtitle">Genera reportes detallados del sistema</p>
</div>

<div class="row">
    <!-- Reporte de Ventas -->
    <div class="col-md-6 col-lg-4 mb-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <div class="mb-3">
                    <i class="fas fa-chart-line fa-4x" style="color: #D4A574;"></i>
                </div>
                <h3 class="card-title">Reporte de Ventas</h3>
                <p class="text-muted">Genera un reporte detallado de las ventas por período</p>
                <a href="#" onclick="mostrarModalVentas()" class="btn btn-primary">
                    <i class="fas fa-file-pdf"></i> Generar Reporte
                </a>
            </div>
        </div>
    </div>
    
    <!-- Reporte de Productos -->
    <div class="col-md-6 col-lg-4 mb-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <div class="mb-3">
                    <i class="fas fa-box fa-4x" style="color: #8B6F47;"></i>
                </div>
                <h3 class="card-title">Reporte de Productos</h3>
                <p class="text-muted">Inventario actual y valor de stock</p>
                <a href="/reportes/productos" class="btn btn-primary">
                    <i class="fas fa-eye"></i> Ver Reporte
                </a>
                <a href="/reportes/productos?formato=pdf" class="btn btn-secondary">
                    <i class="fas fa-file-pdf"></i> PDF
                </a>
            </div>
        </div>
    </div>
    
    <!-- Reporte de Clientes -->
    <div class="col-md-6 col-lg-4 mb-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <div class="mb-3">
                    <i class="fas fa-users fa-4x" style="color: #D4A574;"></i>
                </div>
                <h3 class="card-title">Reporte de Clientes</h3>
                <p class="text-muted">Listado de clientes y sus estadísticas</p>
                <a href="/reportes/clientes" class="btn btn-primary">
                    <i class="fas fa-eye"></i> Ver Reporte
                </a>
                <a href="/reportes/clientes?formato=pdf" class="btn btn-secondary">
                    <i class="fas fa-file-pdf"></i> PDF
                </a>
            </div>
        </div>
    </div>
    
    <!-- Reporte de Insumos -->
    <div class="col-md-6 col-lg-4 mb-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <div class="mb-3">
                    <i class="fas fa-warehouse fa-4x" style="color: #8B6F47;"></i>
                </div>
                <h3 class="card-title">Reporte de Insumos</h3>
                <p class="text-muted">Estado actual del inventario de insumos</p>
                <a href="/reportes/insumos" class="btn btn-primary">
                    <i class="fas fa-eye"></i> Ver Listado
                </a>
            </div>
        </div>
    </div>
    
    <!-- Reporte de Producciones -->
    <div class="col-md-6 col-lg-4 mb-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <div class="mb-3">
                    <i class="fas fa-industry fa-4x" style="color: #D4A574;"></i>
                </div>
                <h3 class="card-title">Reporte de Producciones</h3>
                <p class="text-muted">Historial de producciones y costos</p>
                <a href="/reportes/producciones" class="btn btn-primary">
                    <i class="fas fa-eye"></i> Ver Historial
                </a>
            </div>
        </div>
    </div>
    
    <!-- Reporte de Pedidos -->
    <div class="col-md-6 col-lg-4 mb-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <div class="mb-3">
                    <i class="fas fa-clipboard-list fa-4x" style="color: #8B6F47;"></i>
                </div>
                <h3 class="card-title">Reporte de Pedidos</h3>
                <p class="text-muted">Estado de pedidos y pagos pendientes</p>
                <a href="/reportes/pedidos" class="btn btn-primary">
                    <i class="fas fa-eye"></i> Ver Pedidos
                </a>
            </div>
        </div>
    </div>
    <!-- Reporte de Compras -->
   

    <!-- Reporte de Vencimientos -->
    
</div>

<!-- Modal para Reporte de Ventas -->
<div id="modalVentas" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999;">
    <div style="position: relative; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 2rem; border-radius: 12px; max-width: 500px; width: 90%;">
        <h3 class="mb-4">Reporte de Ventas</h3>
        
        <form id="formReporteVentas" method="GET" action="/reportes/ventas">
            <div class="mb-3">
                <label class="form-label">Fecha Inicio <span class="text-danger">*</span></label>
                <input type="date" name="fecha_inicio" class="form-control" required value="<?= date('Y-m-01') ?>">
            </div>
            
            <div class="mb-3">
                <label class="form-label">Fecha Fin <span class="text-danger">*</span></label>
                <input type="date" name="fecha_fin" class="form-control" required value="<?= date('Y-m-d') ?>">
            </div>
            
            <div class="mb-3">
                <label class="form-label">Formato</label>
                <select name="formato" class="form-control">
                    <option value="html">Ver en Pantalla</option>
                    <option value="pdf">Descargar PDF</option>
                </select>
            </div>
            
            <div class="d-flex gap-2">
                <button type="button" onclick="cerrarModalVentas()" class="btn btn-secondary flex-fill">Cancelar</button>
                <button type="submit" class="btn btn-primary flex-fill">Generar</button>
            </div>
        </form>
    </div>
</div>

<script>
function mostrarModalVentas() {
    document.getElementById('modalVentas').style.display = 'block';
}

function cerrarModalVentas() {
    document.getElementById('modalVentas').style.display = 'none';
}
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
