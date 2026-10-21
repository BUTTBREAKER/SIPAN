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
            <?php
            require_once __DIR__ . '/../../Helpers/CSRF.php';
            echo \App\Helpers\CSRF::field();
            ?>
            <!-- Información del Cliente y Fechas -->
            <div class="row mb-3">
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="form-label">Buscar por Cédula</label>
                        <div class="input-group">
                            <input type="text" id="inputBuscarCedulaPedido" class="form-control" placeholder="Ej. 12345678" @keydown.enter.prevent="buscarClienteCedula()">
                            <button type="button" class="btn btn-outline-primary" @click="buscarClienteCedula()" title="Buscar por cédula">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <div class="d-flex justify-content-between align-items-center">
                            <label class="form-label mb-0">Cliente <span class="text-danger">*</span></label>
                            <button type="button" class="btn btn-sm btn-link p-0 text-decoration-none" data-bs-toggle="modal" data-bs-target="#modalNuevoClientePedido">
                                <i class="fas fa-user-plus me-1"></i>Nuevo
                            </button>
                        </div>
                        <select x-model="id_cliente" id="selectClientePedido" class="form-control mt-1" required @change="cargarGastoAnterior()">
                            <option value="">Seleccionar cliente</option>
                            <?php
                            require_once __DIR__ . '/../../Models/Cliente.php';
                            $clienteModel = new \App\Models\Cliente();
                            $clientes = $clienteModel->getBySucursal($_SESSION['sucursal_id']);
                            foreach ($clientes as $cliente) :
                                ?>
                            <option value="<?= $cliente['id'] ?>" data-cedula="<?= htmlspecialchars($cliente['documento_numero'] ?? '') ?>">
                                <?= htmlspecialchars($cliente['nombre'] . ' ' . $cliente['apellido']) ?>
                                <?php if (!empty($cliente['documento_numero'])) : ?>(<?= $cliente['documento_numero'] ?>)<?php endif; ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="form-group">
                        <label class="form-label">Tipo de Pedido <span class="text-danger">*</span></label>
                        <select x-model="tipo" class="form-control">
                            <option value="despacho_mayor">Despacho al Mayor</option>
                            <option value="insumos">Pedido de Insumos</option>
                        </select>
                    </div>
                </div>
                
                <div class="col-md-2">
                    <div class="form-group">
                        <label class="form-label">Repartidor (Opcional)</label>
                        <select x-model="id_repartidor" class="form-control">
                            <option value="">Sin asignar</option>
                            <?php
                            require_once __DIR__ . '/../../Models/Usuario.php';
                            $usuarioModel = new \App\Models\Usuario();
                            $repartidores = $usuarioModel->getRepartidoresBySucursal($_SESSION['sucursal_id']);
                            foreach ($repartidores as $rep) :
                                ?>
                            <option value="<?= $rep['id'] ?>">
                                <?= htmlspecialchars($rep['primer_nombre'] . ' ' . $rep['apellido_paterno']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="form-group">
                        <label class="form-label">Fecha Entrega <span class="text-danger">*</span></label>
                        <input type="date" x-model="fecha_entrega" class="form-control" required :min="minDate">
                    </div>
                </div>
                
                <div class="col-md-2">
                    <div class="form-group">
                        <label class="form-label">Hora Entrega</label>
                        <input type="time" x-model="hora_entrega" class="form-control">
                    </div>
                </div>
            </div>

            <!-- Panel Informativo: Gasto del Pedido Anterior (Insumos) -->
            <div x-show="ultimo_pedido" class="alert alert-info border-info d-flex align-items-center mb-4 py-3" style="background-color: #f0fdf4; border-left: 5px solid #16a34a;">
                <i class="fas fa-history fa-2x text-success me-3"></i>
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between align-items-center">
                        <strong class="text-dark">Historial del Cliente — Último Pedido (<span x-text="ultimo_pedido?.numero_pedido"></span>)</strong>
                        <span class="badge bg-primary text-white" x-text="'Total Facturado: $' + parseFloat(ultimo_pedido?.total || 0).toFixed(2)"></span>
                    </div>
                    <div class="small text-muted mt-1">
                        Fecha: <span x-text="ultimo_pedido?.fecha_pedido ? ultimo_pedido.fecha_pedido.substring(0, 10) : ''"></span> |
                        Productos elaborados: <strong x-text="ultimo_pedido?.productos_resumen || 'Detalles'"></strong>
                    </div>
                    <div class="mt-2 p-2 rounded bg-white border">
                        <div class="d-flex justify-content-between align-items-center">
                            <span>
                                <span class="badge bg-warning text-dark me-2"><i class="fas fa-boxes me-1"></i>Insumos invertidos en preparación:</span>
                                <span class="small text-secondary" x-text="ultimo_pedido?.insumos_resumen_str || ''"></span>
                            </span>
                            <span class="ms-2">
                                Gasto Insumos: <strong class="text-danger h6 mb-0" x-text="'$' + parseFloat(ultimo_pedido?.gasto_insumos || 0).toFixed(2)"></strong>
                            </span>
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
                <button type="submit" class="btn btn-primary" :disabled="items.length === 0 || !id_cliente || !fecha_entrega || isSubmitting">
                    <template x-if="!isSubmitting">
                        <span><i class="fas fa-check"></i> Guardar Pedido</span>
                    </template>
                    <template x-if="isSubmitting">
                        <span><i class="fas fa-spinner fa-spin"></i> Guardando...</span>
                    </template>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Nuevo Cliente Pedido -->
<div class="modal fade" id="modalNuevoClientePedido" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Registrar Nuevo Cliente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formNuevoClientePedido">
                    <?= \App\Helpers\CSRF::field() ?>
                    <div class="mb-3">
                        <label class="form-label">Nombre <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="nombre" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Apellido</label>
                        <input type="text" class="form-control" name="apellido">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Cédula / Documento <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="documento_numero" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Teléfono</label>
                        <input type="text" class="form-control" name="telefono">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Dirección</label>
                        <textarea class="form-control" name="direccion" rows="2"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnGuardarClientePedido" onclick="guardarClienteExpressPedido()">
                    <i class="fas fa-save me-1"></i> Guardar Cliente
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function pedidoApp() {
    return {
        id_cliente: '',
        tipo: 'despacho_mayor',
        id_repartidor: '',
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
        isSubmitting: false,
        minDate: new Date().toISOString().split('T')[0],
        ultimo_pedido: null,
        
        init() {
            // Establecer fecha mínima como hoy
            this.fecha_entrega = this.minDate;
        },

        async cargarGastoAnterior() {
            if (!this.id_cliente) {
                this.ultimo_pedido = null;
                return;
            }

            try {
                const response = await fetch(`/pedidos/ultimo-cliente/${this.id_cliente}`);
                const data = await response.json();
                if (data.success && data.pedido) {
                    this.ultimo_pedido = {
                        numero_pedido: data.pedido.numero_pedido,
                        fecha_pedido: data.pedido.fecha_pedido,
                        total: data.pedido.total,
                        gasto_insumos: data.gasto_insumos,
                        productos_resumen: data.productos_resumen,
                        insumos_resumen_str: data.insumos_resumen_str,
                        insumos_detalle: data.insumos_detalle || []
                    };
                } else {
                    this.ultimo_pedido = null;
                }
            } catch (err) {
                console.error('Error cargando gasto anterior:', err);
                this.ultimo_pedido = null;
            }
        },

        async buscarClienteCedula() {
            const cedulaInput = document.getElementById('inputBuscarCedulaPedido');
            const cedula = cedulaInput ? cedulaInput.value.trim() : '';
            if (!cedula) {
                SIPAN.warning('Ingrese un número de cédula para buscar');
                return;
            }

            const select = document.getElementById('selectClientePedido');
            // 1. Buscar en opciones ya cargadas
            for (let i = 0; i < select.options.length; i++) {
                const opt = select.options[i];
                const optCedula = (opt.getAttribute('data-cedula') || '').trim();
                if (optCedula === cedula) {
                    this.id_cliente = opt.value;
                    select.value = opt.value;
                    this.cargarGastoAnterior();
                    SIPAN.success('Cliente encontrado: ' + opt.text);
                    return;
                }
            }

            // 2. Buscar en servidor
            try {
                const resp = await fetch(`/clientes/search?q=${encodeURIComponent(cedula)}`);
                const data = await resp.json();
                if (data.success && data.clientes && data.clientes.length > 0) {
                    const cli = data.clientes.find(c => (c.documento_numero || '').trim() === cedula) || data.clientes[0];
                    const option = new Option(
                        `${cli.nombre} ${cli.apellido} (${cli.documento_numero || ''})`,
                        cli.id,
                        true,
                        true
                    );
                    option.setAttribute('data-cedula', cli.documento_numero || '');
                    select.add(option);
                    this.id_cliente = cli.id;
                    select.value = cli.id;
                    this.cargarGastoAnterior();
                    SIPAN.success(`Cliente seleccionado: ${cli.nombre} ${cli.apellido}`);
                } else {
                    Swal.fire({
                        title: 'Cliente no registrado',
                        text: `No se encontró ningún cliente con la cédula "${cedula}". ¿Desea registrarlo ahora?`,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: '<i class="fas fa-user-plus me-1"></i> Sí, Registrar',
                        cancelButtonText: 'Cancelar',
                        confirmButtonColor: '#0d6efd'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const modalEl = document.getElementById('modalNuevoClientePedido');
                            const modal = new bootstrap.Modal(modalEl);
                            modal.show();
                            const docInput = modalEl.querySelector('input[name="documento_numero"]');
                            if (docInput) docInput.value = cedula;
                        }
                    });
                }
            } catch (err) {
                console.error(err);
                SIPAN.error('Error al consultar cliente');
            }
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
            formData.append('tipo', this.tipo || 'despacho_mayor');
            if (this.id_repartidor) formData.append('id_repartidor', this.id_repartidor);
            formData.append('fecha_entrega', this.fecha_entrega + (this.hora_entrega ? ' ' + this.hora_entrega : ' 12:00:00'));
            formData.append('total', this.total);
            formData.append('abono_inicial', this.abono_inicial || 0);
            formData.append('observaciones', this.observaciones);
            formData.append('productos', JSON.stringify(this.items));
            
            this.isSubmitting = true;
            try {
                // Obtener CSRF token
                const csrfToken = document.querySelector('input[name="csrf_token"]').value;

                const response = await fetch('/pedidos/store', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-Token': csrfToken
                    },
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    SIPAN.success(data.message);
                    const targetId = data.pedido_id || data.id;
                    setTimeout(() => {
                        window.location.href = targetId ? ('/pedidos/show/' + targetId) : '/pedidos';
                    }, 1500);
                } else {
                    SIPAN.error(data.message);
                }
            } finally {
                this.isSubmitting = false;
            }
        }
    }
}

