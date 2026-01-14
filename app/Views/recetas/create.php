<?php
$pageTitle = 'Nueva Receta';
$currentPage = 'recetas';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="page-header">
    <h2 class="page-title">Nueva Receta</h2>
    <p class="page-subtitle">Crear una nueva receta de producción</p>
</div>

<div class="card" x-data="recetaApp()">
    <div class="card-header">
        <h3 class="card-title">Información de la Receta</h3>
        <a href="/recetas" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>
    <div class="card-body">
        <form @submit.prevent="guardarReceta()">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Producto <span class="text-danger">*</span></label>
                        <select x-model="id_producto" class="form-control" required>
                            <option value="">Seleccionar producto</option>
                            <?php foreach ($productos as $producto) : ?>
                                <option value="<?= $producto['id'] ?>">
                                    <?= htmlspecialchars($producto['nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>

                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Rendimiento (unidades) <span class="text-danger">*</span></label>
                        <input type="number" x-model="rendimiento" class="form-control" required min="1">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label class="form-label">Instrucciones de Preparación</label>
                        <textarea x-model="instrucciones" class="form-control" rows="4"></textarea>
                    </div>
                </div>
            </div>

            <hr>

            <h4 class="mb-3">Insumos de la Receta</h4>

            <!-- Agregar Insumo -->
            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Seleccionar Insumo</label>
                        <select x-model="insumo_seleccionado" class="form-control">
                            <option value="">Seleccionar insumo</option>
                            <?php foreach ($insumos as $insumo) : ?>
                                <option value="<?= $insumo['id'] ?>"
                                    data-nombre="<?= htmlspecialchars($insumo['nombre']) ?>"
                                    data-unidad="<?= htmlspecialchars($insumo['unidad_medida']) ?>">
                                    <?= htmlspecialchars($insumo['nombre']) ?> (<?= $insumo['unidad_medida'] ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>

                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label">Cantidad</label>
                        <input type="number" x-model="cantidad_insumo" class="form-control" step="0.01" min="0.01">
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="form-group">
                        <label class="form-label">&nbsp;</label>
                        <button type="button" @click="agregarInsumo()" class="btn btn-success w-100">
                            <i class="fas fa-plus"></i> Agregar
                        </button>
                    </div>
                </div>
            </div>

            <!-- Lista de Insumos -->
            <div class="table-responsive mb-4">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Insumo</th>
                            <th>Cantidad</th>
                            <th>Unidad</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-if="insumos_receta.length === 0">
                            <tr>
                                <td colspan="4" class="text-center">No hay insumos agregados</td>
                            </tr>
                        </template>
                        <template x-for="(insumo, index) in insumos_receta" :key="index">
                            <tr>
                                <td x-text="insumo.nombre"></td>
                                <td x-text="insumo.cantidad"></td>
                                <td x-text="insumo.unidad"></td>
                                <td>
                                    <button type="button" @click="eliminarInsumo(index)" class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-primary" :disabled="insumos_receta.length === 0">
                    <i class="fas fa-save"></i> Guardar Receta
                </button>
                <a href="/recetas" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancelar
                </a>
            </div>
        </form>
    </div>
</div>

<script>
    function recetaApp() {
        return {
            id_producto: '',
            rendimiento: '',
            instrucciones: '',
            insumo_seleccionado: '',
            cantidad_insumo: '',
            insumos_receta: [],

            init() {
                const urlParams = new URLSearchParams(window.location.search);
                const prodId = urlParams.get('producto_id');
                if (prodId) {
                    this.id_producto = prodId;
                    // Opcional: Deshabilitar el select si queremos forzarlo, o dejarlo libre
                    // Si el nombre viene en URL, podríamos usarlo para mostrar un título mejor, 
                    // pero el select ya debería seleccionarse si el value coincide.
                }
            },
            
            agregarInsumo() {
                if (!this.insumo_seleccionado || !this.cantidad_insumo) {
                    SIPAN.error('Debe seleccionar un insumo y especificar la cantidad');
                    return;
                }

                // Verificar si ya está agregado
                const existe = this.insumos_receta.find(i => i.id === this.insumo_seleccionado);
                if (existe) {
                    SIPAN.warning('Este insumo ya está en la receta');
                    return;
                }

                // Obtener datos del select
                const select = document.querySelector('select[x-model="insumo_seleccionado"]');
                const option = select.options[select.selectedIndex];

                this.insumos_receta.push({
                    id: this.insumo_seleccionado,
                    nombre: option.getAttribute('data-nombre'),
                    cantidad: parseFloat(this.cantidad_insumo),
                    unidad: option.getAttribute('data-unidad')
                });

                this.insumo_seleccionado = '';
                this.cantidad_insumo = '';
            },

            eliminarInsumo(index) {
                this.insumos_receta.splice(index, 1);
            },

            async guardarReceta() {
                if (!this.id_producto || !this.rendimiento) {
                    SIPAN.error('Debe completar todos los campos requeridos');
                    return;
                }

                if (this.insumos_receta.length === 0) {
                    SIPAN.error('Debe agregar al menos un insumo a la receta');
                    return;
                }

                const formData = new FormData();
                formData.append('id_producto', this.id_producto);
                formData.append('rendimiento', this.rendimiento);
                formData.append('instrucciones', this.instrucciones);
                formData.append('insumos', JSON.stringify(this.insumos_receta));

                try {
                    const response = await fetch('/recetas/store', {
                        method: 'POST',
                        body: formData
                    });

                    const data = await response.json();

                    if (data.success) {
                        SIPAN.success(data.message);
                        setTimeout(() => {
                            window.location.href = '/recetas';
                        }, 1500);
                    } else {
                        SIPAN.error(data.message);
                    }
                } catch (error) {
                    SIPAN.error('Error al guardar la receta');
                    console.error('Error:', error);
                }
            }
        }
    }
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
