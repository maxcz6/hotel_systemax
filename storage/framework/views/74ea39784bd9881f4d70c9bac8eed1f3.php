<nav>
    <div class="container">
        <div class="nav-content">
            <div style="display: flex; align-items: center;">
                <a href="<?php echo e(route('dashboard')); ?>" class="brand"><?php echo e(__('Hotel Systemax')); ?></a>
                <ul>
                    <li><a href="<?php echo e(route('dashboard')); ?>"><?php echo e(__('Dashboard')); ?></a></li>
                    
                    <?php if(Auth::user()->role === 'administrador'): ?>
                        
                        <li><a href="<?php echo e(route('usuarios.index')); ?>"><?php echo e(__('Users')); ?></a></li>
                        <li><a href="<?php echo e(route('clientes.index')); ?>"><?php echo e(__('Clients')); ?></a></li>
                        <li><a href="<?php echo e(route('habitaciones.index')); ?>"><?php echo e(__('Rooms')); ?></a></li>
                        <li><a href="<?php echo e(route('reservas.index')); ?>"><?php echo e(__('Reservations')); ?></a></li>
                        <li><a href="<?php echo e(route('pagos.index')); ?>"><?php echo e(__('Payments')); ?></a></li>
                        <li><a href="<?php echo e(route('tipo_habitaciones.index')); ?>"><?php echo e(__('Room Types')); ?></a></li>
                        <li><a href="<?php echo e(route('servicios.index')); ?>"><?php echo e(__('Services')); ?></a></li>
                        <li><a href="<?php echo e(route('reportes.index')); ?>"><?php echo e(__('Reports')); ?></a></li>
                    <?php endif; ?>
                    
                    <?php if(Auth::user()->role === 'gerente'): ?>
                        
                        <li><a href="<?php echo e(route('clientes.index')); ?>"><?php echo e(__('Clients')); ?></a></li>
                        <li><a href="<?php echo e(route('habitaciones.index')); ?>"><?php echo e(__('Rooms')); ?></a></li>
                        <li><a href="<?php echo e(route('reservas.index')); ?>"><?php echo e(__('Reservations')); ?></a></li>
                        <li><a href="<?php echo e(route('pagos.index')); ?>"><?php echo e(__('Payments')); ?></a></li>
                        <li><a href="<?php echo e(route('tipo_habitaciones.index')); ?>"><?php echo e(__('Room Types')); ?></a></li>
                        <li><a href="<?php echo e(route('servicios.index')); ?>"><?php echo e(__('Services')); ?></a></li>
                        <li><a href="<?php echo e(route('reportes.index')); ?>"><?php echo e(__('Reports')); ?></a></li>
                    <?php endif; ?>
                    
                    <?php if(Auth::user()->role === 'recepcion'): ?>
                        
                        <li><a href="<?php echo e(route('clientes.index')); ?>"><?php echo e(__('Clients')); ?></a></li>
                        <li><a href="<?php echo e(route('habitaciones.index')); ?>"><?php echo e(__('Rooms')); ?></a></li>
                        <li><a href="<?php echo e(route('reservas.index')); ?>"><?php echo e(__('Reservations')); ?></a></li>
                        <li><a href="<?php echo e(route('pagos.index')); ?>"><?php echo e(__('Payments')); ?></a></li>
                    <?php endif; ?>

                    <?php if(Auth::user()->role === 'limpieza'): ?>
                        
                        <li><a href="<?php echo e(route('limpieza.habitaciones')); ?>"><?php echo e(__('Rooms')); ?></a></li>
                    <?php endif; ?>

                    <?php if(Auth::user()->role === 'mantenimiento'): ?>
                        
                        <li><a href="<?php echo e(route('mantenimiento.habitaciones')); ?>"><?php echo e(__('Rooms')); ?></a></li>
                    <?php endif; ?>
                </ul>
            </div>
            <div class="user-menu">
                <span><?php echo e(Auth::user()->name); ?></span>
                <form method="POST" action="<?php echo e(route('logout')); ?>" style="display: inline;">
                    <?php echo csrf_field(); ?>
                    <a href="<?php echo e(route('logout')); ?>"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();"
                            style="margin-left: 10px; color: #ef4444;">
                        (<?php echo e(__('Log Out')); ?>)
                    </a>
                </form>
            </div>
        </div>
    </div>
</nav>
<?php /**PATH C:\xampp\htdocs\hotel_systemax\resources\views/layouts/navigation.blade.php ENDPATH**/ ?>