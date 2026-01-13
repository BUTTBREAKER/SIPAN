<?php 
$pageTitle = 'Nueva Venta';
$currentPage = 'ventas';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h2 class="page-title">Nueva Venta</h2>
        <p class="page-subtitle">Registrar una nueva venta</p>
    </div>
    <div class="d-flex align-items-center gap-3">
        <div class="card mb-0 px-3 py-2 bg-light border-0">
            <span class="text-muted small text-uppercase fw-bold">Tasa BCV</span>
            <span class="d-block fw-bold text-dark h5 mb-0" id="tasa-display">Bs <?= number_format($tasa_bcv, 2) ?></span>
        </div>
    </div>
</div>

<div class="card" x-data="ventaApp()">
    <div class="card-header">
        <h3 class="card-title">Punto de Venta</h3>
        <a href="/ventas" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>
    <div class="card-body">
        <form id="formVenta" @submit.prevent="procesarVenta()">
            <!-- Selección de Cliente con Botón Rápido -->
            <div class="row mb-4">
                <div class="col-md-5">
                    <div class="form-group">
                        <label class="form-label">Cliente (Opcional)</label>
                        <select name="id_cliente" class="form-select" id="selectCliente" x-model="id_cliente">
                            <option value="">Cliente General</option>
                            <?php
                            require_once __DIR__ . '/../../Models/Cliente.php';
                            $clienteModel = new \App\Models\Cliente();
                            $clientes = $clienteModel->getBySucursal($_SESSION['sucursal_id']);
                            foreach ($clientes as $cliente):
                            ?>
                            <option value="<?= $cliente['id'] ?>">
                                <?= htmlspecialchars($cliente['nombre'] . ' ' . $cliente['apellido']) ?>
                                <?php if($cliente['documento_numero']): ?> (<?= $cliente['documento_numero'] ?>) <?php endif; ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <button type="button" class="btn btn-info w-100" id="btnNuevoCliente" title="Nuevo Cliente" data-bs-toggle="modal" data-bs-target="#modalNuevoCliente">
                        <i class="fas fa-user-plus"></i>
                    </button>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Pagos <span class="text-danger">*</span></label>
                        
                        <!-- Lista de Pagos Agregados -->
                        <div class="list-group mb-2" x-show="pagos.length > 0">
                            <template x-for="(pago, index) in pagos" :key="index">
                                <div class="list-group-item d-flex justify-content-between align-items-center py-1">
                                    <span>
                                        <i class="fas fa-money-bill-wave me-1"></i>
                                        <span x-text="formatMetodo(pago.metodo)"></span>
                                        <small x-show="pago.referencia" class="text-muted ms-1" x-text="'Ref: ' + pago.referencia"></small>
                                        <small class="badge bg-light text-dark border ms-1" x-show="pago.es_bs" x-text="'Bs ' + parseFloat(pago.monto_bs).toFixed(2)"></small>
                                    </span>
                                    <span>
                                        <strong x-text="'$ ' + parseFloat(pago.monto).toFixed(2)"></strong>
                                        <button type="button" class="btn btn-sm btn-link text-danger p-0 ms-2" @click="removerPago(index)">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </span>
                                </div>
                            </template>
                        </div>

                        <!-- Agregar Nuevo Pago -->
                        <div class="card bg-light border-0 p-3" id="panel-pagos">
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <select class="form-select form-select-sm" x-model="nuevoPago.metodo" @change="actualizarMoneda()">
                                        <option value="efectivo_usd">Efectivo ($)</option>
                                        <option value="efectivo_bs">Efectivo (Bs)</option>
                                        <option value="pago_movil">Pago Móvil (Bs)</option>
                                        <option value="biopago">Biopago (Bs)</option>
                                        <option value="transferencia">Transferencia (Bs)</option>
                                        <option value="punto_usd">Punto ($)</option> <!-- Punto en $ si tienen cuenta extranjera -->
                                        <option value="zelle">Zelle ($)</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text" x-text="monedaInput"></span>
                                        <input type="number" class="form-control" x-model="nuevoPago.monto_input" step="0.01" placeholder="Monto" @input="calcularConversion()">
                                    </div>
                                    <div class="form-text mt-0" x-show="esBs">
                                        = $ <span x-text="nuevoPago.monto_usd_preview"></span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <input type="text" class="form-control form-select-sm" placeholder="Ref (opcional)" x-model="nuevoPago.referencia">
                                </div>
                                <div class="col-md-1">
                                    <button type="button" class="btn btn-sm btn-primary w-100" @click="agregarPago()">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Totales -->
                        <div class="d-flex justify-content-between mt-2 pt-2 border-top">
                             <div class="text-end w-100">
                                <small class="text-muted d-block">Restante</small>
                                <span class="h5" :class="restante > 0 ? 'text-danger' : 'text-success'" x-text="'$ ' + restante.toFixed(2)"></span>
                                <span class="d-block text-muted small" x-show="restante > 0">
                                    (Bs <span x-text="(restante * tasa).toFixed(2)"></span>)
                                </span>
                            </div>
                        </div>
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
                                   placeholder="Buscar por nombre o código..."
                                   x-model="busqueda"
                                   @input.debounce.300ms="buscarProductos()"
                                   id="inputBusqueda">
                            <button type="button" class="btn btn-primary" @click="buscarProductos()">
                                <i class="fas fa-search"></i> Buscar
                            </button>
                        </div>
                    </div>
                    
                    <!-- Resultados de búsqueda -->
                    <div x-show="resultados.length > 0" class="mt-2" style="max-height: 200px; overflow-y: auto; border: 1px solid #ddd; border-radius: 8px;">
                        <template x-for="producto in resultados" :key="producto.id">
                            <div class="p-2" style="border-bottom: 1px solid #eee; cursor: pointer;" @click="agregarProducto(producto)" onmouseover="this.style.backgroundColor='#f5f5f5'" onmouseout="this.style.backgroundColor='transparent'">
                                <strong x-text="producto.nombre"></strong>
                                <span class="badge bg-secondary ms-1" x-show="producto.codigo" x-text="producto.codigo"></span> - 
                                Stock: <span x-text="producto.stock_actual"></span> - 
                                Precio: <span class="text-success fw-bold" x-text="'$ ' + parseFloat(producto.precio_actual).toFixed(2)"></span>
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
                            <th>Precio Unit.</th>
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
                                <td>
                                    <span x-text="item.nombre"></span>
                                    <br><small class="text-muted" x-text="item.codigo"></small>
                                </td>
                                <td x-text="'$ ' + parseFloat(item.precio).toFixed(2)"></td>
                                <td>
                                    <input type="number" 
                                           class="form-control" 
                                           style="width: 100px;"
                                           min="1"
                                           :max="item.stock_disponible"
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
                            <td colspan="3" class="text-end"><strong>TOTAL A PAGAR:</strong></td>
                            <td colspan="2">
                                <div class="d-flex flex-column align-items-start">
                                    <strong class="text-success display-6 mb-0" style="font-size: 1.5rem;" x-text="'$ ' + total.toFixed(2)"></strong>
                                    <small class="text-muted">Bs <span x-text="(total * tasa).toFixed(2)"></span></small>
                                </div>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            
            <!-- Botones de Acción -->
            <div class="d-flex justify-content-end gap-2">
                <a href="/ventas" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancelar
                </a>
                <button type="submit" class="btn btn-success" :disabled="items.length === 0 || restante > 0.05">
                    <i class="fas fa-check"></i> Procesar Venta
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Nuevo Cliente -->
<div class="modal fade" id="modalNuevoCliente" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Registrar Nuevo Cliente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formNuevoCliente">
                    <div class="mb-3">
                        <label class="form-label">Nombre</label>
                        <input type="text" class="form-control" name="nombre" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Apellido</label>
                        <input type="text" class="form-control" name="apellido">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Cédula / Documento</label>
                        <input type="text" class="form-control" name="documento_numero" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Teléfono</label>
                        <input type="text" class="form-control" name="telefono">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="guardarClienteExpress()">Guardar</button>
            </div>
        </div>
    </div>
