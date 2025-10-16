<?php
$pageTitle = 'Nueva Venta';
$currentPage = 'ventas';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="page-header">
    <h2 class="page-title">Nueva Venta</h2>
    <p class="page-subtitle">Registrar una nueva venta</p>
</div>

<div class="card" x-data="ventaApp()">
    <div class="card-header">
        <h3 class="card-title">Punto de Venta</h3>
        <a href="./ventas" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>
    <div class="card-body">
        <form id="formVenta" @submit.prevent="procesarVenta()">
            <!-- Selección de Cliente -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Cliente (Opcional)</label>
                        <select name="id_cliente" class="form-control" x-model="id_cliente">
                            <option value="">Cliente General</option>
                            <?php
                            require_once __DIR__ . '/../../Models/Cliente.php';
                            $clienteModel = new \SIPAN\Models\Cliente();
                            $clientes = $clienteModel->getBySucursal($_SESSION['sucursal_id']);
                            foreach ($clientes as $cliente):
                            ?>
                            <option value="<?= $cliente['id'] ?>">
                                <?= htmlspecialchars($cliente['nombre'] . ' ' . $cliente['apellido']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Método de Pago <span class="text-danger">*</span></label>
                        <select name="metodo_pago" class="form-control" required x-model="metodo_pago">
                            <option value="">Seleccionar método</option>
                            <option value="efectivo">Efectivo</option>
                            <option value="tarjeta">Tarjeta</option>
                            <option value="transferencia">Transferencia</option>
                            <option value="yape">Yape</option>
                            <option value="plin">Plin</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Búsqueda y Selección de Productos -->
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="form-group">
                        <label class="form-label">Buscar Producto</label>
                        <div class="input-group">
                            <input type="text"
                                   class="form-control"
                                   placeholder="Buscar por nombre..."
                                   x-model="busqueda"
                                   @input.debounce.300ms="buscarProductos()">
                            <button type="button" class="btn btn-primary" @click="buscarProductos()">
                                <i class="fas fa-search"></i> Buscar
                            </button>
                        </div>
                    </div>

                    <!-- Resultados de búsqueda -->
                    <div x-show="resultados.length > 0" class="mt-2" style="max-height: 200px; overflow-y: auto; border: 1px solid #ddd; border-radius: 8px;">
                        <template x-for="producto in resultados" :key="producto.id">
                            <div class="p-2" style="border-bottom: 1px solid #eee; cursor: pointer;" onmouseover="this.style.backgroundColor='#f5f5f5'" onmouseout="this.style.backgroundColor=''"
                                 @click="agregarProducto(producto)">
                                <strong x-text="producto.nombre"></strong> -
                                Stock: <span x-text="producto.stock_actual"></span> -
                                Precio: <span x-text="'S/ ' + parseFloat(producto.precio_actual).toFixed(2)"></span>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Tabla de Productos Seleccionados -->
            <div class="table-responsive mb-4">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Precio</th>
                            <th>Cantidad</th>
                            <th>Subtotal</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-if="items.length === 0">
                            <tr>
                                <td colspan="5" class="text-center">No hay productos agregados</td>
                            </tr>
                        </template>
                        <template x-for="(item, index) in items" :key="index">
                            <tr>
                                <td x-text="item.nombre"></td>
                                <td x-text="'S/ ' + parseFloat(item.precio).toFixed(2)"></td>
                                <td>
                                    <input type="number"
                                           class="form-control"
                                           style="width: 100px;"
                                           min="1"
                                           :max="item.stock_disponible"
                                           x-model="item.cantidad"
                                           @input="calcularTotal()">
                                </td>
                                <td><strong x-text="'S/ ' + (item.precio * item.cantidad).toFixed(2)"></strong></td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-danger" @click="eliminarItem(index)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-end"><strong>TOTAL:</strong></td>
                            <td colspan="2"><strong class="text-success" style="font-size: 1.5rem;" x-text="'S/ ' + total.toFixed(2)"></strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Botones de Acción -->
            <div class="d-flex justify-content-end gap-2">
                <a href="./ventas" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancelar
                </a>
                <button type="submit" class="btn btn-success" :disabled="items.length === 0">
                    <i class="fas fa-check"></i> Procesar Venta
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function ventaApp() {
    return {
        id_cliente: '',
        metodo_pago: '',
        busqueda: '',
        resultados: [],
        items: [],
        total: 0,

        async buscarProductos() {
            if (this.busqueda.length < 2) {
                this.resultados = [];
                return;
            }

            try {
                const response = await fetch(`/productos/search?q=${encodeURIComponent(this.busqueda)}`);
                const data = await response.json();

                if (data.success) {
                    this.resultados = data.productos.filter(p => p.stock_actual > 0);
                }
            } catch (error) {
                console.error('Error al buscar productos:', error);
            }
        },

        agregarProducto(producto) {
            // Verificar si ya está en la lista
            const existe = this.items.find(item => item.id === producto.id);
            if (existe) {
                SIPAN.warning('Este producto ya está en la lista');
                return;
            }

            this.items.push({
                id: producto.id,
                nombre: producto.nombre,
                precio: parseFloat(producto.precio_actual),
                cantidad: 1,
                stock_disponible: producto.stock_actual
            });

            this.busqueda = '';
            this.resultados = [];
            this.calcularTotal();
        },

        eliminarItem(index) {
            this.items.splice(index, 1);
            this.calcularTotal();
        },

        calcularTotal() {
            this.total = this.items.reduce((sum, item) => {
                return sum + (item.precio * item.cantidad);
            }, 0);
        },

        async procesarVenta() {
            if (this.items.length === 0) {
                SIPAN.error('Debe agregar al menos un producto');
                return;
            }

            if (!this.metodo_pago) {
                SIPAN.error('Debe seleccionar un método de pago');
                return;
            }

            const formData = new FormData();
            formData.append('id_cliente', this.id_cliente);
            formData.append('metodo_pago', this.metodo_pago);
            formData.append('total', this.total);
            formData.append('productos', JSON.stringify(this.items));

            try {
                const response = await fetch(App::getUrl('ventas.store'), {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    SIPAN.success(data.message);
                    setTimeout(() => {
                        window.location.href = '/ventas/show/' + data.venta_id;
                    }, 1500);
                } else {
                    SIPAN.error(data.message);
                }
            } catch (error) {
                SIPAN.error('Error al procesar la venta');
                console.error('Error:', error);
            }
        }
    }
}
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
