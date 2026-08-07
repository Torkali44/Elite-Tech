<?php $__env->startSection('title', __('admin.overview')); ?>
<?php $__env->startSection('content'); ?>
<div class="mb-6">
    <h1 class="text-2xl lg:text-3xl font-black text-primary mb-1"><?php echo e(__('admin.overview')); ?></h1>
    <p class="text-tertiary text-sm"><?php echo e(__('admin.overview_desc')); ?></p>
</div>

<div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-8">
    <div class="card p-5">
        <div class="text-xs text-tertiary mb-1"><?php echo e(__('admin.stats.users')); ?></div>
        <div class="text-3xl font-black text-primary"><?php echo e($stats['users']); ?></div>
    </div>
    <div class="card p-5">
        <div class="text-xs text-tertiary mb-1"><?php echo e(__('admin.stats.ideas')); ?></div>
        <div class="text-3xl font-black text-primary"><?php echo e($stats['ideas']); ?></div>
    </div>
    <div class="card p-5">
        <div class="text-xs text-tertiary mb-1"><?php echo e(__('admin.stats.published')); ?></div>
        <div class="text-3xl font-black text-primary"><?php echo e($stats['published']); ?></div>
    </div>
    <div class="card p-5 border-<?php echo e(app()->getLocale() === 'ar' ? 'r' : 'l'); ?>-4 border-indigo-500">
        <div class="text-xs font-bold text-indigo-600 mb-1"><?php echo e(__('admin.stats.conversion')); ?></div>
        <div class="text-3xl font-black text-indigo-700"><?php echo e($stats['conversion']); ?>%</div>
        <div class="text-[10px] text-tertiary mt-1"><?php echo e(__('admin.stats.conversion_desc')); ?></div>
    </div>
    <div class="card p-5 border-<?php echo e(app()->getLocale() === 'ar' ? 'r' : 'l'); ?>-4 border-emerald-500">
        <div class="text-xs font-bold text-emerald-600 mb-1"><?php echo e(__('admin.stats.avg_kyc_sla')); ?></div>
        <div class="text-2xl font-black text-emerald-700"><?php echo e($stats['avg_kyc_sla']); ?></div>
        <div class="text-[10px] text-tertiary mt-1"><?php echo e(__('admin.stats.avg_kyc_desc')); ?></div>
    </div>
</div>

<div class="grid lg:grid-cols-2 gap-6">
    <div class="card p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold text-primary"><?php echo e(__('admin.recent_users')); ?></h3>
            <a href="<?php echo e(route('admin.users')); ?>" class="text-xs text-secondary font-bold"><?php echo e(__('admin.view_all')); ?></a>
        </div>
        <div class="space-y-3">
            <?php $__empty_1 = true; $__currentLoopData = $recentUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-primary/10 grid place-items-center text-primary font-bold text-sm"><?php echo e(mb_substr($u->name,0,1)); ?></div>
                <div class="flex-1 min-w-0">
                    <div class="font-bold text-primary text-sm truncate"><?php echo e($u->name); ?></div>
                    <div class="text-xs text-tertiary truncate"><?php echo e($u->email); ?> · <?php echo e($u->roleLabel()); ?></div>
                </div>
                <span class="badge bg-mist text-tertiary"><?php echo e($u->kyc_status); ?></span>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <p class="text-sm text-tertiary"><?php echo e(__('admin.no_users')); ?></p>
            <?php endif; ?>
        </div>
    </div>

    <div class="card p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold text-primary"><?php echo e(__('admin.pending_kyc')); ?></h3>
            <a href="<?php echo e(route('admin.verifications')); ?>" class="text-xs text-secondary font-bold"><?php echo e(__('admin.view_all')); ?></a>
        </div>
        <div class="space-y-3">
            <?php $__empty_1 = true; $__currentLoopData = $pendingKyc; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-secondary/10 grid place-items-center text-secondary font-bold">KYC</div>
                <div class="flex-1 min-w-0">
                    <div class="font-bold text-primary text-sm"><?php echo e($v->user->name ?? '—'); ?></div>
                    <div class="text-xs text-tertiary"><?php echo e($v->purposeLabel()); ?> · <?php echo e($v->created_at->diffForHumans()); ?></div>
                </div>
                <a href="<?php echo e(route('admin.verifications')); ?>" class="text-xs bg-primary text-white px-3 py-1.5 rounded-lg font-bold"><?php echo e(__('admin.review')); ?></a>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <p class="text-sm text-tertiary"><?php echo e(__('admin.no_pending_kyc')); ?></p>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\E\iti-open-source\Freelance\elitetech-laravel\resources\views/admin/dashboard.blade.php ENDPATH**/ ?>