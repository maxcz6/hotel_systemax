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
            <h1>Tipos de Habitación</h1>
            <a href="<?php echo e(route('tipo_habitaciones.create')); ?>" class="btn btn-primary">Nuevo Tipo de Habitación</a>
        </div>

        <?php if(session('success')): ?>
        <div class="alert alert-success"><?php echo e(session('success')); ?></div>
        <?php endif; ?>

        <?php if(session('error')): ?>
        <div class="alert alert-error"><?php echo e(session('error')); ?></div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Descripción</th>
                            <th>Capacidad</th>
                            <th>Precio/Noche</th>
                            <th>N° Habitaciones</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $tipoHabitaciones; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tipo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><strong><?php echo e($tipo->nombre); ?></strong></td>
                            <td><?php echo e($tipo->descripcion ?? 'Sin descripción'); ?></td>
                            <td><?php echo e($tipo->capacidad ?? 'N/A'); ?> personas</td>
                            <td>$<?php echo e(number_format($tipo->precio_por_noche, 2)); ?></td>
                            <td><?php echo e($tipo->habitaciones_count ?? $tipo->habitaciones->count()); ?></td>
                            <td>
                                <a href="<?php echo e(route('tipo_habitaciones.edit', $tipo->id)); ?>" class="action-btn">Editar</a>
                                <form action="<?php echo e(route('tipo_habitaciones.destroy', $tipo->id)); ?>" method="POST" style="display:inline;" onsubmit="return confirm('¿Está seguro de eliminar este tipo de habitación? Esto puede afectar las habitaciones existentes.');">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="delete-btn">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="6" class="text-center">No hay tipos de habitación registrados</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>

                <div class="pagination-wrapper">
                    <?php echo e($tipoHabitaciones->links()); ?>

                </div>
            </div>
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
<?php /**PATH C:\xampp\htdocs\hotel_systemax\resources\views/tipo_habitaciones/index.blade.php ENDPATH**/ ?>