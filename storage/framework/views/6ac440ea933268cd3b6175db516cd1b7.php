<?php if (isset($component)) { $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54 = $attributes; } ?>
<?php $component = App\View\Components\AppLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\AppLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    <div class="container">
        <div class="page-header">
            <h1>Registrar Pago</h1>
        </div>

        <?php if(isset($reserva)): ?>
            
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Detalles de la Reserva</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Reserva:</strong> #<?php echo e($reserva->id); ?></p>
                            <p><strong>Cliente:</strong> <?php echo e($reserva->cliente->nombre); ?> <?php echo e($reserva->cliente->apellido); ?></p>
                            <p><strong>Habitación:</strong> <?php echo e($reserva->habitacion->numero); ?> (<?php echo e($reserva->habitacion->tipoHabitacion->nombre ?? 'N/A'); ?>)</p>
                            <p><strong>Fecha Entrada:</strong> <?php echo e(\Carbon\Carbon::parse($reserva->fecha_entrada)->format('d/m/Y')); ?></p>
                            <p><strong>Fecha Salida:</strong> <?php echo e(\Carbon\Carbon::parse($reserva->fecha_salida)->format('d/m/Y')); ?></p>
                        </div>
                        <div class="col-md-6">
                            <div class="alert alert-info">
                                <h5>Resumen de Costos:</h5>
                                <table class="table table-sm mb-0">
                                    <tr>
                                        <td><strong>Habitación:</strong></td>
                                        <td class="text-right">$<?php echo e(number_format($reserva->total_precio, 2)); ?></td>
                                    </tr>
                                    <?php
                                        $serviciosTotal = $reserva->estancia ? $reserva->estancia->serviciosDetalle->where('estado', '!=', 'anulado')->sum('subtotal') : 0;
                                    ?>
                                    <tr>
                                        <td><strong>Servicios:</strong></td>
                                        <td class="text-right">$<?php echo e(number_format($serviciosTotal, 2)); ?></td>
                                    </tr>
                                    <tr class="table-active">
                                        <td><strong>TOTAL RESERVA:</strong></td>
                                        <td class="text-right"><strong>$<?php echo e(number_format($totalGeneral ?? 0, 2)); ?></strong></td>
                                    </tr>
                                    <tr class="table-success">
                                        <td><strong>Total Pagado:</strong></td>
                                        <td class="text-right"><strong class="text-success">$<?php echo e(number_format($totalPagado ?? 0, 2)); ?></strong></td>
                                    </tr>
                                    <tr class="table-danger">
                                        <td><strong>SALDO PENDIENTE:</strong></td>
                                        <td class="text-right"><strong class="text-danger">$<?php echo e(number_format($saldoPendiente ?? 0, 2)); ?></strong></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <form action="<?php echo e(route('pagos.store')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="reserva_id" value="<?php echo e($reserva->id); ?>">

                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h4 class="mb-0">Información del Pago</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="monto" class="form-label"><strong>Monto a Pagar</strong></label>
                                    <div class="input-group input-group-lg">
                                        <span class="input-group-text">$</span>
                                        <input type="number" name="monto" id="monto" value="<?php echo e(old('monto', $saldoPendiente ?? 0)); ?>" step="0.01" min="0.01" max="<?php echo e($saldoPendiente ?? 999999); ?>" class="form-control" placeholder="0.00" required>
                                    </div>
                                    <small class="form-text text-muted">Máximo a pagar: $<?php echo e(number_format($saldoPendiente ?? 0, 2)); ?></small>
                                    <?php $__errorArgs = ['monto'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <span class="error-message"><?php echo e($message); ?></span>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="metodo_pago" class="form-label"><strong>Método de Pago</strong></label>
                                    <select name="metodo_pago" id="metodo_pago" class="form-control form-control-lg" required>
                                        <option value="">Seleccione método</option>
                                        <option value="efectivo" <?php echo e(old('metodo_pago') == 'efectivo' ? 'selected' : ''); ?>>Efectivo</option>
                                        <option value="tarjeta" <?php echo e(old('metodo_pago') == 'tarjeta' ? 'selected' : ''); ?>>Tarjeta</option>
                                        <option value="transferencia" <?php echo e(old('metodo_pago') == 'transferencia' ? 'selected' : ''); ?>>Transferencia</option>
                                    </select>
                                    <?php $__errorArgs = ['metodo_pago'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <span class="error-message"><?php echo e($message); ?></span>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="numero_transaccion" class="form-label">Número de Transacción (Opcional)</label>
                                    <input type="text" name="numero_transaccion" id="numero_transaccion" value="<?php echo e(old('numero_transaccion')); ?>" maxlength="100" class="form-control" placeholder="Ej: 123456789">
                                    <?php $__errorArgs = ['numero_transaccion'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <span class="error-message"><?php echo e($message); ?></span>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="comprobante" class="form-label">Comprobante (Opcional)</label>
                                    <input type="text" name="comprobante" id="comprobante" value="<?php echo e(old('comprobante')); ?>" maxlength="255" class="form-control" placeholder="Ej: Boleta 001-001234">
                                    <?php $__errorArgs = ['comprobante'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <span class="error-message"><?php echo e($message); ?></span>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="form-actions">
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="fas fa-check-circle"></i> Registrar Pago
                            </button>
                            <a href="<?php echo e(route('reservas.show', $reserva->id)); ?>" class="btn btn-secondary btn-lg">
                                <i class="fas fa-times-circle"></i> Cancelar
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        <?php else: ?>
            
            <form action="<?php echo e(route('pagos.store')); ?>" method="POST">
                <?php echo csrf_field(); ?>

                <div class="card">
                    <div class="card-body">
                        <div class="form-group">
                            <label for="reserva_id" class="form-label">Seleccionar Reserva</label>
                            <select name="reserva_id" id="reserva_id" class="form-control" required>
                                <option value="">Seleccione una reserva</option>
                                <?php $__currentLoopData = $reservas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $res): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($res->id); ?>" <?php echo e(old('reserva_id') == $res->id ? 'selected' : ''); ?>>
                                        #<?php echo e($res->id); ?> - <?php echo e($res->cliente->nombre); ?> <?php echo e($res->cliente->apellido); ?> - Hab. <?php echo e($res->habitacion->numero); ?> - $<?php echo e(number_format($res->total_precio, 2)); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <?php $__errorArgs = ['reserva_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <span class="error-message"><?php echo e($message); ?></span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div class="form-group">
                            <label for="monto" class="form-label">Monto</label>
                            <input type="number" name="monto" id="monto" value="<?php echo e(old('monto')); ?>" step="0.01" min="0.01" class="form-control" required>
                            <?php $__errorArgs = ['monto'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <span class="error-message"><?php echo e($message); ?></span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div class="form-group">
                            <label for="metodo_pago" class="form-label">Método de Pago</label>
                            <select name="metodo_pago" id="metodo_pago" class="form-control" required>
                                <option value="">Seleccione método</option>
                                <option value="efectivo" <?php echo e(old('metodo_pago') == 'efectivo' ? 'selected' : ''); ?>>Efectivo</option>
                                <option value="tarjeta" <?php echo e(old('metodo_pago') == 'tarjeta' ? 'selected' : ''); ?>>Tarjeta</option>
                                <option value="transferencia" <?php echo e(old('metodo_pago') == 'transferencia' ? 'selected' : ''); ?>>Transferencia</option>
                            </select>
                            <?php $__errorArgs = ['metodo_pago'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <span class="error-message"><?php echo e($message); ?></span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div class="form-group">
                            <label for="numero_transaccion" class="form-label">Número de Transacción (Opcional)</label>
                            <input type="text" name="numero_transaccion" id="numero_transaccion" value="<?php echo e(old('numero_transaccion')); ?>" maxlength="100" class="form-control">
                            <?php $__errorArgs = ['numero_transaccion'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <span class="error-message"><?php echo e($message); ?></span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div class="form-group">
                            <label for="comprobante" class="form-label">Comprobante (Opcional)</label>
                            <input type="text" name="comprobante" id="comprobante" value="<?php echo e(old('comprobante')); ?>" maxlength="255" class="form-control">
                            <?php $__errorArgs = ['comprobante'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <span class="error-message"><?php echo e($message); ?></span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">Registrar Pago</button>
                            <a href="<?php echo e(route('pagos.index')); ?>" class="btn btn-secondary">Cancelar</a>
                        </div>
                    </div>
                </div>
            </form>
        <?php endif; ?>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\hotel_systemax\resources\views/pagos/create.blade.php ENDPATH**/ ?>