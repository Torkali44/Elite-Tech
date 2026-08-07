<?php $__env->startSection('title', $member['name']); ?>
<?php $__env->startSection('content'); ?>
<div class="max-w-5xl mx-auto px-4 py-10">
    <div class="card p-6 lg:p-8">
        <div class="flex flex-wrap items-start gap-6">
            <div class="w-28 h-28 rounded-2xl bg-primary text-white grid place-items-center text-4xl font-black"><?php echo e(mb_substr($member['name'],0,1)); ?></div>
            <div class="flex-1 min-w-0">
                <h1 class="text-2xl font-black text-primary mb-1"><?php echo e($member['name']); ?></h1>
                <p class="text-tertiary"><?php echo e($member['title']); ?></p>
                <div class="flex flex-wrap gap-2 mt-3">
                    <span class="badge bg-emerald-50 text-emerald-600">✓ Elite Verified</span>
                    <span class="badge bg-primary/10 text-primary">Software Developer</span>
                </div>
            </div>
            <button class="btn-secondary">💬 تواصل معي</button>
        </div>

        <div class="grid md:grid-cols-3 gap-4 mt-8 pt-8 border-t border-slate-100">
            <div><div class="text-xs text-tertiary">الخبرة</div><div class="font-bold text-primary">7 سنوات</div></div>
            <div><div class="text-xs text-tertiary">الموقع</div><div class="font-bold text-primary">القاهرة، مصر</div></div>
            <div><div class="text-xs text-tertiary">الحالة</div><div class="font-bold text-emerald-600">متاح للعمل</div></div>
        </div>

        <div class="mt-8">
            <h3 class="font-bold text-primary mb-3">نبذة</h3>
            <p class="text-sm text-tertiary leading-relaxed"><?php echo e($member['bio']); ?></p>
        </div>

        <div class="mt-8">
            <h3 class="font-bold text-primary mb-3">المهارات</h3>
            <div class="flex flex-wrap gap-2">
                <?php $__currentLoopData = $member['skills']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <span class="badge bg-neutral text-primary border border-slate-200"><?php echo e($s); ?></span>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\E\iti-open-source\Freelance\elitetech-laravel\resources\views\community\show.blade.php ENDPATH**/ ?>