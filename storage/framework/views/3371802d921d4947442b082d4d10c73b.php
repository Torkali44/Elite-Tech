<?php $__env->startSection('title', $title); ?>

<?php $__env->startSection('content'); ?>
<section class="bg-primary text-white py-12">
    <div class="max-w-3xl mx-auto px-4">
        <h1 class="text-3xl sm:text-4xl font-extrabold mb-2"><?php echo e($title); ?></h1>
        <p class="text-white/70 text-sm">آخر تحديث: <?php echo e($updated); ?></p>
    </div>
</section>

<article class="max-w-3xl mx-auto px-4 py-12 space-y-6">
    <?php $__currentLoopData = $sections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="card p-6">
            <h2 class="font-black text-primary text-lg mb-2"><?php echo e($s['h']); ?></h2>
            <p class="text-sm text-tertiary leading-relaxed"><?php echo e($s['p']); ?></p>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    <div class="flex flex-wrap gap-3 text-sm pt-2">
        <a href="<?php echo e(route('terms')); ?>" class="<?php echo e(request()->routeIs('terms') ? 'text-primary font-bold' : 'text-tertiary hover:text-primary'); ?>">الشروط والأحكام</a>
        <span class="text-mist">|</span>
        <a href="<?php echo e(route('privacy')); ?>" class="<?php echo e(request()->routeIs('privacy') ? 'text-primary font-bold' : 'text-tertiary hover:text-primary'); ?>">سياسة الخصوصية</a>
        <span class="text-mist">|</span>
        <a href="<?php echo e(route('agreement')); ?>" class="<?php echo e(request()->routeIs('agreement') ? 'text-primary font-bold' : 'text-tertiary hover:text-primary'); ?>">اتفاقية الاستخدام</a>
    </div>
</article>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\E\iti-open-source\Freelance\elitetech-laravel\resources\views\pages\legal.blade.php ENDPATH**/ ?>