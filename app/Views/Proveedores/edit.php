<?php
$pageTitle = 'Editar Proveedor';
$currentPage = 'proveedores';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="page-header">
    <h2 class="page-title">Editar Proveedor</h2>
    <p class="page-subtitle">Modifica la información del proveedor y sus insumos asociados</p>
</div>

<div class="card" 
     x-data="proveedorEditApp(
         <?= htmlspecialchars(json_encode($proveedor ?: []), ENT_QUOTES) ?>,
         <?= htmlspecialchars(json_encode($insumos ?: []), ENT_QUOTES) ?>,
         <?= htmlspecialchars(json_encode($insumos_asociados ?: []), ENT_QUOTES) ?>
     )">

    <div class="card-header d-flex justify-between align-center">
        <h3 class="card-title">Información del Proveedor</h3>
        <a href="/proveedores" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Volver</a>
    </div>

    <div class="card-body">
        <form @submit.prevent="guardar()">

            <div class="row">
                <div class="col-md-6">
                    <label class="form-label">Nombre</label>
                    <input type="text" x-model="nombre" class="form-control" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label">RIF</label>
                    <input type="text" x-model="rif" class="form-control">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Teléfono</label>
                    <input type="text" x-model="telefono" class="form-control">
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-md-6">
                    <label class="form-label">Correo</label>
                    <input type="email" x-model="correo" class="form-control">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Dirección</label>
                    <input type="text" x-model="direccion" class="form-control">
                </div>
            </div>

            <div class="form-group mt-3">
                <label class="form-label">Observaciones</label>
                <textarea x-model="observaciones" rows="3" class="form-control"></textarea>
            </div>

            <hr>

            <h4>Insumos que suministra</h4>

            <input type="text" class="form-control mb-3" placeholder="Buscar insumo..."
                   x-model="busqueda">

            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Insumo</th>
                            <th>Precio</th>
                            <th>Tiempo de Entrega</th>
                            <th>Asociado</th>
                        </tr>
                    </thead>

                    <tbody>
                        <template x-for="ins in insumos">
                            <tr x-show="filtrar(ins.nombre)">
                                <td x-text="ins.nombre"></td>
                                <td><input type="number" step="0.01" x-model="precios[ins.id]" class="form-control"></td>
                                <td><input type="text" x-model="entregas[ins.id]" class="form-control"></td>
                                <td><input type="checkbox" x-model="seleccionados" :value="ins.id"></td>
                            </tr>
                        </template>
                    </tbody>

                </table>
            </div>

            <button class="btn btn-warning mt-4">
                <i class="fas fa-save"></i> Guardar Cambios
            </button>

        </form>
    </div>
</div>

<script>
function proveedorEditApp(proveedor, insumos, insumosAsociados) {
    return {
        // Datos principales
        id: proveedor.id,
        nombre: proveedor.nombre,
        rif: proveedor.rif,
        telefono: proveedor.telefono,
        correo: proveedor.correo,
        direccion: proveedor.direccion,
        observaciones: proveedor.observaciones,

        // Listas
        insumos: insumos,
        seleccionados: insumosAsociados.map(i => parseInt(i.id_insumo)),
        precios: {},
        entregas: {},
        busqueda: "",

        init() {
            // precargar precios y tiempos
            insumosAsociados.forEach(i => {
                this.precios[i.id_insumo] = i.precio;
                this.entregas[i.id_insumo] = i.tiempo_entrega;
            });
        },

        filtrar(nombre) {
            return nombre.toLowerCase().includes(this.busqueda.toLowerCase());
        },

        async guardar() {
            const insumos = this.seleccionados.map(id => ({
                id_insumo: id,
                precio: this.precios[id] ?? 0,
                tiempo_entrega: this.entregas[id] ?? ""
            }));

            const fd = new FormData();
            fd.append("nombre", this.nombre);
            fd.append("rif", this.rif);
            fd.append("telefono", this.telefono);
            fd.append("correo", this.correo);
            fd.append("direccion", this.direccion);
            fd.append("observaciones", this.observaciones);
            fd.append("insumos", JSON.stringify(insumos));

            const req = await fetch(`/proveedores/update/${this.id}`, {
                method: "POST",
                body: fd
            });

            const data = await req.json();

            if (data.success) {
                SIPAN.success(data.message);
                setTimeout(() => window.location.href = '/proveedores', 1200);
            } else {
                SIPAN.error(data.message);
            }
        }
    }
}
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
