<?php $__env->startSection('title','تسجيل الدخول'); ?>
<?php $__env->startSection('content'); ?>
<h2 class="text-2xl font-extrabold text-primary mb-1">أهلاً بك في Elite Tech</h2>
<p class="text-sm text-tertiary mb-7">سجّل دخولك للمتابعة إلى مسارك في المجتمع</p>

<div class="flex bg-mist rounded-lg p-1 mb-6 text-sm font-bold">
    <a href="<?php echo e(route('login')); ?>" class="flex-1 text-center py-2.5 rounded-md bg-white text-primary">تسجيل الدخول</a>
    <a href="<?php echo e(route('register')); ?>" class="flex-1 text-center py-2.5 rounded-md text-tertiary hover:text-primary">حساب جديد</a>
</div>

<?php if($errors->any()): ?>
    <div class="mb-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-sm px-4 py-3"><?php echo e($errors->first()); ?></div>
<?php endif; ?>

<form action="<?php echo e(route('login')); ?>" method="POST" class="space-y-4">
    <?php echo csrf_field(); ?>
    <div>
        <label class="block text-sm font-bold text-primary mb-1.5">البريد الإلكتروني</label>
        <input type="email" name="email" value="<?php echo e(old('email')); ?>" placeholder="name@example.com" class="input" required autofocus>
    </div>
    <div>
        <div class="flex items-center justify-between mb-1.5">
            <label class="block text-sm font-bold text-primary">كلمة المرور</label>
            <a href="<?php echo e(route('password.request')); ?>" class="text-xs text-secondary font-semibold hover:underline">نسيت كلمة المرور؟</a>
        </div>
        <input type="password" name="password" placeholder="••••••••" class="input" required>
    </div>
    <label class="flex items-center gap-2 text-sm text-tertiary">
        <input type="checkbox" name="remember" class="rounded border-slate-300 accent-primary"> تذكرني على هذا الجهاز
    </label>
    <button type="submit" class="btn-primary w-full">دخول إلى المنصة</button>
</form>

<div class="my-6 flex items-center gap-3 text-xs text-tertiary">
    <div class="flex-1 h-px bg-slate-200"></div>
    أو
    <div class="flex-1 h-px bg-slate-200"></div>
</div>

<a href="<?php echo e(route('home')); ?>" class="block text-center text-sm font-semibold text-tertiary hover:text-primary transition">
    المتابعة كزائر — تصفح محدود بدون تفاعل
</a>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.auth', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\E\iti-open-source\Freelance\elitetech-laravel\resources\views/auth/login.blade.php ENDPATH**/ ?>