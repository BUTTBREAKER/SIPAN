<?php
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/sidebar.php';
?>

<div class="main-content" x-data="editRecetaApp(<?= htmlspecialchars(json_encode($receta)) ?>, <?= htmlspecialchars(json_encode($insumos_disponibles)) ?>, <?= htmlspecialchars(json_encode($receta_insumos)) ?>)">
    <div class="content-header">
        <h1><i class="fas fa-book"></i> Editar Receta</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="/recetas">Recetas</a></li>
                <li class="breadcrumb-item active">Editar</li>
            </ol>
        </nav>
    </div>

    <div class="card">
        <div class="card-body">
            <form @submit.prevent="handleSubmit()">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nombre de la Receta <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" x-model="formData.nombre" required>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Rendimiento <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" x-model="formData.rendimiento" required step="0.01" min="0">
                        <small class="text-muted">Cantidad de unidades que produce esta receta</small>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Instrucciones</label>
                    <textarea class="form-control" x-model="formData.instrucciones" rows="4" placeholder="Pasos para preparar la receta..."></textarea>
                </div>
                
                <hr>
                
                <h5 class="mb-3">Insumos de la Receta</h5>
                
                <div class="mb-3">
                    <button type="button" @click="agregarInsumo()" class="btn btn-success">
                        <i class="fas fa-plus"></i> Agregar Insumo
                    </button>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Insumo</th>
                                <th>Cantidad</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(insumo, index) in insumos" :key="index">
                                <tr>
                                    <td>
                                        <select class="form-select" x-model="insumo.id_insumo" required>
                                            <option value="">Seleccionar insumo</option>
                                            <template x-for="ins in insumosDisponibles" :key="ins.id">
                                                <option :value="ins.id" x-text="`${ins.nombre} (${ins.unidad_medida})`"></option>
                                            </template>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" class="form-control" x-model="insumo.cantidad" required step="0.01" min="0.01">
                                    </td>
                                    <td>
                                        <button type="button" @click="eliminarInsumo(index)" class="btn btn-danger btn-sm">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                            <tr x-show="insumos.length === 0">
                                <td colspan="3" class="text-center text-muted">No hay insumos agregados</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Guardar Cambios
                    </button>
                    <a href="/recetas" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editRecetaApp(receta, insumosDisponibles, recetaInsumos) {
    return {
        formData: {
            nombre: receta.nombre,
            rendimiento: receta.rendimiento,
            instrucciones: receta.instrucciones || ''
        },
        insumos: recetaInsumos.map(ri => ({
            id_insumo: ri.id_insumo,
            cantidad: ri.cantidad
        })),
        insumosDisponibles: insumosDisponibles,
        
        agregarInsumo() {
            this.insumos.push({
                id_insumo: '',
                cantidad: ''
            });
        },
        
        eliminarInsumo(index) {
            this.insumos.splice(index, 1);
        },
        
        async handleSubmit() {
            if (this.insumos.length === 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Debe agregar al menos un insumo',
                    confirmButtonColor: '#D4A574'
                });
                return;
            }
            
            const data = {
                ...this.formData,
                insumos: this.insumos
            };
            
            try {
                const response = await fetch('/recetas/update/<?= $receta['id'] ?>', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify(data)
                });
                
                const result = await response.json();
                
                if (result.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Éxito',
                        text: 'Receta actualizada correctamente',
                        confirmButtonColor: '#D4A574'
                    }).then(() => {
                        window.location.href = '/recetas';
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: result.message || 'Error al actualizar receta',
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
    };
}
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

