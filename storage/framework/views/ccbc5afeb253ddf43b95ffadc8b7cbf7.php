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
            <h1>Reservas</h1>
            <a href="<?php echo e(route('reservas.create')); ?>" class="btn btn-primary">+ Nueva Reserva</a>
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
                            <th>Cliente</th>
                            <th>Habitación</th>
                            <th>Entrada</th>
                            <th>Salida</th>
                            <th>Total</th>
                            <th>Estado</th>
                            <th style="text-align: right;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $reservas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $reserva): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><span class="badge badge-primary">#<?php echo e($reserva->id); ?></span></td>
                            <td><strong><?php echo e($reserva->cliente->nombre); ?> <?php echo e($reserva->cliente->apellido); ?></strong></td>
                            <td><?php echo e($reserva->habitacion->numero); ?></td>
                            <td><?php echo e($reserva->fecha_entrada->format('d/m')); ?></td>
                            <td><?php echo e($reserva->fecha_salida->format('d/m')); ?></td>
                            <td><strong>$<?php echo e(number_format($reserva->total_precio, 2)); ?></strong></td>
                            <td>
                                <?php
                                    $estados = [
                                        'activa' => 'badge-primary',
                                        'confirmada' => 'badge-success',
                                        'cancelada' => 'badge-danger',
                                        'checkout' => 'badge-warning'
                                    ];
                                    $clase = $estados[$reserva->estado] ?? 'badge-info';
                                ?>
                                <span class="badge <?php echo e($clase); ?>"><?php echo e(ucfirst($reserva->estado)); ?></span>
                            </td>
                            <td style="text-align: right;">
                                <a href="<?php echo e(route('reservas.show', $reserva)); ?>" class="btn btn-sm btn-outline">Ver</a>
                                <a href="<?php echo e(route('reservas.edit', $reserva)); ?>" class="btn btn-sm btn-primary">Editar</a>
                                <form action="<?php echo e(route('reservas.destroy', $reserva)); ?>" method="POST" style="display:inline;" onsubmit="return confirm('¿Está seguro?');">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="btn btn-sm btn-danger">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted">No hay reservas registradas</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4">
            <?php echo e($reservas->links()); ?>

        </div>
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
<?php /**PATH C:\xampp\htdocs\hotel_systemax\resources\views/reservas/index.blade.php ENDPATH**/ ?>