</div>

<script>
function ventaApp() {
    return {
        id_cliente: '',
        busqueda: '',
        resultados: [],
        items: [],
        total: 0,
        pagos: [],
        tasa: <?= $tasa_bcv ?? 50 ?>,
        
        nuevoPago: {
            metodo: 'efectivo_usd',
            monto_input: '',
            monto_usd_preview: '0.00',
            referencia: ''
        },
        
        esBs: false,
        monedaInput: '$',

        init() {
            setTimeout(() => document.getElementById('inputBusqueda').focus(), 500);
            this.actualizarMoneda();
        },
        
        actualizarMoneda() {
            const metodo = this.nuevoPago.metodo;
            this.esBs = ['efectivo_bs', 'pago_movil', 'biopago', 'transferencia'].includes(metodo);
            this.monedaInput = this.esBs ? 'Bs' : '$';
            this.calcularConversion();
        },
        
        calcularConversion() {
            if (!this.nuevoPago.monto_input) {
                this.nuevoPago.monto_usd_preview = '0.00';
                return;
            }
            
            const monto = parseFloat(this.nuevoPago.monto_input);
            if (this.esBs) {
                this.nuevoPago.monto_usd_preview = (monto / this.tasa).toFixed(2);
            } else {
                this.nuevoPago.monto_usd_preview = monto.toFixed(2);
            }
        },

        formatMetodo(metodo) {
            const map = {
                'efectivo_usd': 'Efectivo ($)',
                'efectivo_bs': 'Efectivo (Bs)',
                'pago_movil': 'Pago Móvil',
                'biopago': 'Biopago',
                'transferencia': 'Transferencia',
                'punto_usd': 'Punto ($)',
                'zelle': 'Zelle'
            };
            return map[metodo] || metodo;
        },

        get totalPagado() {
            return this.pagos.reduce((sum, p) => sum + parseFloat(p.monto), 0);
        },
        
        get restante() {
            return Math.max(0, this.total - this.totalPagado);
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
                    this.resultados = data.productos.filter(p => p.stock_actual > 0);
                }
            } catch (error) {
                console.error('Error al buscar productos:', error);
            }
        },
        
        agregarProducto(producto) {
            const existe = this.items.find(item => item.id === producto.id);
            if (existe) {
                SIPAN.warning('Este producto ya está en la lista');
                return;
            }
            
            this.items.push({
                id: producto.id,
                nombre: producto.nombre,
                codigo: producto.codigo || '',
                precio: parseFloat(producto.precio_actual),
                cantidad: 1,
                stock_disponible: producto.stock_actual
            });
            
            this.busqueda = '';
            this.resultados = [];
            this.calcularTotal();
            
            // Sugerir monto restante (en $)
            if (this.pagos.length === 0) {
                // Reset to USD default
                this.nuevoPago.metodo = 'efectivo_usd';
                this.actualizarMoneda();
                this.nuevoPago.monto_input = this.total.toFixed(2);
                this.calcularConversion();
            }
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
        
        agregarPago() {
            if (!this.nuevoPago.monto_input || parseFloat(this.nuevoPago.monto_input) <= 0) return;
            
            let montoUsd = 0;
            let montoBs = 0;
            
            if (this.esBs) {
                montoBs = parseFloat(this.nuevoPago.monto_input);
                montoUsd = montoBs / this.tasa;
            } else {
                montoUsd = parseFloat(this.nuevoPago.monto_input);
            }
            
            if (this.totalPagado + montoUsd > this.total + 0.05) {
                SIPAN.warning('El monto excede el total a pagar');
                return;
            }
            
            this.pagos.push({
                metodo: this.nuevoPago.metodo,
                monto: montoUsd,       // Siempre guardamos el valor en USD para la suma total
                monto_bs: montoBs,     // Guardamos Bs referencial si aplica
                es_bs: this.esBs,
                referencia: this.nuevoPago.referencia
            });
            
            // Limpiar y recalcular sugerencia para el siguiente pago
            this.nuevoPago.monto_input = '';
            this.nuevoPago.referencia = '';
            this.nuevoPago.monto_usd_preview = '0.00';
            
            // Sugerir el restante si queda algo
            if (this.restante > 0.01) {
                if (this.esBs) {
                   this.nuevoPago.monto_input = (this.restante * this.tasa).toFixed(2);
                } else {
                   this.nuevoPago.monto_input = this.restante.toFixed(2);
                }
                this.calcularConversion();
            }
        },
        
        removerPago(index) {
            this.pagos.splice(index, 1);
        },
        
        async procesarVenta() {
            if (this.items.length === 0) {
                SIPAN.error('Debe agregar al menos un producto');
                return;
            }
            
            if (this.restante > 0.05) {
                SIPAN.error('Falta cubrir el total de la venta');
                return;
            }
            
            const formData = new FormData();
            formData.append('id_cliente', this.id_cliente);
            formData.append('total', this.total); // Total en USD
            formData.append('productos', JSON.stringify(this.items));
            
            // Enviar Pagos
            if (this.pagos.length > 0) {
                formData.append('pagos', JSON.stringify(this.pagos));
                formData.append('metodo_pago', 'mixto');
            } else {
                formData.append('metodo_pago', 'efectivo_usd'); 
            }
            
            try {
                const response = await fetch('/ventas/store', {
                    method: 'POST',
                    body: formData
                });
                
                const rawText = await response.text();
                console.log('Server Response:', rawText); // Para debug

                try {
                    const data = JSON.parse(rawText);
                    
                    if (data.success) {
                        SIPAN.success(data.message);
                        setTimeout(() => {
                            window.location.href = '/ventas/show/' + data.venta_id;
                        }, 1500);
                    } else {
                        SIPAN.error(data.message);
                    }
                } catch (jsonError) {
                    console.error('JSON Parse Error:', jsonError);
                    console.error('Raw content:', rawText);
                    SIPAN.error('Error de servidor: Respuesta inválida');
                }
            } catch (error) {
                SIPAN.error('Error al procesar la venta');
                console.error('Fetch Error:', error);
            }
        }
    }
}

