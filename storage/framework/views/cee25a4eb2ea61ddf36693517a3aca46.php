<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $__env->yieldContent('title', 'Elite Tech Community — مجتمع النخبة التقنية'); ?></title>
    <meta name="description" content="<?php echo $__env->yieldContent('description', 'منصة لحرق الأفكار وتحويلها لمشاريع قابلة للتنفيذ — بيئة تشاركية شفافة وموثوقة.'); ?>">
    <?php echo $__env->make('partials.styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->yieldPushContent('head'); ?>
</head>
<body class="min-h-screen antialiased" x-data="{ gateOpen: false, gateMsg: '' }">
    <?php echo $__env->make('partials.navbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <main class="min-h-[70vh]">
        <?php if(session('ok')): ?>
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6 animate-fade-in">
                <div class="rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm px-4 py-3 font-medium"><?php echo e(session('ok')); ?></div>
            </div>
        <?php endif; ?>
        <?php if(session('error')): ?>
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6 animate-fade-in">
                <div class="rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-sm px-4 py-3 font-medium"><?php echo e(session('error')); ?></div>
            </div>
        <?php endif; ?>
        <?php echo $__env->yieldContent('content'); ?>
    </main>

    <?php echo $__env->make('partials.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('partials.gate-modal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH C:\E\iti-open-source\Freelance\elitetech-laravel\resources\views/layouts/app.blade.php ENDPATH**/ ?>