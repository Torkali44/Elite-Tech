<?php $__env->startSection('title', __('admin.implementations_title')); ?>
<?php $__env->startSection('content'); ?>
<h1 class="text-2xl font-black text-primary mb-2"><?php echo e(__('admin.implementations_title')); ?></h1>
<p class="text-sm text-tertiary mb-6"><?php echo e(__('admin.implementations_desc')); ?></p>

<div class="space-y-4">
<?php $__empty_1 = true; $__currentLoopData = $requests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
<div class="card p-5" x-data="{ rej:false }">
    <div class="flex flex-wrap justify-between gap-3">
        <div>
            <div class="font-bold text-primary"><?php echo e($r->idea->title ?? '—'); ?></div>
            <div class="text-xs text-tertiary mt-1">
                <?php echo e(__('admin.applicant')); ?>: <?php echo e($r->user->name ?? '—'); ?> (<?php echo e($r->user->email ?? ''); ?>)
                · <?php echo e(__('admin.idea_owner')); ?>: <?php echo e($r->idea->user->name ?? '—'); ?>

                · <?php echo e($r->via === 'elite_tech' ? __('admin.elite_tech_via') : __('admin.direct_via')); ?>

            </div>
            <?php if($r->note): ?><p class="text-xs mt-2 bg-mist rounded-lg px-2 py-1"><?php echo e($r->note); ?></p><?php endif; ?>
        </div>
        <span class="badge bg-mist text-primary"><?php echo e($r->status); ?></span>
    </div>
    <div class="flex gap-2 mt-3 items-center">
        <a href="<?php echo e(route('admin.implementations.show', $r->id)); ?>" class="btn-ghost text-sm !py-2 border border-mist hover:bg-mist"><?php echo e(__('admin.view_details')); ?></a>
        <?php if($r->status === 'pending'): ?>
        <form method="POST" action="<?php echo e(route('admin.implementations.approve', $r->id)); ?>"><?php echo csrf_field(); ?>
            <button class="btn-primary text-sm !py-2"><?php echo e(__('admin.approve')); ?></button>
        </form>
        <button type="button" @click="rej=!rej" class="btn-outline text-sm !py-2 !text-rose-600 !border-rose-300"><?php echo e(__('admin.reject')); ?></button>
        <?php endif; ?>
    </div>
    <?php if($r->status === 'pending'): ?>
    <form x-show="rej" x-cloak method="POST" action="<?php echo e(route('admin.implementations.reject', $r->id)); ?>" class="mt-2 space-y-2"><?php echo csrf_field(); ?>
        <textarea name="reason" class="input" rows="2" required placeholder="<?php echo e(__('admin.rejection_reason')); ?>"></textarea>
        <button class="btn-secondary text-sm !py-2"><?php echo e(__('admin.confirm_reject')); ?></button>
    </form>
    <?php endif; ?>
</div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
<div class="card p-10 text-center text-tertiary"><?php echo e(__('admin.no_requests')); ?></div>
<?php endif; ?>
</div>
<div class="mt-6"><?php echo e($requests->links()); ?></div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\E\iti-open-source\Freelance\elitetech-laravel\resources\views/admin/implementations.blade.php ENDPATH**/ ?>