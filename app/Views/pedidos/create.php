<?php
$pageTitle = 'Nuevo Pedido';
$currentPage = 'pedidos';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="page-header">
    <h2 class="page-title">Nuevo Pedido</h2>
    <p class="page-subtitle">Registrar un nuevo pedido de cliente</p>
</div>

<div class="card" x-data="pedidoApp()">
    <div class="card-header">
        <h3 class="card-title">Información del Pedido</h3>
        <a href="/pedidos" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>
    <div class="card-body">
        <form @submit.prevent="guardarPedido()">
            <!-- Información del Cliente y Fechas -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label">Cliente <span class="text-danger">*</span></label>
                        <select x-model="id_cliente" class="form-control" required>
                            <option value="">Seleccionar cliente</option>
                            <?php
                            require_once __DIR__ . '/../../Models/Cliente.php';
                            $clienteModel = new \App\Models\Cliente();
                            $clientes = $clienteModel->getBySucursal($_SESSION['sucursal_id']);
                            foreach ($clientes as $cliente) :
                                ?>
                            <option value="<?= $cliente['id'] ?>">
                                <?= htmlspecialchars($cliente['nombre'] . ' ' . $cliente['apellido']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label">Fecha de Entrega <span class="text-danger">*</span></label>
                        <input type="date" x-model="fecha_entrega" class="form-control" required :min="minDate">
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label">Hora de Entrega</label>
                        <input type="time" x-model="hora_entrega" class="form-control">
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
                            <div class="p-2" style="border-bottom: 1px solid #eee; cursor: pointer;"
                                 @click="agregarProducto(producto)">
                                <strong x-text="producto.nombre"></strong> - 
                                Precio: <span x-text="'$ ' + parseFloat(producto.precio_actual).toFixed(2)"></span>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
            
            <!-- Tabla de Productos del Pedido -->
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
                                <td x-text="'$ ' + parseFloat(item.precio).toFixed(2)"></td>
                                <td>
                                    <input type="number" 
                                           class="form-control" 
                                           style="width: 100px;"
                                           min="1"
                                           x-model="item.cantidad"
                                           @input="calcularTotal()">
                                </td>
                                <td><strong x-text="'$ ' + (item.precio * item.cantidad).toFixed(2)"></strong></td>
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
                            <td colspan="2"><strong class="text-success" style="font-size: 1.5rem;" x-text="'$ ' + total.toFixed(2)"></strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            
            <!-- Información de Pago -->
            <div class="row mb-4">
                <div class="col-md-12">
                    <h4>Información de Pago</h4>
                </div>
                
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label">Abono Inicial</label>
                        <input type="number" x-model="abono_inicial" class="form-control" step="0.01" min="0" :max="total" @input="calcularSaldo()">
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label">Saldo Pendiente</label>
                        <input type="text" :value="'$ ' + saldo_pendiente.toFixed(2)" class="form-control" readonly>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label">Estado de Pago</label>
                        <input type="text" :value="estado_pago_texto" class="form-control" readonly>
                    </div>
                </div>
            </div>
            
            <!-- Observaciones -->
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="form-group">
                        <label class="form-label">Observaciones / Detalles del Pedido</label>
                        <textarea x-model="observaciones" class="form-control" rows="3" placeholder="Ej: Decoración especial, mensaje personalizado, etc."></textarea>
                    </div>
                </div>
            </div>
            
            <!-- Botones de Acción -->
            <div class="d-flex justify-content-end gap-2">
                <a href="/pedidos" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancelar
                </a>
                <button type="submit" class="btn btn-primary" :disabled="items.length === 0 || !id_cliente || !fecha_entrega">
                    <i class="fas fa-check"></i> Guardar Pedido
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function pedidoApp() {
    return {
        id_cliente: '',
        fecha_entrega: '',
        hora_entrega: '',
        busqueda: '',
        resultados: [],
        items: [],
        total: 0,
        abono_inicial: 0,
        saldo_pendiente: 0,
        estado_pago_texto: 'Pendiente',
        observaciones: '',
        minDate: new Date().toISOString().split('T')[0],
        
        init() {
            // Establecer fecha mínima como hoy
            this.fecha_entrega = this.minDate;
        },
        
        async buscarProductos() {
            if (this.busqueda.length < 2) {
                this.resultados = [];
                return;
            }
            
            try {
                const response = await fetch(`/productos/search?q=${encodeURIComponent(this.busqueda)}`);
                const data = await response.json();
                
                if (data.success) {
                    this.resultados = data.productos;
                }
            } catch (error) {
                console.error('Error al buscar productos:', error);
            }
        },
        
        agregarProducto(producto) {
            const existe = this.items.find(item => item.id === producto.id);
            if (existe) {
                SIPAN.warning('Este producto ya está en el pedido');
                return;
            }
            
            this.items.push({
                id: producto.id,
                nombre: producto.nombre,
                precio: parseFloat(producto.precio_actual),
                cantidad: 1
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
            this.calcularSaldo();
        },
        
        calcularSaldo() {
            this.saldo_pendiente = this.total - parseFloat(this.abono_inicial || 0);
            
            if (this.saldo_pendiente <= 0) {
                this.estado_pago_texto = 'Pagado';
            } else if (this.abono_inicial > 0) {
                this.estado_pago_texto = 'Abonado';
            } else {
                this.estado_pago_texto = 'Pendiente';
            }
        },
        
        async guardarPedido() {
            if (this.items.length === 0) {
                SIPAN.error('Debe agregar al menos un producto');
                return;
            }
            
            if (!this.id_cliente) {
                SIPAN.error('Debe seleccionar un cliente');
                return;
            }
            
            if (!this.fecha_entrega) {
                SIPAN.error('Debe especificar la fecha de entrega');
                return;
            }
            
            const formData = new FormData();
            formData.append('id_cliente', this.id_cliente);
            formData.append('fecha_entrega', this.fecha_entrega + (this.hora_entrega ? ' ' + this.hora_entrega : ' 12:00:00'));
            formData.append('total', this.total);
            formData.append('abono_inicial', this.abono_inicial || 0);
            formData.append('observaciones', this.observaciones);
            formData.append('productos', JSON.stringify(this.items));
            
            try {
                const response = await fetch('/pedidos/store', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    SIPAN.success(data.message);
                    setTimeout(() => {
                        window.location.href = '/pedidos/show/' + data.pedido_id;
                    }, 1500);
                } else {
                    SIPAN.error(data.message);
                }
            } catch (error) {
                SIPAN.error('Error al guardar el pedido');
                console.error('Error:', error);
            }
        }
    }
}
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