async function guardarClienteExpressPedido() {
    const form = document.getElementById('formNuevoClientePedido');
    const formData = new FormData(form);
    const btn = document.getElementById('btnGuardarClientePedido');
    const originalContent = btn.innerHTML;

    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';

    try {
        const csrfToken = form.querySelector('input[name="csrf_token"]').value;
        const response = await fetch('/clientes/store', {
            method: 'POST',
            body: formData,
            headers: {
                'Accept': 'application/json',
                'X-CSRF-Token': csrfToken
            }
        });

        const data = await response.json();
        if (data.success && data.id) {
            const select = document.getElementById('selectClientePedido');
            const docNum = formData.get('documento_numero') || '';
            const option = new Option(
                `${formData.get('nombre')} ${formData.get('apellido')} (${docNum})`,
                data.id,
                true,
                true
            );
            option.setAttribute('data-cedula', docNum);
            select.add(option);
            select.value = data.id;
            select.dispatchEvent(new Event('change'));

            const modalEl = document.getElementById('modalNuevoClientePedido');
            const modal = bootstrap.Modal.getInstance(modalEl);
            if (modal) modal.hide();
            SIPAN.success('Cliente registrado correctamente');
            form.reset();
        } else {
            SIPAN.error(data.message || 'Error al registrar cliente');
        }
    } catch(err) {
        console.error(err);
        SIPAN.error('Error de conexión al registrar cliente');
    } finally {
        btn.disabled = false;
        btn.innerHTML = originalContent;
    }
}
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
