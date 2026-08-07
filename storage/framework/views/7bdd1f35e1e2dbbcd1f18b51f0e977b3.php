<?php $__env->startSection('title', __('admin.nav.reports')); ?>
<?php $__env->startSection('content'); ?>
<h1 class="text-2xl font-black text-primary mb-2"><?php echo e(app()->getLocale()==='ar' ? 'الإحصائيات والتحليلات' : 'Statistics & Analytics'); ?></h1>
<p class="text-sm text-tertiary mb-6"><?php echo e(app()->getLocale()==='ar' ? 'نمو المجتمع، معدل التحويل، ومتوسط استجابة KYC' : 'Community growth, conversion rate, and average KYC response time.'); ?></p>

<div class="grid md:grid-cols-4 gap-4 mb-8">
    <div class="card p-5">
        <div class="text-xs text-tertiary"><?php echo e(__('admin.stats.published')); ?></div>
        <div class="text-3xl font-black text-primary"><?php echo e($ideasPublished); ?></div>
        <div class="text-xs text-tertiary"><?php echo e(app()->getLocale()==='ar' ? 'من أصل' : 'of'); ?> <?php echo e($ideasTotal); ?></div>
    </div>
    <div class="card p-5">
        <div class="text-xs text-tertiary"><?php echo e(__('admin.nav.implementations')); ?></div>
        <div class="text-3xl font-black text-primary"><?php echo e($implementStarted); ?></div>
    </div>
    <div class="card p-5">
        <div class="text-xs text-tertiary"><?php echo e(__('admin.stats.conversion')); ?></div>
        <div class="text-3xl font-black text-secondary"><?php echo e($conversion); ?>%</div>
        <div class="text-xs text-tertiary"><?php echo e(__('admin.stats.conversion_desc')); ?></div>
    </div>
    <div class="card p-5">
        <div class="text-xs text-tertiary"><?php echo e(__('admin.stats.avg_kyc_sla')); ?></div>
        <div class="text-3xl font-black text-primary"><?php echo e($avgKycHours !== null ? round($avgKycHours, 1) : '—'); ?></div>
        <div class="text-xs text-tertiary"><?php echo e(app()->getLocale()==='ar' ? 'ساعة حتى المراجعة' : 'hours until review'); ?></div>
    </div>
</div>

<div class="grid lg:grid-cols-2 gap-6">
    <div class="card p-6">
        <h3 class="font-bold text-primary mb-4"><?php echo e(app()->getLocale()==='ar' ? 'المستخدمون حسب المسار' : 'Users by Path'); ?></h3>
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
                <p class="text-sm text-tertiary"><?php echo e(app()->getLocale()==='ar' ? 'لا بيانات.' : 'No data.'); ?></p>
            <?php endif; ?>
        </div>
    </div>
    <div class="card p-6">
        <h3 class="font-bold text-primary mb-4"><?php echo e(app()->getLocale()==='ar' ? 'مستخدمون جدد (14 يوم)' : 'New Users (14 days)'); ?></h3>
        <div class="space-y-2 max-h-72 overflow-y-auto">
            <?php $__empty_1 = true; $__currentLoopData = $newUsersDaily; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="flex justify-between text-sm border-b border-mist py-2">
                    <span class="text-tertiary"><?php echo e($row->d); ?></span>
                    <span class="font-bold text-primary"><?php echo e($row->c); ?></span>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <p class="text-sm text-tertiary"><?php echo e(app()->getLocale()==='ar' ? 'لا تسجيلات حديثة.' : 'No recent registrations.'); ?></p>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\E\iti-open-source\Freelance\elitetech-laravel\resources\views/admin/reports.blade.php ENDPATH**/ ?>