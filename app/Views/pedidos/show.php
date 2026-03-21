<?php
$pageTitle = 'Detalle de Pedido';
$currentPage = 'pedidos';
require_once __DIR__ . '/../layouts/header.php';
?>



<div class="page-header">
    <h2 class="page-title">Pedido #<?= str_pad($pedido['id'], 6, '0', STR_PAD_LEFT) ?></h2>
    <p class="page-subtitle">Información completa del pedido</p>
</div>

<div class="row" x-data="pedidoDetailApp()">
    <?php
    require_once __DIR__ . '/../../Helpers/CSRF.php';
    echo \App\Helpers\CSRF::field();
    ?>
    <div class="col-md-8">
        <!-- Productos del Pedido -->
        <div class="card mb-3">
            <div class="card-header">
                <h3 class="card-title">Productos del Pedido</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Precio Unit.</th>
                                <th>Cantidad</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($productos)) : ?>
                                <tr>
                                    <td colspan="4" class="text-center">No hay productos en este pedido</td>
                                </tr>
                            <?php else : ?>
                                <?php foreach ($productos as $detalle) : ?>
                                    <tr>
                                        <td><strong><?= htmlspecialchars($detalle['producto_nombre'] ?? 'Producto #' . $detalle['id_producto']) ?></strong></td>
                                        <td>$ <?= number_format($detalle['precio_unitario'], 2) ?></td>
                                        <td><?= $detalle['cantidad'] ?></td>
                                        <td><strong>$ <?= number_format($detalle['subtotal'], 2) ?></strong></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" class="text-end"><strong>TOTAL:</strong></td>
                                <td><strong class="text-success" style="font-size: 1.3rem;">$ <?= number_format($pedido['total'], 2) ?></strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- Historial de Pagos -->
        <div class="card">
            <div class="card-header d-flex justify-between align-center">
                <h3 class="card-title">Historial de Pagos</h3>
                <?php if ($pedido['estado_pago'] !== 'pagado') : ?>
                    <button type="button" data-bs-toggle="modal" data-bs-target="#modalPago" class="btn btn-success btn-sm">
                        <i class="fas fa-dollar-sign"></i> Registrar Pago
                    </button>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Monto</th>
                                <th>Método</th>
                                <th>Usuario</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($pagos)) : ?>
                                <tr>
                                    <td colspan="4" class="text-center">No hay pagos registrados</td>
                                </tr>
                            <?php else : ?>
                                <?php foreach ($pagos as $pago) : ?>
                                    <tr>
                                        <td><?= date('d/m/Y H:i', strtotime($pago['fecha_pago'])) ?></td>
                                        <td><strong>$ <?= number_format($pago['monto'], 2) ?></strong></td>
                                        <td><?= ucfirst($pago['metodo_pago']) ?></td>
                                        <td><?= htmlspecialchars(($pago['primer_nombre'] ?? '') . ' ' . ($pago['apellido_paterno'] ?? '') ?: '-') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <!-- Información del Pedido -->
        <div class="card mb-3">
            <div class="card-header">
                <h3 class="card-title">Información del Pedido</h3>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="text-muted">Cliente:</label>
                    <p class="mb-0"><strong><?= htmlspecialchars($pedido['cliente_nombre'] ?? 'Cliente #' . $pedido['id_cliente']) ?></strong></p>
                </div>

                <div class="mb-3">
                    <label class="text-muted">Fecha de Pedido:</label>
                    <p class="mb-0"><strong><?= date('d/m/Y H:i', strtotime($pedido['fecha_pedido'])) ?></strong></p>
                </div>

                <div class="mb-3">
                    <label class="text-muted">Fecha de Entrega:</label>
                    <p class="mb-0">
                        <strong>
                            <?= $pedido['fecha_entrega'] ? date('d/m/Y H:i', strtotime($pedido['fecha_entrega'])) : 'No especificada' ?>
                        </strong>
                    </p>
                </div>

                <div class="mb-3">
                    <label class="text-muted">Estado:</label>
                    <p class="mb-0">
                        <?php
                        $estados = [
                            'pendiente' => '<span class="badge badge-warning">Pendiente</span>',
                            'en_proceso' => '<span class="badge badge-info">En Proceso</span>',
                            'en_camino' => '<span class="badge badge-primary">En Camino</span>',
                            'completado' => '<span class="badge badge-success">Completado</span>',
                            'entregado' => '<span class="badge badge-success">Entregado</span>',
                            'no_entregado' => '<span class="badge badge-danger">No Entregado</span>',
                            'cancelado' => '<span class="badge badge-danger">Cancelado</span>'
                        ];
                        echo $estados[$pedido['estado_pedido']] ?? $pedido['estado_pedido'];
                        ?>
                    </p>
                </div>

                <?php if (!empty($pedido['observaciones'])) : ?>
                    <div class="mb-3">
                        <label class="text-muted">Observaciones:</label>
                        <p class="mb-0"><?= nl2br(htmlspecialchars($pedido['observaciones'])) ?></p>
                    </div>
                <?php endif; ?>

                <hr>

                <?php if ($pedido['estado_pedido'] !== 'cancelado') :
                    ?>
                    <div class="d-grid gap-2 mb-4">
                        <select x-model="nuevoEstado" class="form-control">
                            <option value="">Cambiar estado...</option>
                            <option value="pendiente">Pendiente</option>
                            <option value="en_proceso">En Proceso</option>
                            <option value="en_camino">En Camino</option>
                            <option value="completado">Completado</option>
                            <option value="entregado">Entregado</option>
                            <option value="no_entregado">No Entregado</option>
                            <option value="cancelado">Cancelado</option>
                        </select>
                        <button @click="actualizarEstado()" class="btn btn-primary" :disabled="!nuevoEstado">
                            <i class="fas fa-sync"></i> Actualizar Estado
                        </button>
                    </div>
                <?php endif; ?>

                <!-- SECCIÓN REPARTIDOR -->
                <div class="mt-4 border-top pt-3">
                    <h4 class="h6 mb-3 text-muted">Asignación de Repartidor</h4>
                    <?php if (!empty($pedido['id_repartidor'])): ?>
                        <?php 
                            $rep_nombre = 'Repartidor Asignado';
                            foreach($repartidores ?? [] as $rep) {
                                if($rep['id'] == $pedido['id_repartidor']) {
                                    $rep_nombre = $rep['primer_nombre'] . ' ' . $rep['apellido_paterno'];
                                    break;
                                }
                            }
                        ?>
                        <p class="mb-2"><i class="fas fa-motorcycle text-success mr-2"></i> <strong><?= htmlspecialchars($rep_nombre) ?></strong></p>
                    <?php else: ?>
                        <p class="mb-2 text-warning"><i class="fas fa-exclamation-triangle mr-2"></i> Sin asignar</p>
                    <?php endif; ?>

                    <?php if ($pedido['estado_pedido'] !== 'cancelado' && $pedido['estado_pedido'] !== 'entregado' && $pedido['estado_pedido'] !== 'completado') : ?>
                        <div class="input-group mt-2">
                            <select x-model="nuevoRepartidor" class="form-select" style="max-width:200px">
                                <option value="">Seleccionar...</option>
                                <?php foreach($repartidores ?? [] as $rep): ?>
                                    <option value="<?= $rep['id'] ?>" <?= $pedido['id_repartidor'] == $rep['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($rep['primer_nombre'] . ' ' . $rep['apellido_paterno']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <button @click="asignarRepartidor()" class="btn btn-outline-secondary" :disabled="!nuevoRepartidor">
                                Asignar
                            </button>
                        </div>
                    <?php endif; ?>
                </div>

            </div>
        </div>

        <!-- Información de Pago -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Estado de Pago</h3>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="text-muted">Total del Pedido:</label>
                    <p class="mb-0"><strong class="text-success" style="font-size: 1.3rem;">$ <?= number_format($pedido['total'], 2) ?></strong></p>
                </div>
                <div class="mb-3">
                    <label class="text-muted d-block small">Monto Pagado</label>
                    <p class="mb-0"><strong class="text-info">$ <?= number_format($pedido['monto_pagado'] ?? 0, 2) ?></strong></p>
                </div>
                <div class="mb-0">
                    <label class="text-muted d-block small">Saldo Pendiente</label>
                    <p class="mb-0"><strong class="text-danger">$ <?= number_format($pedido['total'] - ($pedido['monto_pagado'] ?? 0), 2) ?></strong></p>
                </div>

                <div class="mb-3">
                    <label class="text-muted">Estado de Pago:</label>
                    <p class="mb-0">
                        <?php
                        $pagos_estados = [
                            'pendiente' => '<span class="badge badge-danger">Pendiente</span>',
                            'abonado' => '<span class="badge badge-warning">Abonado</span>',
                            'pagado' => '<span class="badge badge-success">Pagado</span>'
                        ];
                        echo $pagos_estados[$pedido['estado_pago']] ?? $pedido['estado_pago'];
                        ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Registro de Pago -->
<div class="modal fade" id="modalPago" tabindex="-1" aria-labelledby="modalPagoLabel" aria-hidden="true" x-data="modalPagoData()">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalPagoLabel">Registrar Pago</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Monto <span class="text-danger">*</span></label>
                    <input type="number" x-model="monto" class="form-control" step="0.01" min="0.01" :max="<?= $pedido['total'] - ($pedido['monto_pagado'] ?? 0) ?>">
                    <small class="text-muted">Saldo pendiente: $ <?= number_format($pedido['total'] - ($pedido['monto_pagado'] ?? 0), 2) ?></small>
                </div>

                <div class="mb-3">
                    <label class="form-label">Método de Pago <span class="text-danger">*</span></label>
                    <select x-model="metodo" class="form-control">
                        <option value="">Seleccionar método</option>
                        <option value="efectivo">Efectivo</option>
                        <option value="tarjeta">Tarjeta</option>
                        <option value="transferencia">Transferencia</option>
                        <option value="Pago Movil">Pago Movil</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success" @click="registrarPago()" :disabled="procesando || !monto || !metodo">
                    <span x-show="!procesando">Registrar Pago</span>
                    <span x-show="procesando">Procesando...</span>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function pedidoDetailApp() {
        return {
            nuevoEstado: '',

            async actualizarEstado() {
                if (!this.nuevoEstado) return;

                const confirmed = await SIPAN.confirm('¿Actualizar el estado del pedido?');
                if (!confirmed) return;

                const formData = new FormData();
                formData.append('estado_pedido', this.nuevoEstado);

                const csrfToken = document.querySelector('input[name="csrf_token"]').value;

                try {
                    const response = await fetch('/pedidos/update/<?= $pedido['id'] ?>', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-Token': csrfToken
                        },
                        body: formData
                    });

                    const data = await response.json();

                    if (data.success) {
                        SIPAN.success(data.message);
                        setTimeout(() => window.location.reload(), 1500);
                    } else {
                        SIPAN.error(data.message);
                    }
                } catch (error) {
                    SIPAN.error('Error al actualizar estado');
                    console.error('Error en actualizarEstado:', error);
                }
            },

            nuevoRepartidor: '<?= $pedido['id_repartidor'] ?? '' ?>',
            
            async asignarRepartidor() {
                if (!this.nuevoRepartidor) return;

                const formData = new FormData();
                formData.append('id_repartidor', this.nuevoRepartidor);

                const csrfToken = document.querySelector('input[name="csrf_token"]').value;

                try {
                    const response = await fetch('/pedidos/asignar-repartidor/<?= $pedido['id'] ?>', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-Token': csrfToken
                        },
                        body: formData
                    });

                    const data = await response.json();

                    if (data.success) {
                        SIPAN.success(data.message);
                        setTimeout(() => window.location.reload(), 1500);
                    } else {
                        SIPAN.error(data.message || 'Error al asignar');
                    }
                } catch (error) {
                    SIPAN.error('Error de conexión');
                    console.error('Error:', error);
                }
            }
        }
    }

    function modalPagoData() {
        return {
            monto: '',
            metodo: '',
            procesando: false,

            async registrarPago() {
                console.log('=== CLICK EN REGISTRAR PAGO ===');
                console.log('Monto:', this.monto);
                console.log('Método:', this.metodo);

                // Validaciones
                if (!this.monto || !this.metodo) {
                    console.error('Campos incompletos');
                    SIPAN.error('Complete todos los campos');
                    return;
                }

                const montoFloat = parseFloat(this.monto);
                console.log('Monto parseado:', montoFloat);

                if (isNaN(montoFloat) || montoFloat <= 0) {
                    console.error('Monto inválido:', montoFloat);
                    SIPAN.error('El monto debe ser mayor a 0');
                    return;
                }

                const saldoPendiente = <?= $pedido['total'] - ($pedido['monto_pagado'] ?? 0) ?>;
                console.log('Saldo pendiente:', saldoPendiente);

                if (montoFloat > saldoPendiente) {
                    console.error('Monto mayor al saldo');
                    SIPAN.error('El monto no puede ser mayor al saldo pendiente');
                    return;
                }

                this.procesando = true;
                console.log('Procesando... Estado:', this.procesando);

                const formData = new FormData();
                formData.append('pedido_id', '<?= $pedido['id'] ?>');
                formData.append('monto', montoFloat.toString());
                formData.append('metodo_pago', this.metodo);

                console.log('FormData preparado:');
                for (let pair of formData.entries()) {
                    console.log('  ' + pair[0] + ': ' + pair[1]);
                }

                try {
                    console.log('Enviando petición fetch...');

                    const csrfToken = document.querySelector('input[name="csrf_token"]').value;

                    const response = await fetch('/pedidos/registrar-pago', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-Token': csrfToken
                        },
                        body: formData
                    });

                    console.log('Respuesta recibida. Status:', response.status);

                    const responseText = await response.text();
                    console.log('Response text:', responseText);

                    let data;
                    try {
                        data = JSON.parse(responseText);
                        console.log('JSON parseado:', data);
                    } catch (e) {
                        console.error('Error parseando JSON:', e);
                        console.error('Text recibido:', responseText);
                        throw new Error('Respuesta inválida del servidor');
                    }

                    if (data.success) {
                        console.log('✓ Éxito');
                        SIPAN.success(data.message || 'Pago registrado correctamente');

                        // Cerrar modal
                        const modalEl = document.getElementById('modalPago');
                        const modal = bootstrap.Modal.getInstance(modalEl);
                        if (modal) {
                            modal.hide();
                        }

                        // Recargar
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    } else {
                        console.error('✗ Error del servidor:', data.message);
                        SIPAN.error(data.message || 'Error al registrar el pago');
                        this.procesando = false;
                    }
                } catch (error) {
                    console.error('=== ERROR CATCH ===');
                    console.error('Error:', error);
                    SIPAN.error('Error de conexión: ' + error.message);
                    this.procesando = false;
                }
            }
        }
    }

    // Log cuando Alpine.js se inicializa
    document.addEventListener('alpine:init', () => {
        console.log('✓ Alpine.js inicializado');
    });
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
