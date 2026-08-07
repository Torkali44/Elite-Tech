<?php $__env->startSection('title', __('general.impl_req_title')); ?>
<?php $__env->startSection('content'); ?>
<div class="mb-6">
    <h1 class="text-2xl font-black text-primary mb-1"><?php echo e(__('general.impl_req_title')); ?></h1>
    <p class="text-sm text-tertiary"><?php echo e(__('general.impl_req_subtitle')); ?></p>
</div>

<div class="space-y-4">
<?php $__empty_1 = true; $__currentLoopData = $requests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
<div class="card p-5">
    <div class="flex flex-wrap justify-between gap-3">
        <div>
            <div class="font-bold text-primary"><?php echo e($r->idea->title ?? '—'); ?></div>
            <div class="text-sm text-tertiary mt-1">
                <?php echo e(__('general.from_label')); ?>: <b><?php echo e($r->user->name ?? '—'); ?></b>
                · <?php echo e($r->via === 'elite_tech' ? __('general.via_elite') : __('general.via_direct')); ?>

                · <?php echo e($r->created_at->diffForHumans()); ?>

            </div>
            <?php if($r->note): ?><p class="text-xs text-tertiary mt-2 bg-mist rounded-lg px-3 py-2"><?php echo e($r->note); ?></p><?php endif; ?>
        </div>
        <span class="badge <?php echo e($r->status==='pending'?'bg-amber-50 text-amber-700':($r->status==='approved'?'bg-emerald-50 text-emerald-700':'bg-rose-50 text-rose-700')); ?>"><?php echo e($r->status); ?></span>
    </div>
    <?php if($r->status === 'pending'): ?>
    <div class="flex flex-wrap gap-2 mt-4 pt-3 border-t border-mist">
        <form method="POST" action="<?php echo e(route('dashboard.implementRespond', $r->id)); ?>"><?php echo csrf_field(); ?>
            <input type="hidden" name="action" value="approved">
            <button class="btn-primary text-sm !py-2"><?php echo e(__('general.accept_btn')); ?></button>
        </form>
        <form method="POST" action="<?php echo e(route('dashboard.implementRespond', $r->id)); ?>" class="flex gap-2 flex-1 min-w-[200px]"><?php echo csrf_field(); ?>
            <input type="hidden" name="action" value="rejected">
            <input name="note" class="input !py-2 text-sm" placeholder="<?php echo e(__('general.rejection_reason_placeholder')); ?>">
            <button class="btn-outline text-sm !py-2 !border-rose-300 !text-rose-600"><?php echo e(__('general.reject_btn')); ?></button>
        </form>
        <a href="<?php echo e(route('network.index', ['with' => $r->user_id])); ?>" class="btn-ghost text-sm"><?php echo e(__('general.message_btn')); ?></a>
    </div>
    <?php endif; ?>
</div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
<div class="card p-10 text-center text-tertiary"><?php echo e(__('general.no_impl_requests')); ?></div>
<?php endif; ?>
</div>
<div class="mt-6"><?php echo e($requests->links()); ?></div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\E\iti-open-source\Freelance\elitetech-laravel\resources\views\dashboards\implement-requests.blade.php ENDPATH**/ ?>