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
                            <?php foreach ($detalles as $detalle): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($detalle['producto_nombre'] ?? 'Producto #' . $detalle['id_producto']) ?></strong></td>
                                <td>S/ <?= number_format($detalle['precio_unitario'], 2) ?></td>
                                <td><?= $detalle['cantidad'] ?></td>
                                <td><strong>S/ <?= number_format($detalle['subtotal'], 2) ?></strong></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" class="text-end"><strong>TOTAL:</strong></td>
                                <td><strong class="text-success" style="font-size: 1.3rem;">S/ <?= number_format($pedido['total'], 2) ?></strong></td>
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
                <?php if ($pedido['estado_pago'] !== 'pagado'): ?>
                <button @click="mostrarModalPago()" class="btn btn-success btn-sm">
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
                            <?php if (empty($pagos)): ?>
                            <tr>
                                <td colspan="4" class="text-center">No hay pagos registrados</td>
                            </tr>
                            <?php else: ?>
                            <?php foreach ($pagos as $pago): ?>
                            <tr>
                                <td><?= date('d/m/Y H:i', strtotime($pago['fecha_pago'])) ?></td>
                                <td><strong>S/ <?= number_format($pago['monto'], 2) ?></strong></td>
                                <td><?= ucfirst($pago['metodo_pago']) ?></td>
                                <td><?= htmlspecialchars($pago['usuario_nombre'] ?? '-') ?></td>
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
                    <p class="mb-0"><strong><?= date('d/m/Y H:i', strtotime($pedido['fecha_entrega'])) ?></strong></p>
                </div>
                
                <div class="mb-3">
                    <label class="text-muted">Estado:</label>
                    <p class="mb-0">
                        <?php
                        $estados = [
                            'pendiente' => '<span class="badge badge-warning">Pendiente</span>',
                            'en_proceso' => '<span class="badge badge-info">En Proceso</span>',
                            'completado' => '<span class="badge badge-success">Completado</span>',
                            'cancelado' => '<span class="badge badge-danger">Cancelado</span>'
                        ];
                        echo $estados[$pedido['estado']] ?? $pedido['estado'];
                        ?>
                    </p>
                </div>
                
                <?php if (!empty($pedido['observaciones'])): ?>
                <div class="mb-3">
                    <label class="text-muted">Observaciones:</label>
                    <p class="mb-0"><?= nl2br(htmlspecialchars($pedido['observaciones'])) ?></p>
                </div>
                <?php endif; ?>
                
                <hr>
                
                <?php if ($pedido['estado'] !== 'cancelado'): ?>
                <div class="d-grid gap-2">
                    <select x-model="nuevoEstado" class="form-control">
                        <option value="">Cambiar estado...</option>
                        <option value="pendiente">Pendiente</option>
                        <option value="en_proceso">En Proceso</option>
                        <option value="completado">Completado</option>
                        <option value="cancelado">Cancelado</option>
                    </select>
                    <button @click="actualizarEstado()" class="btn btn-primary" :disabled="!nuevoEstado">
                        <i class="fas fa-sync"></i> Actualizar Estado
                    </button>
                </div>
                <?php endif; ?>
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
                    <p class="mb-0"><strong class="text-success" style="font-size: 1.3rem;">S/ <?= number_format($pedido['total'], 2) ?></strong></p>
                </div>
                
                <div class="mb-3">
                    <label class="text-muted">Total Pagado:</label>
                    <p class="mb-0"><strong class="text-info">S/ <?= number_format($pedido['total_pagado'] ?? 0, 2) ?></strong></p>
                </div>
                
                <div class="mb-3">
                    <label class="text-muted">Saldo Pendiente:</label>
                    <p class="mb-0"><strong class="text-danger">S/ <?= number_format($pedido['total'] - ($pedido['total_pagado'] ?? 0), 2) ?></strong></p>
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
<div x-show="modalPago" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999;" @click.self="modalPago = false">
    <div style="position: relative; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 2rem; border-radius: 12px; max-width: 500px; width: 90%;">
        <h3 class="mb-4">Registrar Pago</h3>
        
        <form @submit.prevent="registrarPago()">
            <div class="mb-3">
                <label class="form-label">Monto <span class="text-danger">*</span></label>
                <input type="number" x-model="pago.monto" class="form-control" step="0.01" required :max="<?= $pedido['total'] - ($pedido['total_pagado'] ?? 0) ?>">
                <small class="text-muted">Saldo pendiente: S/ <?= number_format($pedido['total'] - ($pedido['total_pagado'] ?? 0), 2) ?></small>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Método de Pago <span class="text-danger">*</span></label>
                <select x-model="pago.metodo" class="form-control" required>
                    <option value="">Seleccionar método</option>
                    <option value="efectivo">Efectivo</option>
                    <option value="tarjeta">Tarjeta</option>
                    <option value="transferencia">Transferencia</option>
                    <option value="yape">Yape</option>
                    <option value="plin">Plin</option>
                </select>
            </div>
            
            <div class="d-flex gap-2">
                <button type="button" @click="modalPago = false" class="btn btn-secondary flex-fill">Cancelar</button>
                <button type="submit" class="btn btn-success flex-fill">Registrar Pago</button>
            </div>
        </form>
    </div>
</div>

<script>
function pedidoDetailApp() {
    return {
        nuevoEstado: '',
        modalPago: false,
        pago: {
            monto: '',
            metodo: ''
        },
        
        mostrarModalPago() {
            this.modalPago = true;
            this.pago = {
                monto: '',
                metodo: ''
            };
        },
        
        async actualizarEstado() {
            if (!this.nuevoEstado) return;
            
            const confirmed = await SIPAN.confirm('¿Actualizar el estado del pedido?');
            if (!confirmed) return;
            
            const formData = new FormData();
            formData.append('estado', this.nuevoEstado);
            
            try {
                const response = await fetch('/pedidos/update/<?= $pedido['id'] ?>', {
                    method: 'POST',
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
            }
        },
        
        async registrarPago() {
            if (!this.pago.monto || !this.pago.metodo) {
                SIPAN.error('Complete todos los campos');
                return;
            }
            
            const formData = new FormData();
            formData.append('id_pedido', <?= $pedido['id'] ?>);
            formData.append('monto', this.pago.monto);
            formData.append('metodo_pago', this.pago.metodo);
            
            try {
                const response = await fetch('/pedidos/registrar-pago', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    SIPAN.success(data.message);
                    this.modalPago = false;
                    setTimeout(() => window.location.reload(), 1500);
                } else {
                    SIPAN.error(data.message);
                }
            } catch (error) {
                SIPAN.error('Error al registrar pago');
            }
        }
    }
}
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
