<?php
$pageTitle = 'Editar Producto';
$currentPage = 'productos';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="page-header">
    <h2 class="page-title">Editar Producto</h2>
    <p class="page-subtitle">Modificar información del producto</p>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Información del Producto</h3>
        <a href="/productos" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>
    <div class="card-body">
        <form id="formProducto" action="/productos/update/<?= $producto['id'] ?>" method="POST" x-data="productoForm()">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Nombre del Producto <span class="text-danger">*</span></label>
                        <input type="text" name="nombre" class="form-control" required x-model="nombre" value="<?= htmlspecialchars($producto['nombre']) ?>">
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Categoría</label>
                        <select name="categoria" class="form-control" x-model="categoria">
                            <option value="">Seleccionar categoría</option>
                            <option value="Pan" <?= ($producto['categoria'] ?? '') === 'Pan' ? 'selected' : '' ?>>Pan</option>
                            <option value="Torta" <?= ($producto['categoria'] ?? '') === 'Torta' ? 'selected' : '' ?>>Torta</option>
                            <option value="Pastel" <?= ($producto['categoria'] ?? '') === 'Pastel' ? 'selected' : '' ?>>Pastel</option>
                            <option value="Galleta" <?= ($producto['categoria'] ?? '') === 'Galleta' ? 'selected' : '' ?>>Galleta</option>
                            <option value="Empanada" <?= ($producto['categoria'] ?? '') === 'Empanada' ? 'selected' : '' ?>>Empanada</option>
                            <option value="Otro" <?= ($producto['categoria'] ?? '') === 'Otro' ? 'selected' : '' ?>>Otro</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label class="form-label">Descripción</label>
                        <textarea name="descripcion" class="form-control" rows="3" x-model="descripcion"><?= htmlspecialchars($producto['descripcion'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label">Precio de Venta <span class="text-danger">*</span></label>
                        <input type="number" name="precio_actual" class="form-control" step="0.01" required x-model="precio_actual" value="<?= $producto['precio_actual'] ?>">
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label">Stock Actual <span class="text-danger">*</span></label>
                        <input type="number" name="stock_actual" class="form-control" required x-model="stock_actual" value="<?= $producto['stock_actual'] ?>">
                        <small class="text-muted">Stock actual: <?= $producto['stock_actual'] ?></small>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label">Stock Mínimo <span class="text-danger">*</span></label>
                        <input type="number" name="stock_minimo" class="form-control" required x-model="stock_minimo" value="<?= $producto['stock_minimo'] ?>">
                    </div>
                </div>
            </div>
            
            <div class="form-group mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Actualizar Producto
                </button>
                <a href="/productos" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancelar
                </a>
            </div>
        </form>
    </div>
</div>

<script>
function productoForm() {
    return {
        nombre: '<?= htmlspecialchars($producto['nombre']) ?>',
        categoria: '<?= $producto['categoria'] ?? '' ?>',
        descripcion: '<?= htmlspecialchars($producto['descripcion'] ?? '') ?>',
        precio_actual: '<?= $producto['precio_actual'] ?>',
        stock_actual: '<?= $producto['stock_actual'] ?>',
        stock_minimo: '<?= $producto['stock_minimo'] ?>',
        
        init() {
            const form = document.getElementById('formProducto');
            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                
                if (!SIPAN.validateForm(form)) {
                    SIPAN.error('Por favor complete todos los campos requeridos');
                    return;
                }
                
                const data = await SIPAN.submitForm(form, (response) => {
                    setTimeout(() => {
                        window.location.href = '/productos';
                    }, 1500);
                });
            });
        }
    }
}
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
