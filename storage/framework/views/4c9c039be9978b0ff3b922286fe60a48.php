<?php $__env->startSection('title', 'إعدادات النشر والظهور'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $vis = $visibility ?? [];
    $empType = old('employment_type', $vis['employment_type'] ?? 'full_time');
    $workStyle = old('work_style', $vis['work_style'] ?? 'remote');
?>

<div class="mb-6">
    <h1 class="text-2xl sm:text-3xl font-extrabold text-primary">إعدادات النشر والظهور</h1>
    <p class="text-sm text-tertiary mt-1 leading-relaxed max-w-3xl">
        هذه الصفحة تتحكم في <strong>كيف تظهر</strong> في منتدى التوظيف (التوفر، الخصوصية، نوع العمل).
        أما <strong>محتوى السيرة</strong> (مهارات، خبرات، مشاريع) فيُعدَّل من
        <a href="<?php echo e(route('profile.cv')); ?>" class="text-secondary font-bold underline">بناء الـ CV</a>.
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
                        <h2 class="text-lg font-extrabold text-primary">حالة التوفر للعمل</h2>
                        <p class="text-xs text-tertiary mt-1">يظهر لأصحاب العمل في منتدى التوظيف بعد اجتياز KYC.</p>
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
                    <label class="block text-xs font-bold text-tertiary mb-2">نوع التوظيف</label>
                    <div class="grid grid-cols-3 gap-2">
                        <?php $__currentLoopData = ['full_time'=>'دوام كلي','part_time'=>'دوام جزئي','contract'=>'عقود']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <button type="button" @click="empType='<?php echo e($val); ?>'"
                                    :class="empType==='<?php echo e($val); ?>' ? 'bg-primary text-white' : 'bg-neutral text-tertiary'"
                                    class="py-2.5 rounded-md text-sm font-bold"><?php echo e($label); ?></button>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-tertiary mb-2">نمط العمل</label>
                    <div class="grid grid-cols-3 gap-2">
                        <?php $__currentLoopData = ['remote'=>'عن بعد','hybrid'=>'هجين','onsite'=>'مقر الشركة']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <button type="button" @click="workStyle='<?php echo e($val); ?>'"
                                    :class="workStyle==='<?php echo e($val); ?>' ? 'bg-secondary text-white' : 'bg-neutral text-tertiary'"
                                    class="py-2.5 rounded-md text-sm font-bold"><?php echo e($label); ?></button>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>

                <div class="grid sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-tertiary mb-1.5">الراتب المستهدف</label>
                        <input type="text" name="target_salary" class="input"
                               value="<?php echo e(old('target_salary', $vis['target_salary'] ?? ($cvData['expected_salary'] ?? ''))); ?>"
                               placeholder="مثال: 15000">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-tertiary mb-1.5">المنطقة الجغرافية</label>
                        <input type="text" name="location" class="input"
                               value="<?php echo e(old('location', auth()->user()->location)); ?>"
                               placeholder="مثال: القاهرة">
                    </div>
                </div>

                <?php if(!auth()->user()->isKycApproved()): ?>
                    <div class="rounded-lg bg-amber-50 border border-amber-200 p-3 text-sm text-amber-900">
                        الظهور في منتدى التوظيف يتطلب KYC.
                        <a href="<?php echo e(route('verification.kyc', ['purpose'=>'jobs_forum'])); ?>" class="font-bold text-secondary underline">أكمل التوثيق</a>
                    </div>
                <?php endif; ?>
            </div>

            <div class="card p-6 space-y-4">
                <h2 class="text-lg font-extrabold text-primary pb-3 border-b border-mist">خصوصية بيانات التواصل</h2>
                <div class="flex items-center justify-between p-3 rounded-lg bg-neutral">
                    <div>
                        <div class="font-bold text-primary text-sm">إظهار البريد للعامة</div>
                        <div class="text-xs text-tertiary">في ملفك العام ومنتدى التوظيف</div>
                    </div>
                    <input type="checkbox" name="show_email" value="1" class="w-4 h-4 accent-primary"
                           <?php if(old('show_email', $vis['show_email'] ?? false)): echo 'checked'; endif; ?>>
                </div>
                <div class="flex items-center justify-between p-3 rounded-lg bg-neutral">
                    <div>
                        <div class="font-bold text-primary text-sm">إظهار رقم الجوال للعامة</div>
                        <div class="text-xs text-tertiary">مخفي افتراضياً لحماية خصوصيتك</div>
                    </div>
                    <input type="checkbox" name="show_phone" value="1" class="w-4 h-4 accent-primary"
                           <?php if(old('show_phone', $vis['show_phone'] ?? false)): echo 'checked'; endif; ?>>
                </div>
            </div>

            <div class="card p-6 space-y-4">
                <h2 class="text-lg font-extrabold text-primary pb-3 border-b border-mist">بيانات الحساب</h2>
                <div class="grid sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-tertiary mb-1.5">الاسم</label>
                        <input class="input" name="name" value="<?php echo e(old('name', auth()->user()->name)); ?>" required>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-tertiary mb-1.5">البريد</label>
                        <input type="email" class="input" name="email" value="<?php echo e(old('email', auth()->user()->email)); ?>" required>
                    </div>
                </div>
                <div class="grid sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-tertiary mb-1.5">المسمى الوظيفي <span class="text-secondary">(حسّاس)</span></label>
                        <input class="input" name="title" value="<?php echo e(old('title', auth()->user()->title)); ?>">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-tertiary mb-1.5">Portfolio <span class="text-secondary">(حسّاس)</span></label>
                        <input type="url" class="input" name="portfolio_url" value="<?php echo e(old('portfolio_url', auth()->user()->portfolio_url)); ?>">
                    </div>
                </div>
                <div class="grid sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-tertiary mb-1.5">الصورة الرمزية</label>
                        <input type="file" name="avatar" accept="image/*" class="input">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-tertiary mb-1.5">نبذة قصيرة</label>
                        <textarea class="input" name="bio" rows="2"><?php echo e(old('bio', auth()->user()->bio)); ?></textarea>
                    </div>
                </div>
                <div class="grid sm:grid-cols-3 gap-3 pt-2 border-t border-mist">
                    <input type="password" name="current_password" class="input" placeholder="كلمة المرور الحالية" autocomplete="current-password">
                    <input type="password" name="password" class="input" placeholder="كلمة مرور جديدة" autocomplete="new-password">
                    <input type="password" name="password_confirmation" class="input" placeholder="تأكيد كلمة المرور" autocomplete="new-password">
                </div>
                <p class="text-xs text-tertiary -mt-2">لتغيير كلمة المرور يلزم إدخال كلمة المرور الحالية.</p>
                <button type="submit" class="btn-primary">حفظ التغييرات</button>
            </div>
        </form>
    </div>

    <aside class="lg:col-span-4 space-y-4">
        <div class="card p-5 space-y-3">
            <h2 class="text-lg font-extrabold text-primary">إعدادات الحساب</h2>
            <a href="<?php echo e(route('profile.cv')); ?>" class="btn-secondary w-full text-center text-sm">تعديل السيرة الذاتية</a>
            <a href="<?php echo e(route('jobs')); ?>" class="btn-outline w-full text-center text-sm">معاينة منتدى التوظيف</a>
        </div>
        <div class="card p-5 text-sm text-tertiary leading-relaxed">
            <strong class="text-primary block mb-2">ضوابط KYC</strong>
            تعديل المسمى أو Portfolio يسحب شارة التوثيق مؤقتاً ويعيد الحساب للمراجعة.
        </div>
    </aside>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\E\iti-open-source\Freelance\elitetech-laravel\resources\views/settings/index.blade.php ENDPATH**/ ?>