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
        <div class="flex justify-between items-center mb-4">
            <h1>Pagos</h1>
            <?php if(in_array(Auth::user()->role, ['administrador', 'gerente', 'recepcion'])): ?>
                <a href="<?php echo e(route('pagos.create')); ?>" class="btn btn-primary">+ Nuevo Pago</a>
            <?php endif; ?>
        </div>

        <?php if(session('success')): ?>
            <div class="alert alert-success"><?php echo e(session('success')); ?></div>
        <?php endif; ?>
        <?php if(session('error')): ?>
            <div class="alert alert-danger"><?php echo e(session('error')); ?></div>
        <?php endif; ?>

        <div class="card">
            <div style="overflow-x: auto;">
                <table class="table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Reserva</th>
                            <th>Cliente</th>
                            <th>Fecha</th>
                            <th>Monto</th>
                            <th>Total</th>
                            <th>Pagado</th>
                            <th>Pendiente</th>
                            <th>Método</th>
                            <th>Estado</th>
                            <?php if(Auth::user()->role === 'administrador'): ?>
                                <th style="text-align: right;">Acciones</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $pagos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pago): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><a href="<?php echo e(route('pagos.show', $pago->id)); ?>" class="badge badge-primary">#<?php echo e($pago->id); ?></a></td>
                            <td><a href="<?php echo e(route('reservas.show', $pago->reserva_id)); ?>">#<?php echo e($pago->reserva_id); ?></a></td>
                            <td><strong><?php echo e($pago->reserva->cliente->nombre); ?></strong></td>
                            <td><?php echo e($pago->fecha_pago->format('d/m')); ?></td>
                            <td><span class="badge badge-info">$<?php echo e(number_format($pago->monto, 2)); ?></span></td>
                            <td>$<?php echo e(number_format($pago->total_reserva, 2)); ?></td>
                            <td><strong class="text-success">$<?php echo e(number_format($pago->total_pagado, 2)); ?></strong></td>
                            <td>
                                <?php if($pago->saldo_pendiente > 0): ?>
                                    <strong class="text-danger">$<?php echo e(number_format($pago->saldo_pendiente, 2)); ?></strong>
                                <?php else: ?>
                                    <span class="badge badge-success">Pagado</span>
                                <?php endif; ?>
                            </td>
                            <td><span class="badge badge-outline"><?php echo e(ucfirst($pago->metodo_pago)); ?></span></td>
                            <td>
                                <?php
                                    $estadoColor = [
                                        'completado' => 'badge-success',
                                        'pendiente' => 'badge-warning',
                                        'anulado' => 'badge-danger'
                                    ];
                                    $clase = $estadoColor[$pago->estado] ?? 'badge-info';
                                ?>
                                <span class="badge <?php echo e($clase); ?>"><?php echo e(ucfirst($pago->estado)); ?></span>
                            </td>
                            <?php if(Auth::user()->role === 'administrador'): ?>
                            <td style="text-align: right;">
                                <a href="<?php echo e(route('pagos.show', $pago->id)); ?>" class="btn btn-sm btn-outline">Ver</a>
                                <a href="<?php echo e(route('pagos.edit', $pago->id)); ?>" class="btn btn-sm btn-primary">Editar</a>
                                <?php if($pago->estado !== 'anulado'): ?>
                                    <form action="<?php echo e(route('pagos.destroy', $pago->id)); ?>" method="POST" style="display: inline;" onsubmit="return confirm('¿Está seguro?')">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="btn btn-sm btn-danger">Anular</button>
                                    </form>
                                <?php endif; ?>
                            </td>
                            <?php endif; ?>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="<?php echo e(Auth::user()->role === 'administrador' ? '11' : '10'); ?>" class="text-center text-muted">No hay pagos registrados</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4">
            <?php echo e($pagos->links()); ?>

        </div>
    </div>

    <style>
        .table-hover tbody tr:hover {
            background-color: #f5f5f5;
        }
        .badge-info {
            background-color: #17a2b8;
            color: white;
        }
        .badge-secondary {
            background-color: #6c757d;
            color: white;
        }
        .btn-group-sm {
            gap: 0.25rem;
        }
    </style>
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
<?php /**PATH C:\xampp\htdocs\hotel_systemax\resources\views/pagos/index.blade.php ENDPATH**/ ?>