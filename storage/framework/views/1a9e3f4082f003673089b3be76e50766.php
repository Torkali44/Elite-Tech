<?php $__env->startSection('title','الموجهون'); ?>
<?php $__env->startSection('content'); ?>
<div class="max-w-7xl mx-auto px-4 py-10">
    <h1 class="text-3xl font-black text-primary mb-2">الموجهون (Mentors)</h1>
    <p class="text-tertiary mb-8">تواصل مع خبراء متخصصين في مجالات مختلفة لتوجيه مسيرتك المهنية.</p>
    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-5">
        <?php $__currentLoopData = $mentors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="card p-6 text-center">
            <div class="w-20 h-20 rounded-full bg-primary/10 grid place-items-center mx-auto mb-4 text-primary text-2xl font-black"><?php echo e(mb_substr($m['name'],0,1)); ?></div>
            <h3 class="font-bold text-primary"><?php echo e($m['name']); ?></h3>
            <p class="text-xs text-tertiary mb-3"><?php echo e($m['expertise']); ?></p>
            <div class="flex flex-wrap gap-1.5 justify-center mb-4">
                <?php $__currentLoopData = $m['tags']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><span class="badge bg-neutral text-primary text-[10px]"><?php echo e($t); ?></span><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <button class="btn-outline w-full text-sm">طلب جلسة إرشاد</button>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('head'); ?>
<style>.btn-outline { @apply inline-flex items-center justify-center gap-2 border border-primary text-primary font-semibold px-4 py-2 rounded-lg hover:bg-primary hover:text-white; }</style>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\E\iti-open-source\Freelance\elitetech-laravel\resources\views\mentors\index.blade.php ENDPATH**/ ?>