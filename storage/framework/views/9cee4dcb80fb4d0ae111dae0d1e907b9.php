<?php $__env->startSection('title', __('admin.nav.kyc')); ?>
<?php $__env->startSection('content'); ?>
<div class="mb-6 flex flex-wrap items-end justify-between gap-3">
    <div>
        <h1 class="text-2xl font-black text-primary mb-1"><?php echo e(__('admin.nav.kyc')); ?></h1>
        <p class="text-sm text-tertiary"><?php echo e(app()->getLocale()==='ar' ? 'موافقة · رفض مع سبب · المستندات للإدارة فقط' : 'Approve · Reject with reason · Documents for admin only'); ?></p>
    </div>
    <form class="flex gap-2">
        <select name="status" class="input !py-2" onchange="this.form.submit()">
            <option value="pending" <?php if(request('status','pending')==='pending'): echo 'selected'; endif; ?>><?php echo e(app()->getLocale()==='ar' ? 'معلّق' : 'Pending'); ?></option>
            <option value="approved" <?php if(request('status')==='approved'): echo 'selected'; endif; ?>><?php echo e(app()->getLocale()==='ar' ? 'موافق' : 'Approved'); ?></option>
            <option value="rejected" <?php if(request('status')==='rejected'): echo 'selected'; endif; ?>><?php echo e(app()->getLocale()==='ar' ? 'مرفوض' : 'Rejected'); ?></option>
            <option value="" <?php if(request()->has('status') && request('status')===''): echo 'selected'; endif; ?>><?php echo e(__('general.all')); ?></option>
        </select>
    </form>
</div>

<div class="space-y-4">
<?php $__empty_1 = true; $__currentLoopData = $verifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
<div class="card p-5" x-data="{ reject:false, notes:false }">
    <div class="flex flex-wrap gap-4 items-start justify-between">
        <div>
            <div class="font-bold text-primary text-lg"><?php echo e($v->user->name ?? '—'); ?></div>
            <div class="text-xs text-tertiary mb-2"><?php echo e($v->user->email ?? ''); ?> · #<?php echo e($v->user_id); ?> · <?php echo e($v->user->roleLabel() ?? ''); ?></div>
            <div class="flex flex-wrap gap-2">
                <span class="badge bg-primary/10 text-primary"><?php echo e($v->purposeLabel()); ?></span>
                <span class="badge bg-mist text-tertiary"><?php echo e($v->doc_type); ?></span>
                <span class="badge <?php echo e($v->status==='pending'?'bg-amber-50 text-amber-700':($v->status==='approved'?'bg-emerald-50 text-emerald-700':'bg-rose-50 text-rose-700')); ?>"><?php echo e($v->status); ?></span>
            </div>
            <?php if($v->user?->admin_notes): ?>
                <p class="text-xs text-tertiary mt-2 bg-mist rounded-lg px-2 py-1"><?php echo e(app()->getLocale()==='ar' ? 'ملاحظة داخلية:' : 'Internal note:'); ?> <?php echo e($v->user->admin_notes); ?></p>
            <?php endif; ?>
        </div>
        <div class="text-xs text-tertiary"><?php echo e($v->created_at->format('Y-m-d H:i')); ?></div>
    </div>

    <div class="grid sm:grid-cols-3 gap-3 mt-4">
        <?php if($v->doc_type === 'reevaluation'): ?>
            <div class="sm:col-span-3 bg-amber-50 border border-amber-200 rounded-xl p-4 text-sm text-amber-900">
                <?php echo e(app()->getLocale()==='ar' ? 'طلب إعادة تقييم بسبب تعديل بيانات حسّاسة — لا مستندات جديدة مطلوبة بالضرورة.' : 'Re-evaluation request due to sensitive data change — no new documents necessarily required.'); ?>

                <?php if($v->admin_notes): ?><div class="mt-1 font-bold"><?php echo e($v->admin_notes); ?></div><?php endif; ?>
            </div>
        <?php else: ?>
            <?php $__currentLoopData = [
                [app()->getLocale()==='ar' ? 'أمامية' : 'Front', 'id_front', $v->id_front],
                [app()->getLocale()==='ar' ? 'خلفية' : 'Back', 'id_back', $v->id_back],
                ['Selfie', 'selfie', $v->selfie]
            ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$label,$fieldKey,$path]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="bg-mist rounded-xl p-3 text-center text-xs">
                    <div class="font-bold text-primary mb-1"><?php echo e($label); ?></div>
                    <?php if($path): ?>
                        <a href="<?php echo e(route('admin.verifications.file', [$v->id, $fieldKey])); ?>" target="_blank" class="text-secondary font-bold underline"><?php echo e(app()->getLocale()==='ar' ? 'عرض المستند' : 'View Document'); ?></a>
                    <?php else: ?>
                        <span class="text-tertiary">—</span>
                    <?php endif; ?>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php endif; ?>
    </div>

    <?php if($v->status === 'pending'): ?>
    <div class="flex flex-wrap gap-2 mt-4 pt-4 border-t border-mist">
        <form method="POST" action="<?php echo e(route('admin.verifications.approve', $v->id)); ?>"><?php echo csrf_field(); ?>
            <button class="btn-primary text-sm !py-2"><?php echo e(__('admin.approve')); ?></button>
        </form>
        <button type="button" @click="reject=!reject" class="btn-outline text-sm !py-2 !border-rose-300 !text-rose-600"><?php echo e(__('admin.reject')); ?></button>
        <form method="POST" action="<?php echo e(route('admin.users.notes', $v->user_id)); ?>" class="flex-1 flex gap-2 min-w-[200px]"><?php echo csrf_field(); ?>
            <input name="admin_notes" class="input !py-2 text-xs" placeholder="<?php echo e(__('admin.internal_notes')); ?>" value="<?php echo e($v->user->admin_notes); ?>">
            <button class="btn-ghost text-xs"><?php echo e(__('admin.save_notes')); ?></button>
        </form>
    </div>
    <form x-show="reject" x-cloak method="POST" action="<?php echo e(route('admin.verifications.reject', $v->id)); ?>" class="mt-3 space-y-2">
        <?php echo csrf_field(); ?>
        <textarea name="reason" class="input" rows="2" placeholder="<?php echo e(__('admin.rejection_reason')); ?>" required></textarea>
        <button class="btn-secondary text-sm !py-2"><?php echo e(__('admin.confirm_reject')); ?></button>
    </form>
    <?php endif; ?>
</div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
<div class="card p-10 text-center text-tertiary"><?php echo e(app()->getLocale()==='ar' ? 'لا طلبات في هذه الحالة.' : 'No requests in this status.'); ?></div>
<?php endif; ?>
</div>
<div class="mt-6"><?php echo e($verifications->links()); ?></div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\E\iti-open-source\Freelance\elitetech-laravel\resources\views\admin\verifications.blade.php ENDPATH**/ ?>