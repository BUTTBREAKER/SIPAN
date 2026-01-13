<?php 
$pageTitle = 'Nuevo Proveedor';
$currentPage = 'proveedores';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="page-header">
    <h2 class="page-title">Registrar Proveedor</h2>
    <p class="page-subtitle">Agrega proveedores y asocia sus insumos suministrados</p>
</div>

<div class="card" x-data="proveedorApp()"  x-init="init()">
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
                            <th>Agregar</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($insumos as $i): ?>
                            <tr x-show="filtrar('<?= strtolower($i['nombre']) ?>')">
                                <td><?= htmlspecialchars($i['nombre']) ?></td>
                                <td><input type="number" min="0" step="0.01" x-model="precios[<?= $i['id'] ?>]" class="form-control"></td>
                                <td><input type="text" x-model="entregas[<?= $i['id'] ?>]" placeholder="Ej: 3 días" class="form-control"></td>
                                <td>
                                    <input type="checkbox" x-model="seleccionados" value="<?= $i['id'] ?>">
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>

                </table>
            </div>

            <button class="btn btn-warning mt-4">
                <i class="fas fa-save"></i> Guardar Proveedor
            </button>

        </form>
    </div>
</div>

<script>
function proveedorApp() {
    return {
        nombre: "",
        rif: "",
        telefono: "",
        correo: "",
        direccion: "",
        observaciones: "",
        busqueda: "",
        seleccionados: [],
        precios: {},
        entregas: {},

        // ----------------------------
        // FILTRAR INSUMOS
        // ----------------------------
        filtrar(nombre) {
            return nombre.includes(this.busqueda.toLowerCase());
        },

        // ----------------------------
        // PREFILL DESDE URL
        // ----------------------------
        init() {
            const urlParams = new URLSearchParams(window.location.search);
            const pre = urlParams.get('prefill_insumo');

            if (pre) {
                const id = parseInt(pre);

                if (!this.seleccionados.includes(id)) {
                    this.seleccionados.push(id);
                }

                setTimeout(() => {
                    const el = document.querySelector(
                        `input[type=checkbox][value="${id}"]`
                    );

                    if (el) {
                        el.checked = true;
                        el.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                }, 150);
            }
        },

        // ----------------------------
        // GUARDAR PROVEEDOR
        // ----------------------------
        async guardar() {
            if (!this.nombre) {
                SIPAN.error("Debe colocar el nombre del proveedor");
                return;
            }

            const insumos = this.seleccionados.map(id => ({
                id_insumo: id,
                precio: this.precios[id] ?? 0,
                tiempo_entrega: this.entregas[id] ?? ""
            }));

            const formData = new FormData();
            formData.append("nombre", this.nombre);
            formData.append("rif", this.rif);
            formData.append("telefono", this.telefono);
            formData.append("correo", this.correo);
            formData.append("direccion", this.direccion);
            formData.append("observaciones", this.observaciones);
            formData.append("insumos", JSON.stringify(insumos));

            const res = await fetch("/proveedores/store", {
                method: "POST",
                body: formData
            });

            const data = await res.json();

            if (data.success) {
                SIPAN.success(data.message);
                setTimeout(() => window.location.href = '/proveedores', 1200);
            } else {
                SIPAN.error(data.message);
            }
        }
    };
}
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
