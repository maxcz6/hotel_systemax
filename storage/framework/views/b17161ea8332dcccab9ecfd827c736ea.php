<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

        <title><?php echo e(config('app.name', 'Laravel')); ?></title>

        <!-- Styles -->
        <link rel="stylesheet" href="<?php echo e(asset('css/responsive.css')); ?>">
    </head>
    <body>
        <?php echo $__env->make('layouts.navigation', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <!-- Page Heading -->
        <?php if(isset($header)): ?>
            <header>
                <div class="container">
                    <?php echo e($header); ?>

                </div>
            </header>
        <?php endif; ?>

        <!-- Page Content -->
        <main class="container">
            <?php echo e($slot); ?>

        </main>
        
        <script src="<?php echo e(asset('js/script.js')); ?>"></script>
    </body>
</html>
<?php /**PATH C:\xampp\htdocs\hotel_systemax\resources\views/layouts/app.blade.php ENDPATH**/ ?>