<?php $__env->startSection('title','المسارات المهنية'); ?>
<?php $__env->startSection('content'); ?>
<h1 class="text-2xl lg:text-3xl font-black text-primary mb-2">المسارات المهنية</h1>
<p class="text-tertiary text-sm mb-6">مسارات موثقة لتفعيل دورك في مجتمع النخبة.</p>

<div class="grid md:grid-cols-2 lg:grid-cols-3 gap-5">
    <?php $__currentLoopData = $tracks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <a href="<?php echo e(route('career-tracks.show',$t['slug'])); ?>" class="card p-6 hover:shadow-card-hover transition">
        <div class="w-12 h-12 rounded-xl bg-primary/10 text-primary grid place-items-center text-2xl mb-4"><?php echo e($t['icon']); ?></div>
        <h3 class="font-bold text-primary mb-2"><?php echo e($t['title']); ?></h3>
        <p class="text-sm text-tertiary mb-4"><?php echo e($t['subtitle']); ?></p>
        <div class="flex items-center justify-between text-xs">
            <span class="badge <?php echo e($t['statusColor']); ?>"><?php echo e($t['status']); ?></span>
            <span class="text-primary font-bold">التفاصيل ←</span>
        </div>
    </a>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\E\iti-open-source\Freelance\elitetech-laravel\resources\views\career-tracks\index.blade.php ENDPATH**/ ?>