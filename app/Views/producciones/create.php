<?php
$pageTitle = 'Nueva Producción';
$currentPage = 'producciones';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="page-header">
    <h2 class="page-title">Nueva Producción</h2>
    <p class="page-subtitle">Registrar un nuevo lote de producción</p>
</div>

<div class="card" x-data="produccionApp()">
    <div class="card-header">
        <h3 class="card-title">Información de la Producción</h3>
        <a href="/producciones" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>
    <div class="card-body">
        <form @submit.prevent="guardarProduccion()">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Producto <span class="text-danger">*</span></label>
                        <select x-model="id_producto" @change="cargarReceta()" class="form-control" required>
                            <option value="">Seleccionar producto</option>
                            <?php foreach ($productos as $producto): ?>
                                <option value="<?= $producto['id'] ?>">
                                    <?= htmlspecialchars($producto['nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>

                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Cantidad a Producir <span class="text-danger">*</span></label>
                        <input type="number" x-model="cantidad_producida" @input="calcularInsumos()" class="form-control" required min="1">
                    </div>
                </div>
            </div>

            <div x-show="receta_cargada" class="alert alert-info">
                <strong>Receta encontrada:</strong> Rendimiento de <span x-text="rendimiento"></span> unidades
            </div>

            <div x-show="!receta_cargada && id_producto" class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i> Este producto no tiene una receta asociada. La producción se registrará sin consumo de insumos.
            </div>

            <!-- Insumos Necesarios -->
            <div x-show="insumos_necesarios.length > 0">
                <h4 class="mb-3">Insumos Necesarios</h4>
                <div class="table-responsive mb-4">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Insumo</th>
                                <th>Cantidad Necesaria</th>
                                <th>Unidad</th>
                                <th>Stock Disponible</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="insumo in insumos_necesarios" :key="insumo.id">
                                <tr>
                                    <td x-text="insumo.nombre"></td>
                                    <td><strong x-text="insumo.cantidad_necesaria"></strong></td>
                                    <td x-text="insumo.unidad"></td>
                                    <td x-text="insumo.stock_disponible"></td>
                                    <td>
                                        <span x-show="insumo.stock_disponible >= insumo.cantidad_necesaria" class="badge badge-success">
                                            Suficiente
                                        </span>
                                        <span x-show="insumo.stock_disponible < insumo.cantidad_necesaria" class="badge badge-danger">
                                            Insuficiente
                                        </span>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label class="form-label">Observaciones</label>
                        <textarea x-model="observaciones" class="form-control" rows="3"></textarea>
                    </div>
                </div>
            </div> -->

            <div class="form-group mt-4">
                <button type="submit" class="btn btn-warning" :disabled="!puede_producir">
                    <i class="fas fa-industry"></i> Registrar Producción
                </button>
                <a href="/producciones" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancelar
                </a>
            </div>

            <div x-show="!puede_producir && insumos_necesarios.length > 0" class="alert alert-danger mt-3">
                <i class="fas fa-exclamation-circle"></i> No hay suficientes insumos para esta producción
            </div>
        </form>
    </div>
</div>

<script>
    function produccionApp() {
        return {
            id_producto: '',
            cantidad_producida: '',
            observaciones: '',
            receta_cargada: false,
            rendimiento: 0,
            insumos_necesarios: [],
            puede_producir: true,

            async cargarReceta() {
                if (!this.id_producto) return;

                try {
                    const response = await fetch(`/recetas/calcular?id_producto=${this.id_producto}`);
                    const data = await response.json();

                    if (data.success && data.receta) {
                        this.receta_cargada = true;
                        this.rendimiento = data.receta.rendimiento;
                        this.calcularInsumos();
                    } else {
                        this.receta_cargada = false;
                        this.insumos_necesarios = [];
                        this.puede_producir = true;
                    }
                } catch (error) {
                    console.error('Error al cargar receta:', error);
                    this.receta_cargada = false;
                }
            },

            async calcularInsumos() {
                if (!this.receta_cargada || !this.cantidad_producida) return;

                try {
                    const response = await fetch(`/recetas/calcular?id_producto=${this.id_producto}&cantidad=${this.cantidad_producida}`);
                    const data = await response.json();

                    console.log('Respuesta de calcular insumos:', data); // <-- AGREGAR ESTO

                    if (data.success) {
                        this.insumos_necesarios = data.insumos;
                        this.puede_producir = data.puede_producir;

                        console.log('Insumos necesarios:', this.insumos_necesarios); // <-- Y ESTO
                    }
                } catch (error) {
                    console.error('Error al calcular insumos:', error);
                }
            },

            async guardarProduccion() {
                if (!this.id_producto || !this.cantidad_producida) {
                    SIPAN.error('Debe completar todos los campos requeridos');
                    return;
                }

                if (!this.puede_producir && this.insumos_necesarios.length > 0) {
                    SIPAN.error('No hay suficientes insumos para esta producción');
                    return;
                }

                // Formatear insumos para asegurar que tengan la estructura correcta
                const insumosFormateados = this.insumos_necesarios.map(insumo => ({
                    id_insumo: insumo.id_insumo || insumo.id, // Por si viene como 'id' en lugar de 'id_insumo'
                    cantidad_utilizada: insumo.cantidad_necesaria
                }));

                console.log('Insumos formateados a enviar:', insumosFormateados); // <-- LOG IMPORTANTE

                const formData = new FormData();
                formData.append('id_producto', this.id_producto);
                formData.append('cantidad_producida', this.cantidad_producida);
                formData.append('insumos', JSON.stringify(insumosFormateados));

                try {
                    const response = await fetch('/producciones/store', {
                        method: 'POST',
                        body: formData
                    });

                    const data = await response.json();

                    console.log('Respuesta del servidor:', data); // <-- LOG IMPORTANTE

                    if (data.success) {
                        SIPAN.success(data.message);
                        setTimeout(() => {
                            window.location.href = '/producciones';
                        }, 1500);
                    } else {
                        SIPAN.error(data.message);
                    }
                } catch (error) {
                    SIPAN.error('Error al registrar la producción');
                    console.error('Error:', error);
                }
            }


        }
    }
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>