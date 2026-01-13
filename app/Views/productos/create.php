<?php 
$pageTitle = 'Nuevo Producto';
$currentPage = 'productos';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="page-header">
    <h2 class="page-title">Nuevo Producto</h2>
    <p class="page-subtitle">Registrar un nuevo producto en el inventario</p>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Información del Producto</h3>
        <a href="/productos" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>
    <div class="card-body">
        <form id="formProducto" action="/productos/store" method="POST" x-data="productoForm()">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Nombre del Producto <span class="text-danger">*</span></label>
                        <input type="text" name="nombre" class="form-control" required x-model="nombre">
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Categoría</label>
                        <select name="categoria" class="form-control" x-model="categoria">
                            <option value="">Seleccionar categoría</option>
                            <option value="Pan">Pan</option>
                            <option value="Torta">Torta</option>
                            <option value="Pastel">Pastel</option>
                            <option value="Galleta">Galleta</option>
                            <option value="Empanada">Empanada</option>
                            <option value="Otro">Otro</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label class="form-label">Descripción</label>
                        <textarea name="descripcion" class="form-control" rows="3" x-model="descripcion"></textarea>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label">Precio de Venta <span class="text-danger">*</span></label>
                        <input type="number" name="precio_actual" class="form-control" step="0.01" required x-model="precio_actual">
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label">Stock Inicial <span class="text-danger">*</span></label>
                        <input type="number" name="stock_actual" class="form-control" required x-model="stock_actual">
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label">Stock Mínimo <span class="text-danger">*</span></label>
                        <input type="number" name="stock_minimo" class="form-control" required x-model="stock_minimo">
                    </div>
                </div>
            </div>
            
            <div class="form-group mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Guardar Producto
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
        nombre: '',
        categoria: '',
        descripcion: '',
        precio_actual: '',
        stock_actual: '',
        stock_minimo: '',
        
        init() {
            const form = document.getElementById('formProducto');
            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                
                if (!SIPAN.validateForm(form)) {
                    SIPAN.error('Por favor complete todos los campos requeridos');
                    return;
                }
                
                const data = await SIPAN.submitForm(form, (response) => {
                    // Preguntar si quiere crear la receta ahora
                     Swal.fire({
                        title: 'Producto creado',
                        text: '¿Deseas crear la receta para este producto ahora?',
                        icon: 'success',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Sí, crear receta',
                        cancelButtonText: 'No, volver a lista'
                    }).then((result) => {
                        if (result.isConfirmed) {
                             // Redirigir a crear receta pasando el producto ID y nombre
                            window.location.href = `/recetas/create?producto_id=${response.id}&nombre=${encodeURIComponent(response.nombre || '')}`;
                        } else {
                            window.location.href = '/productos';
                        }
                    });
                });
            });
        }
    }
}
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
