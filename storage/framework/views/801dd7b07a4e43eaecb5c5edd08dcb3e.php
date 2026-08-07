<?php $__env->startSection('title', __('auth.register_title')); ?>
<?php $__env->startSection('content'); ?>
<h2 class="text-2xl font-extrabold text-primary mb-1"><?php echo e(__('auth.register_title')); ?></h2>
<p class="text-sm text-tertiary mb-7"><?php echo e(__('auth.register_subtitle')); ?></p>

<div class="flex bg-mist rounded-lg p-1 mb-6 text-sm font-bold">
    <a href="<?php echo e(route('login')); ?>" class="flex-1 text-center py-2.5 rounded-md text-tertiary hover:text-primary"><?php echo e(__('auth.login_title')); ?></a>
    <a href="<?php echo e(route('register')); ?>" class="flex-1 text-center py-2.5 rounded-md bg-white text-primary"><?php echo e(__('auth.new_account')); ?></a>
</div>

<?php if($errors->any()): ?>
    <div class="mb-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-sm px-4 py-3 space-y-1">
        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><div><?php echo e($error); ?></div><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
<?php endif; ?>

<form action="<?php echo e(route('register')); ?>" method="POST" class="space-y-4">
    <?php echo csrf_field(); ?>
    <div>
        <label class="block text-sm font-bold text-primary mb-1.5"><?php echo e(__('auth.name')); ?></label>
        <input type="text" name="name" value="<?php echo e(old('name')); ?>" placeholder="<?php echo e(__('auth.name_placeholder')); ?>" class="input" required autofocus>
    </div>
    <div>
        <label class="block text-sm font-bold text-primary mb-1.5"><?php echo e(__('auth.email')); ?></label>
        <input type="email" name="email" value="<?php echo e(old('email')); ?>" placeholder="<?php echo e(__('auth.email_placeholder')); ?>" class="input" required>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        <div>
            <label class="block text-sm font-bold text-primary mb-1.5"><?php echo e(__('auth.password')); ?></label>
            <input type="password" name="password" class="input" required minlength="8">
        </div>
        <div>
            <label class="block text-sm font-bold text-primary mb-1.5"><?php echo e(__('auth.password_confirm')); ?></label>
            <input type="password" name="password_confirmation" class="input" required minlength="8">
        </div>
    </div>
    <label class="flex items-start gap-2.5 text-sm text-tertiary">
        <input type="checkbox" name="terms" value="1" class="mt-1 rounded accent-primary" required>
        <span><?php echo e(__('auth.terms_agree')); ?> <a href="<?php echo e(route('terms')); ?>" class="text-secondary underline" target="_blank"><?php echo e(__('auth.terms_link')); ?></a>
        <?php echo e(__('auth.and')); ?> <a href="<?php echo e(route('privacy')); ?>" class="text-secondary underline" target="_blank"><?php echo e(__('auth.privacy_link')); ?></a></span>
    </label>
    <button type="submit" class="btn-primary w-full"><?php echo e(__('auth.submit_register')); ?></button>
</form>

<p class="text-center text-sm text-tertiary mt-6">
    <?php echo e(__('auth.already_have_account')); ?> <a href="<?php echo e(route('login')); ?>" class="text-primary font-bold hover:underline"><?php echo e(__('auth.login_title')); ?></a>
</p>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.auth', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\E\iti-open-source\Freelance\elitetech-laravel\resources\views\auth\register.blade.php ENDPATH**/ ?>