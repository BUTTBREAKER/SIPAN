<?php
$pageTitle = 'Registrar Compra';
$currentPage = 'compras';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="page-header">
    <h2 class="page-title">Registrar Entrada de Mercadería</h2>
</div>

<div class="row" x-data="compraApp()">
    <div class="col-md-12">
        <form @submit.prevent="guardarCompra()">
            <!-- Encabezado Compra -->
            <div class="card mb-3">
                <div class="card-header bg-light">
                    <h3 class="card-title h6 mb-0">Datos Generales</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">Proveedor <span class="text-danger">*</span></label>
                                <select x-model="id_proveedor" class="form-select" required>
                                    <option value="">Seleccionar Proveedor</option>
                                    <?php foreach ($proveedores as $prov) : ?>
                                    <option value="<?= $prov['id'] ?>"><?= htmlspecialchars($prov['nombre']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">N° Comprobante/Factura</label>
                                <input type="text" x-model="numero_comprobante" class="form-control" placeholder="F001-000123">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">Fecha Compra</label>
                                <input type="date" x-model="fecha_compra" class="form-control" value="<?= date('Y-m-d') ?>">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detalle Items -->
            <div class="card mb-3">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h3 class="card-title h6 mb-0">Detalle de Insumos</h3>
                </div>
                <div class="card-body">
                    <!-- Buscador y Agregador -->
                    <div class="row gx-2 align-items-end mb-3 p-3 bg-light rounded border">
                        <div class="col-md-4">
                            <label class="form-label small">Insumo</label>
                            <select x-model="item_temp.id" class="form-select" id="selectInsumo">
                                <option value="">Seleccionar Insumo...</option>
                                <?php foreach ($insumos as $ins) : ?>
                                <option value="<?= $ins['id'] ?>" 
                                        data-nombre="<?= htmlspecialchars($ins['nombre']) ?>"
                                        data-costo="<?= $ins['costo_unitario'] ?? 0 ?>">
                                    <?= htmlspecialchars($ins['nombre']) ?> (<?= $ins['unidad_medida'] ?>)
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                             <label class="form-label small">Costo Unit.</label>
                             <input type="number" x-model="item_temp.costo" class="form-control" step="0.01" min="0">
                        </div>
                         <div class="col-md-2">
                             <label class="form-label small">Cantidad</label>
                             <input type="number" x-model="item_temp.cantidad" class="form-control" step="0.01" min="0.01">
                        </div>
                         <div class="col-md-3">
                             <label class="form-label small">Lote/Vencimiento (Opcional)</label>
                             <div class="input-group">
                                <input type="text" x-model="item_temp.lote" class="form-control" placeholder="Lote #">
                                <input type="date" x-model="item_temp.vencimiento" class="form-control" title="Fecha Vencimiento">
                             </div>
                        </div>
                        <div class="col-md-1">
                            <button type="button" class="btn btn-success w-100" @click="agregarItem()">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Tabla -->
                    <table class="table table-bordered table-sm">
                        <thead class="table-light">
                            <tr>
                                <th>Insumo</th>
                                <th>Lote / Venc.</th>
                                <th>Cantidad</th>
                                <th>Costo Unit.</th>
                                <th>Subtotal</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-if="items.length === 0">
                                <tr><td colspan="6" class="text-center text-muted py-3">No hay items agregados</td></tr>
                            </template>
                             <template x-for="(item, index) in items" :key="index">
                                <tr>
                                    <td x-text="item.nombre"></td>
                                    <td>
                                        <div x-show="item.lote || item.vencimiento">
                                            <small class="d-block" x-show="item.lote">Lote: <span x-text="item.lote"></span></small>
                                            <small class="d-block text-danger" x-show="item.vencimiento">Vence: <span x-text="item.vencimiento"></span></small>
                                        </div>
                                        <span x-show="!item.lote && !item.vencimiento" class="text-muted">-</span>
                                    </td>
                                    <td x-text="item.cantidad"></td>
                                    <td x-text="'$ ' + parseFloat(item.costo).toFixed(2)"></td>
                                    <td x-text="'$ ' + (item.cantidad * item.costo).toFixed(2)"></td>
                                    <td class="text-end">
                                        <button type="button" class="btn btn-sm btn-danger py-0" @click="removerItem(index)">&times;</button>
                                    </td>
                                </tr>
                             </template>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="4" class="text-end"><strong>TOTAL COMPRA:</strong></td>
                                <td colspan="2"><strong class="text-primary h5" x-text="'$ ' + total.toFixed(2)"></strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            
            <div class="text-end">
                <a href="/compras" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary btn-lg" :disabled="items.length === 0 || !id_proveedor">
                    <i class="fas fa-save"></i> Registrar Compra
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function compraApp() {
    return {
        id_proveedor: '',
        numero_comprobante: '',
        fecha_compra: new Date().toISOString().split('T')[0],
        items: [],
        item_temp: {
            id: '',
            nombre: '',
            costo: '',
            cantidad: '',
            lote: '',
            vencimiento: ''
        },
        
        get total() {
             return this.items.reduce((sum, i) => sum + (i.cantidad * i.costo), 0);
        },

        init() {
            // Watch para actualizar costo sugerido al cambiar insumo
            const select = document.getElementById('selectInsumo');
            select.addEventListener('change', () => {
                const option = select.options[select.selectedIndex];
                const costo = option.getAttribute('data-costo');
                const nombre = option.getAttribute('data-nombre');
                
                this.item_temp.id = select.value;
                this.item_temp.nombre = nombre;
                // Sugerir ultimo costo
                if(costo) this.item_temp.costo = costo;
            });
        },

        agregarItem() {
            if(!this.item_temp.id || !this.item_temp.cantidad || !this.item_temp.costo) {
                SIPAN.warning('Complete Insumo, Cantidad y Costo');
                return;
            }

            this.items.push({
                tipo_item: 'insumo', // Por defecto solo insumos por ahora
                id_item: this.item_temp.id,
                nombre: this.item_temp.nombre,
                cantidad: parseFloat(this.item_temp.cantidad),
                costo: parseFloat(this.item_temp.costo),
                lote_codigo: this.item_temp.lote,
                fecha_vencimiento: this.item_temp.vencimiento,
                subtotal: parseFloat(this.item_temp.cantidad) * parseFloat(this.item_temp.costo)
            });

            // Reset temp, dejar fechas si va a agregar lotes similares
            this.item_temp.cantidad = '';
            // this.item_temp.lote = ''; // A veces el lote es el mismo para todo
        },

        removerItem(index) {
            this.items.splice(index, 1);
        },

        async guardarCompra() {
            const payload = {
                id_proveedor: this.id_proveedor,
                numero_comprobante: this.numero_comprobante,
                fecha_compra: this.fecha_compra,
                total: this.total,
                detalles: JSON.stringify(this.items)
            };

            const formData = new FormData();
            for (const key in payload) {
                formData.append(key, payload[key]);
            }

            try {
                const response = await fetch('/compras/store', {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();
                
                if (data.success) {
                    SIPAN.success('Compra registrada correctamente');
                    setTimeout(() => window.location.href = '/compras', 1500);
                } else {
                    SIPAN.error(data.message);
                }
            } catch (e) {
                SIPAN.error('Error de conexión');
            }
        }
    }
}
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
