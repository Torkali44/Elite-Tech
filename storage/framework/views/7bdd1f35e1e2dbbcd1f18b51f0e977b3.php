<?php $__env->startSection('title','الإحصائيات'); ?>
<?php $__env->startSection('content'); ?>
<h1 class="text-2xl font-black text-primary mb-2">الإحصائيات والتحليلات</h1>
<p class="text-sm text-tertiary mb-6">نمو المجتمع، معدل التحويل، ومتوسط استجابة KYC</p>

<div class="grid md:grid-cols-4 gap-4 mb-8">
    <div class="card p-5">
        <div class="text-xs text-tertiary">أفكار منشورة</div>
        <div class="text-3xl font-black text-primary"><?php echo e($ideasPublished); ?></div>
        <div class="text-xs text-tertiary">من أصل <?php echo e($ideasTotal); ?></div>
    </div>
    <div class="card p-5">
        <div class="text-xs text-tertiary">طلبات تنفيذ</div>
        <div class="text-3xl font-black text-primary"><?php echo e($implementStarted); ?></div>
    </div>
    <div class="card p-5">
        <div class="text-xs text-tertiary">Conversion Rate</div>
        <div class="text-3xl font-black text-secondary"><?php echo e($conversion); ?>%</div>
        <div class="text-xs text-tertiary">تنفيذ ÷ أفكار منشورة</div>
    </div>
    <div class="card p-5">
        <div class="text-xs text-tertiary">متوسط SLA لـ KYC</div>
        <div class="text-3xl font-black text-primary"><?php echo e($avgKycHours !== null ? round($avgKycHours, 1) : '—'); ?></div>
        <div class="text-xs text-tertiary">ساعة حتى المراجعة</div>
    </div>
</div>

<div class="grid lg:grid-cols-2 gap-6">
    <div class="card p-6">
        <h3 class="font-bold text-primary mb-4">المستخدمون حسب المسار</h3>
        <div class="space-y-3">
            <?php $__empty_1 = true; $__currentLoopData = $byRole; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role => $total): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-tertiary"><?php echo e($role); ?></span>
                    <span class="font-black text-primary"><?php echo e($total); ?></span>
                </div>
                <div class="h-2 bg-mist rounded-full overflow-hidden">
                    <div class="h-full bg-secondary rounded-full" style="width: <?php echo e(min(100, ($total / max(1, array_sum($byRole->toArray()))) * 100)); ?>%"></div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <p class="text-sm text-tertiary">لا بيانات.</p>
            <?php endif; ?>
        </div>
    </div>
    <div class="card p-6">
        <h3 class="font-bold text-primary mb-4">مستخدمون جدد (14 يوم)</h3>
        <div class="space-y-2 max-h-72 overflow-y-auto">
            <?php $__empty_1 = true; $__currentLoopData = $newUsersDaily; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="flex justify-between text-sm border-b border-mist py-2">
                    <span class="text-tertiary"><?php echo e($row->d); ?></span>
                    <span class="font-bold text-primary"><?php echo e($row->c); ?></span>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <p class="text-sm text-tertiary">لا تسجيلات حديثة.</p>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\E\iti-open-source\Freelance\elitetech-laravel\resources\views/admin/reports.blade.php ENDPATH**/ ?>