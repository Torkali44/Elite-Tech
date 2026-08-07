<?php $__env->startSection('title', __('auth.reset_title')); ?>
<?php $__env->startSection('content'); ?>
<h2 class="text-2xl font-black text-primary mb-2"><?php echo e(__('auth.reset_title')); ?></h2>

<?php if($errors->any()): ?>
    <div class="mb-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-sm px-4 py-3"><?php echo e($errors->first()); ?></div>
<?php endif; ?>

<form method="POST" action="<?php echo e(route('password.update')); ?>" class="space-y-4">
    <?php echo csrf_field(); ?>
    <input type="hidden" name="token" value="<?php echo e($token); ?>">
    <div>
        <label class="block text-sm font-semibold text-primary mb-1.5"><?php echo e(__('auth.email')); ?></label>
        <input type="email" name="email" value="<?php echo e(old('email', $email)); ?>" class="input" required>
    </div>
    <div>
        <label class="block text-sm font-semibold text-primary mb-1.5"><?php echo e(__('auth.new_password')); ?></label>
        <input type="password" name="password" class="input" required minlength="8" autocomplete="new-password">
    </div>
    <div>
        <label class="block text-sm font-semibold text-primary mb-1.5"><?php echo e(__('auth.confirm_password')); ?></label>
        <input type="password" name="password_confirmation" class="input" required minlength="8" autocomplete="new-password">
    </div>
    <button type="submit" class="btn-primary"><?php echo e(__('auth.submit_reset')); ?></button>
</form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.auth', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\E\iti-open-source\Freelance\elitetech-laravel\resources\views\auth\reset.blade.php ENDPATH**/ ?>