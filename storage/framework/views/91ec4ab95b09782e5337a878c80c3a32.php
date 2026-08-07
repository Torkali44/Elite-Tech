<?php $__env->startSection('title', __('auth.verify_title')); ?>
<?php $__env->startSection('content'); ?>
<h2 class="text-2xl font-black text-primary mb-1"><?php echo e(__('auth.verify_title')); ?></h2>
<p class="text-sm text-tertiary mb-6">
    <?php echo e(__('auth.verify_subtitle')); ?><br>
    <span class="text-primary font-bold"><?php echo e(auth()->user()->email ?? ''); ?></span>
</p>

<?php if(session('ok')): ?>
    <div class="mb-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm px-4 py-3"><?php echo e(session('ok')); ?></div>
<?php endif; ?>
<?php if($errors->any()): ?>
    <div class="mb-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-sm px-4 py-3"><?php echo e($errors->first()); ?></div>
<?php endif; ?>

<form action="<?php echo e(route('auth.verify')); ?>" method="POST" class="space-y-5">
    <?php echo csrf_field(); ?>
    <div>
        <label class="block text-sm font-bold text-primary mb-1.5"><?php echo e(__('auth.otp_label')); ?></label>
        <input type="text" name="code" inputmode="numeric" pattern="[0-9]{6}" maxlength="6"
               class="input text-center text-2xl font-black tracking-[0.4em]" placeholder="<?php echo e(__('auth.otp_placeholder')); ?>" required autofocus>
    </div>
    <button type="submit" class="btn-primary w-full"><?php echo e(__('auth.submit_verify')); ?></button>
</form>

<p class="text-center text-sm text-tertiary mt-6">
    <?php echo e(__('auth.no_code_received')); ?>

    <a href="<?php echo e(route('auth.verify', ['resend' => 1])); ?>" class="text-secondary font-bold hover:underline"><?php echo e(__('auth.resend_code')); ?></a>
</p>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.auth', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\E\iti-open-source\Freelance\elitetech-laravel\resources\views\auth\verify.blade.php ENDPATH**/ ?>