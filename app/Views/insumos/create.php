<?php
$pageTitle = 'Nuevo Insumo';
$currentPage = 'insumos';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="page-header">
    <h2 class="page-title">Nuevo Insumo</h2>
    <p class="page-subtitle">Registrar un nuevo insumo o materia prima</p>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Información del Insumo</h3>
        <a href="/insumos" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>
    <div class="card-body">
        <form id="formInsumo" action="/insumos/store" method="POST">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Nombre del Insumo <span class="text-danger">*</span></label>
                        <input type="text" name="nombre" class="form-control" required>
                    </div>
                </div>
                
                    <div class="form-group">
                        <label class="form-label">Unidad de Medida <span class="text-danger">*</span></label>
                        <select name="unidad_medida" class="form-control" required>
                            <option value="">Seleccionar unidad</option>
                            <option value="kg">Kilogramo (kg)</option>
                            <option value="g">Gramo (g)</option>
                            <option value="l">Litro (l)</option>
                            <option value="ml">Mililitro (ml)</option>
                            <option value="unidad">Unidad</option>
                            <option value="caja">Caja</option>
                            <option value="bolsa">Bolsa</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Proveedor Principal</label>
                        <select name="id_proveedor" class="form-control">
                            <option value="">-- Seleccionar Proveedor --</option>
                            <?php foreach ($proveedores as $prov) : ?>
                            <option value="<?= $prov['id'] ?>"><?= htmlspecialchars($prov['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label class="form-label">Descripción</label>
                        <textarea name="descripcion" class="form-control" rows="3"></textarea>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label">Precio Unitario <span class="text-danger">*</span></label>
                        <input type="number" name="precio_unitario" class="form-control" step="0.01" required>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label">Stock Inicial <span class="text-danger">*</span></label>
                        <input type="number" name="stock_actual" class="form-control" step="0.01" required>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label">Stock Mínimo <span class="text-danger">*</span></label>
                        <input type="number" name="stock_minimo" class="form-control" step="0.01" required>
                    </div>
                </div>
            </div>
            
            <div class="form-group mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Guardar Insumo
                </button>
                <a href="/insumos" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancelar
                </a>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('formInsumo').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    if (!SIPAN.validateForm(this)) {
        SIPAN.error('Por favor complete todos los campos requeridos');
        return;
    }
    
    const data = await SIPAN.submitForm(this, (response) => {
        setTimeout(() => {
            window.location.href = '/insumos';
        }, 1500);
    });
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
