<?php $__env->startSection('title','لوحة الأدمن'); ?>
<?php $__env->startSection('content'); ?>
<div class="mb-6">
    <h1 class="text-2xl lg:text-3xl font-black text-primary mb-1">نظرة عامة</h1>
    <p class="text-tertiary text-sm">مراقبة المجتمع، طلبات KYC، ومراجعة الأفكار.</p>
</div>

<div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-8">
    <?php $__currentLoopData = [
        ['المستخدمون', $stats['users']],
        ['الأفكار', $stats['ideas']],
        ['منشورة', $stats['published']],
        ['KYC معلّق', $stats['kyc_pending']],
        ['تنفيذ معلّق', $stats['implement_pending']],
    ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="card p-5">
        <div class="text-xs text-tertiary mb-1"><?php echo e($s[0]); ?></div>
        <div class="text-3xl font-black text-primary"><?php echo e($s[1]); ?></div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>

<div class="grid lg:grid-cols-2 gap-6">
    <div class="card p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold text-primary">آخر المستخدمين</h3>
            <a href="<?php echo e(route('admin.users')); ?>" class="text-xs text-secondary font-bold">عرض الكل ←</a>
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
                <p class="text-sm text-tertiary">لا مستخدمين بعد.</p>
            <?php endif; ?>
        </div>
    </div>

    <div class="card p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold text-primary">طلبات KYC معلّقة</h3>
            <a href="<?php echo e(route('admin.verifications')); ?>" class="text-xs text-secondary font-bold">عرض الكل ←</a>
        </div>
        <div class="space-y-3">
            <?php $__empty_1 = true; $__currentLoopData = $pendingKyc; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-secondary/10 grid place-items-center text-secondary font-bold">KYC</div>
                <div class="flex-1 min-w-0">
                    <div class="font-bold text-primary text-sm"><?php echo e($v->user->name ?? '—'); ?></div>
                    <div class="text-xs text-tertiary"><?php echo e($v->purposeLabel()); ?> · <?php echo e($v->created_at->diffForHumans()); ?></div>
                </div>
                <a href="<?php echo e(route('admin.verifications')); ?>" class="text-xs bg-primary text-white px-3 py-1.5 rounded-lg font-bold">مراجعة</a>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <p class="text-sm text-tertiary">لا طلبات معلّقة.</p>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\E\iti-open-source\Freelance\elitetech-laravel\resources\views/admin/dashboard.blade.php ENDPATH**/ ?>