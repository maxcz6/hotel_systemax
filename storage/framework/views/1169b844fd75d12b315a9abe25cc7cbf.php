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
            <h1>Reportes</h1>
        </div>

        <div class="dashboard-grid">
            <div class="card">
                <div class="card-body">
                    <h3>Reporte General</h3>
                    <p>Vista completa de reservas y estadísticas</p>
                    <a href="<?php echo e(route('reportes.general')); ?>" class="btn btn-primary">Ver Reporte</a>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h3>Ingresos</h3>
                    <p>Análisis de ingresos por período</p>
                    <a href="<?php echo e(route('reportes.ingresos')); ?>" class="btn btn-primary">Ver Reporte</a>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h3>Ocupación</h3>
                    <p>Estadísticas de ocupación de habitaciones</p>
                    <a href="<?php echo e(route('reportes.ocupacion')); ?>" class="btn btn-primary">Ver Reporte</a>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h3>🛎️ Servicios</h3>
                    <p>Servicios más utilizados e ingresos</p>
                    <a href="<?php echo e(route('reportes.servicios')); ?>" class="btn btn-primary">Ver Reporte</a>
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
<?php /**PATH C:\xampp\htdocs\hotel_systemax\resources\views/reportes/index.blade.php ENDPATH**/ ?>