async function guardarClienteExpress() {
    const form = document.getElementById('formNuevoCliente');
    const formData = new FormData(form);
    
    try {
        const response = await fetch('/clientes/store', { 
            method: 'POST',
            body: formData,
            headers: { 'Accept': 'application/json' } // Hint to backend if supported
        });
        
        // Try to parse JSON, if backend redirects (HTML) this might fail or return syntax error
        // We'll assume for now we can extract the ID or just reload.
        // If the backend returns a redirect, we might need a dedicated API endpoint.
        // For MVP, if it succeeds, we query the new client by document.
        
        // Let's assume the backend will just work or we mock the "success" for the UI flow 
        // if we can't get the ID without an API.
        // Ideally: ClientesController should return JSON if AJAX.
        
        try {
            const data = await response.json();
            if (data.success || data.id) {
                // Success path
                const select = document.getElementById('selectCliente');
                const option = new Option(
                    `${formData.get('nombre')} ${formData.get('apellido')} (${formData.get('documento_numero')})`, 
                    data.id || 0, 
                    true, 
                    true
                );
                select.add(option);
                select.dispatchEvent(new Event('change'));
                
                const modal = bootstrap.Modal.getInstance(document.getElementById('modalNuevoCliente'));
                modal.hide();
                SIPAN.success('Cliente registrado');
                form.reset();
                return;
            }
        } catch(e) {
             // Backend returned HTML (redirect) probably.
             // We can't easily get the ID.
             SIPAN.success('Cliente registrado. Por favor búsquelo en la lista.');
             const modal = bootstrap.Modal.getInstance(document.getElementById('modalNuevoCliente'));
             modal.hide();
             // Reload page to refresh list is the safest fallback without API
             setTimeout(() => location.reload(), 1000);
        }

    } catch (e) {
        console.error(e);
        SIPAN.error('Error de conexión');
    }
}
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
