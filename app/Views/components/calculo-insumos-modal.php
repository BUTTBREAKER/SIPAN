<!-- Modal de Cálculo de Insumos -->
<div x-data="calculoInsumosModal()" x-init="init()" @abrir-calculo-insumos.window="abrirModal()">
    <!-- Botón para abrir modal (puede ser incluido en cualquier vista) -->
    <button @click="abrirModal()" class="btn btn-info">
        <i class="fas fa-calculator"></i> Calcular Insumos
    </button>
    
    <!-- Modal -->
    <div x-show="showModal" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="modal-overlay" 
         style="display: none;"
         @click.self="cerrarModal()">
        
        <div class="modal-dialog modal-lg" @click.stop>
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-calculator"></i> Calcular Insumos Necesarios
                    </h5>
                    <button type="button" class="btn-close" @click="cerrarModal()"></button>
                </div>
                
                <div class="modal-body">
                    <!-- Formulario de cálculo -->
                    <div x-show="!resultado">
                        <div class="mb-3">
                            <label class="form-label">Receta <span class="text-danger">*</span></label>
                            <select class="form-select" x-model="formData.id_receta">
                                <option value="">Seleccionar receta</option>
                                <template x-for="receta in recetas" :key="receta.id">
                                    <option :value="receta.id" x-text="`${receta.nombre} (Rinde: ${receta.rendimiento} unidades)`"></option>
                                </template>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Cantidad a Producir <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" x-model="formData.cantidad_producir" 
                                   min="1" step="1" placeholder="Ej: 100">
                            <small class="text-muted">Número de unidades que desea producir</small>
                        </div>
                        
                        <button @click="calcular()" class="btn btn-primary w-100" :disabled="loading">
                            <span x-show="!loading">
                                <i class="fas fa-calculator"></i> Calcular
                            </span>
                            <span x-show="loading">
                                <i class="fas fa-spinner fa-spin"></i> Calculando...
                            </span>
                        </button>
                    </div>
                    
                    <!-- Resultados -->
                    <div x-show="resultado">
                        <!-- Resumen -->
                        <div class="alert" :class="resultado?.puede_producir ? 'alert-success' : 'alert-warning'">
                            <h6 class="alert-heading">
                                <i :class="resultado?.puede_producir ? 'fas fa-check-circle' : 'fas fa-exclamation-triangle'"></i>
                                <span x-text="resultado?.puede_producir ? 'Producción Posible' : 'Insumos Insuficientes'"></span>
                            </h6>
                            <p class="mb-0">
                                Receta: <strong x-text="resultado?.receta?.nombre"></strong><br>
                                Cantidad a producir: <strong x-text="resultado?.cantidad_producir"></strong> unidades<br>
                                Factor de multiplicación: <strong x-text="resultado?.factor?.toFixed(2)"></strong>
                            </p>
                        </div>
                        
                        <!-- Tabla de insumos necesarios -->
                        <h6 class="mt-4">Insumos Necesarios</h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered">
                                <thead>
                                    <tr>
                                        <th>Insumo</th>
                                        <th>Necesario</th>
                                        <th>Stock Actual</th>
                                        <th>Estado</th>
                                        <th>Costo</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="insumo in resultado?.insumos_necesarios" :key="insumo.id_insumo">
                                        <tr :class="!insumo.suficiente ? 'table-danger' : ''">
                                            <td x-text="insumo.nombre"></td>
                                            <td>
                                                <span x-text="insumo.cantidad_necesaria.toFixed(2)"></span>
                                                <span x-text="insumo.unidad_medida"></span>
                                            </td>
                                            <td>
                                                <span x-text="insumo.stock_actual.toFixed(2)"></span>
                                                <span x-text="insumo.unidad_medida"></span>
                                            </td>
                                            <td>
                                                <span x-show="insumo.suficiente" class="badge bg-success">Suficiente</span>
                                                <span x-show="!insumo.suficiente" class="badge bg-danger">Insuficiente</span>
                                            </td>
                                            <td>$ <span x-text="insumo.costo_total.toFixed(2)"></span></td>
                                        </tr>
                                    </template>
                                </tbody>
                                <tfoot>
                                    <tr class="table-info">
                                        <th colspan="4">Costo Total de Producción</th>
                                        <th>$ <span x-text="resultado?.costo_total?.toFixed(2)"></span></th>
                                    </tr>
                                    <tr class="table-success">
                                        <th colspan="4">Costo por Unidad</th>
                                        <th>$ <span x-text="resultado?.costo_por_unidad?.toFixed(2)"></span></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        
                        <!-- Insumos faltantes (si hay) -->
                        <div x-show="resultado?.insumos_faltantes?.length > 0" class="mt-3">
                            <h6 class="text-danger">Insumos Faltantes</h6>
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    <template x-for="faltante in resultado?.insumos_faltantes" :key="faltante.nombre">
                                        <li>
                                            <strong x-text="faltante.nombre"></strong>: 
                                            Faltan <span x-text="faltante.faltante.toFixed(2)"></span> 
                                            <span x-text="faltante.unidad"></span>
                                        </li>
                                    </template>
                                </ul>
                            </div>
                        </div>
                        
                        <div class="d-flex gap-2 mt-3">
                            <button @click="resultado = null" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Nuevo Cálculo
                            </button>
                            <button @click="exportarPDF()" class="btn btn-info">
                                <i class="fas fa-file-pdf"></i> Exportar PDF
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 1rem;
}

.modal-dialog {
    background: white;
    border-radius: 10px;
    max-width: 800px;
    width: 100%;
    max-height: 90vh;
    overflow-y: auto;
}

.modal-content {
    padding: 0;
}

.modal-header {
    padding: 1.5rem;
    border-bottom: 1px solid #dee2e6;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-body {
    padding: 1.5rem;
}

.btn-close {
    background: none;
    border: none;
    font-size: 1.5rem;
    cursor: pointer;
    opacity: 0.5;
}

.btn-close:hover {
    opacity: 1;
}
</style>

<script src="/assets/js/calculo-insumos.js"></script>

