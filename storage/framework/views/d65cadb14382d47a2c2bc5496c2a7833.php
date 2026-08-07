<?php $__env->startSection('title', __('settings.title')); ?>

<?php $__env->startSection('content'); ?>
<?php
    $vis = $visibility ?? [];
    $empType = old('employment_type', $vis['employment_type'] ?? 'full_time');
    $workStyle = old('work_style', $vis['work_style'] ?? 'remote');
?>

<div class="mb-6">
    <h1 class="text-2xl sm:text-3xl font-extrabold text-primary"><?php echo e(__('settings.title')); ?></h1>
    <p class="text-sm text-tertiary mt-1 leading-relaxed max-w-3xl">
        <?php echo e(__('settings.subtitle')); ?>

        <a href="<?php echo e(route('profile.cv')); ?>" class="text-secondary font-bold underline"><?php echo e(__('profile.cv_builder_title')); ?></a>.
    </p>
</div>

<div class="grid lg:grid-cols-12 gap-6 items-start">
    <div class="lg:col-span-8 space-y-6">
        <form action="<?php echo e(route('settings')); ?>" method="POST" enctype="multipart/form-data" class="space-y-6"
              x-data="{ empType: '<?php echo e($empType); ?>', workStyle: '<?php echo e($workStyle); ?>' }">
            <?php echo csrf_field(); ?>

            <?php if($errors->any()): ?>
                <div class="rounded-lg bg-rose-50 border border-rose-200 text-rose-700 text-sm p-4 space-y-1">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><div>• <?php echo e($e); ?></div><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php endif; ?>

            <div class="card p-6 space-y-5">
                <div class="flex items-center justify-between pb-4 border-b border-mist">
                    <div>
                        <h2 class="text-lg font-extrabold text-primary"><?php echo e(__('settings.availability_status')); ?></h2>
                        <p class="text-xs text-tertiary mt-1"><?php echo e(__('settings.availability_desc')); ?></p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="available_for_hire" value="1" class="sr-only peer"
                               <?php if(old('available_for_hire', auth()->user()->available_for_hire)): echo 'checked'; endif; ?>>
                        <div class="w-11 h-6 bg-mist peer-checked:bg-secondary rounded-full after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-5 rtl:peer-checked:after:-translate-x-5"></div>
                    </label>
                </div>

                <input type="hidden" name="employment_type" :value="empType">
                <input type="hidden" name="work_style" :value="workStyle">

                <div>
                    <label class="block text-xs font-bold text-tertiary mb-2"><?php echo e(__('settings.employment_type')); ?></label>
                    <div class="grid grid-cols-3 gap-2">
                        <?php $__currentLoopData = ['full_time'=> __('settings.types.full_time'), 'part_time'=> __('settings.types.part_time'), 'contract'=> __('settings.types.contract')]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <button type="button" @click="empType='<?php echo e($val); ?>'"
                                    :class="empType==='<?php echo e($val); ?>' ? 'bg-primary text-white' : 'bg-neutral text-tertiary'"
                                    class="py-2.5 rounded-md text-sm font-bold"><?php echo e($label); ?></button>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-tertiary mb-2"><?php echo e(__('settings.work_style')); ?></label>
                    <div class="grid grid-cols-3 gap-2">
                        <?php $__currentLoopData = ['remote'=> __('settings.styles.remote'), 'hybrid'=> __('settings.styles.hybrid'), 'onsite'=> __('settings.styles.onsite')]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <button type="button" @click="workStyle='<?php echo e($val); ?>'"
                                    :class="workStyle==='<?php echo e($val); ?>' ? 'bg-secondary text-white' : 'bg-neutral text-tertiary'"
                                    class="py-2.5 rounded-md text-sm font-bold"><?php echo e($label); ?></button>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>

                <div class="grid sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-tertiary mb-1.5"><?php echo e(__('settings.target_salary')); ?></label>
                        <input type="text" name="target_salary" class="input"
                               value="<?php echo e(old('target_salary', $vis['target_salary'] ?? ($cvData['expected_salary'] ?? ''))); ?>"
                               placeholder="<?php echo e(__('settings.target_salary_placeholder')); ?>">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-tertiary mb-1.5"><?php echo e(__('settings.location')); ?></label>
                        <input type="text" name="location" class="input"
                               value="<?php echo e(old('location', auth()->user()->location)); ?>"
                               placeholder="<?php echo e(__('settings.location_placeholder')); ?>">
                    </div>
                </div>

                <?php if(!auth()->user()->isKycApproved() && (!auth()->user()->hasRole('idea_seeker') || auth()->user()->hasRole('idea_owner'))): ?>
                    <div class="rounded-lg bg-amber-50 border border-amber-200 p-3 text-sm text-amber-900">
                        <?php echo e(__('settings.kyc_notice')); ?>

                        <a href="<?php echo e(route('verification.kyc', ['purpose'=>'jobs_forum'])); ?>" class="font-bold text-secondary underline"><?php echo e(__('settings.complete_verification')); ?></a>
                    </div>
                <?php endif; ?>
            </div>

            <div class="card p-6 space-y-4">
                <h2 class="text-lg font-extrabold text-primary pb-3 border-b border-mist"><?php echo e(__('settings.privacy_settings')); ?></h2>
                <div class="flex items-center justify-between p-3 rounded-lg bg-neutral">
                    <div>
                        <div class="font-bold text-primary text-sm"><?php echo e(__('settings.show_email')); ?></div>
                        <div class="text-xs text-tertiary"><?php echo e(__('settings.show_email_desc')); ?></div>
                    </div>
                    <input type="checkbox" name="show_email" value="1" class="w-4 h-4 accent-primary"
                           <?php if(old('show_email', $vis['show_email'] ?? false)): echo 'checked'; endif; ?>>
                </div>
                <div class="flex items-center justify-between p-3 rounded-lg bg-neutral">
                    <div>
                        <div class="font-bold text-primary text-sm"><?php echo e(app()->getLocale()==='ar' ? 'إظهار رقم الجوال للعامة' : 'Show phone number publicly'); ?></div>
                        <div class="text-xs text-tertiary"><?php echo e(app()->getLocale()==='ar' ? 'مخفي افتراضياً لحماية خصوصيتك' : 'Hidden by default to protect your privacy'); ?></div>
                    </div>
                    <input type="checkbox" name="show_phone" value="1" class="w-4 h-4 accent-primary"
                           <?php if(old('show_phone', $vis['show_phone'] ?? false)): echo 'checked'; endif; ?>>
                </div>
            </div>

            <div class="card p-6 space-y-4">
                <h2 class="text-lg font-extrabold text-primary pb-3 border-b border-mist"><?php echo e(app()->getLocale()==='ar' ? 'بيانات الحساب' : 'Account Info'); ?></h2>
                <div class="grid sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-tertiary mb-1.5"><?php echo e(__('auth.name')); ?></label>
                        <input class="input" name="name" value="<?php echo e(old('name', auth()->user()->name)); ?>" required>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-tertiary mb-1.5"><?php echo e(__('auth.email')); ?></label>
                        <input type="email" class="input" name="email" value="<?php echo e(old('email', auth()->user()->email)); ?>" required>
                    </div>
                </div>
                <div class="grid sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-tertiary mb-1.5"><?php echo e(__('profile.job_title')); ?> <span class="text-secondary"><?php echo e(app()->getLocale()==='ar' ? '(حسّاس)' : '(sensitive)'); ?></span></label>
                        <input class="input" name="title" value="<?php echo e(old('title', auth()->user()->title)); ?>">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-tertiary mb-1.5">Portfolio <span class="text-secondary"><?php echo e(app()->getLocale()==='ar' ? '(حسّاس)' : '(sensitive)'); ?></span></label>
                        <input type="url" class="input" name="portfolio_url" value="<?php echo e(old('portfolio_url', auth()->user()->portfolio_url)); ?>">
                    </div>
                </div>
                <div class="grid sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-tertiary mb-1.5"><?php echo e(app()->getLocale()==='ar' ? 'الصورة الرمزية' : 'Avatar'); ?></label>
                        <input type="file" name="avatar" accept="image/*" class="input">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-tertiary mb-1.5"><?php echo e(__('profile.bio')); ?></label>
                        <textarea class="input" name="bio" rows="2"><?php echo e(old('bio', auth()->user()->bio)); ?></textarea>
                    </div>
                </div>
                <div class="grid sm:grid-cols-3 gap-3 pt-2 border-t border-mist">
                    <input type="password" name="current_password" class="input" placeholder="<?php echo e(app()->getLocale()==='ar' ? 'كلمة المرور الحالية' : 'Current password'); ?>" autocomplete="current-password">
                    <input type="password" name="password" class="input" placeholder="<?php echo e(app()->getLocale()==='ar' ? 'كلمة مرور جديدة' : 'New password'); ?>" autocomplete="new-password">
                    <input type="password" name="password_confirmation" class="input" placeholder="<?php echo e(app()->getLocale()==='ar' ? 'تأكيد كلمة المرور' : 'Confirm password'); ?>" autocomplete="new-password">
                </div>
                <p class="text-xs text-tertiary -mt-2"><?php echo e(app()->getLocale()==='ar' ? 'لتغيير كلمة المرور يلزم إدخال كلمة المرور الحالية.' : 'To change the password, current password is required.'); ?></p>
                <button type="submit" class="btn-primary"><?php echo e(__('settings.save_settings')); ?></button>
            </div>
        </form>
    </div>

    <aside class="lg:col-span-4 space-y-4">
        <div class="card p-5 space-y-3">
            <h2 class="text-lg font-extrabold text-primary"><?php echo e(app()->getLocale()==='ar' ? 'إعدادات الحساب' : 'Account Settings'); ?></h2>
            <a href="<?php echo e(route('profile.cv')); ?>" class="btn-secondary w-full text-center text-sm"><?php echo e(__('profile.cv_builder_title')); ?></a>
            <a href="<?php echo e(route('jobs')); ?>" class="btn-outline w-full text-center text-sm"><?php echo e(__('navigation.jobs_forum')); ?></a>
        </div>
        <div class="card p-5 text-sm text-tertiary leading-relaxed">
            <strong class="text-primary block mb-2"><?php echo e(app()->getLocale()==='ar' ? 'ضوابط KYC' : 'KYC Rules'); ?></strong>
            <?php echo e(app()->getLocale()==='ar' ? 'تعديل المسمى أو Portfolio يسحب شارة التوثيق مؤقتاً ويعيد الحساب للمراجعة.' : 'Editing your title or Portfolio temporarily removes the verification badge and returns the account to review.'); ?>

        </div>
    </aside>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\E\iti-open-source\Freelance\elitetech-laravel\resources\views\settings\index.blade.php ENDPATH**/ ?>