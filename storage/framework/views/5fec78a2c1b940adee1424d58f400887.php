<?php $__env->startSection('title','بناء السيرة الذاتية'); ?>
<?php $__env->startSection('content'); ?>
<?php
    $g = fn ($k, $default = '') => old($k, is_array($data[$k] ?? null) ? implode(', ', $data[$k]) : ($data[$k] ?? $default));
    $skills = \App\Http\Controllers\ProfileController::asSkills($data['skills'] ?? []);
    $languages = \App\Http\Controllers\ProfileController::asSkills($data['languages'] ?? []);
    $certs = \App\Http\Controllers\ProfileController::asSkills($data['certifications'] ?? []);
?>

<?php $__env->startPush('head'); ?>
<style>
  @media print {
    @page { margin: 1.2cm; size: A4; }
    body * { visibility: hidden !important; }
    #cv-preview, #cv-preview * { visibility: visible !important; }
    #cv-preview {
      position: absolute !important;
      inset: 0 !important;
      width: 100% !important;
      max-width: 100% !important;
      margin: 0 !important;
      padding: 0 !important;
      border: none !important;
      box-shadow: none !important;
      background: #fff !important;
    }
  }
</style>
<?php $__env->stopPush(); ?>

<div class="mb-6 flex flex-wrap items-end justify-between gap-3 no-print">
    <div>
        <h1 class="text-2xl font-extrabold text-primary mb-1">بناء السيرة الذاتية</h1>
        <p class="text-sm text-tertiary max-w-2xl leading-relaxed">
            ابنِ ملفاً مهنياً واستخرجه PDF بحرية بدون KYC.
            للظهور في منتدى التوظيف أكمل KYC من الإعدادات / التوثيق.
        </p>
    </div>
    <div class="flex gap-2">
        <a href="<?php echo e(route('settings')); ?>" class="btn-ghost text-sm">إعدادات الظهور</a>
        <button type="button" onclick="window.print()" class="btn-secondary text-sm">استخراج PDF</button>
    </div>
</div>

<div class="grid xl:grid-cols-2 gap-6">
    <form method="POST" action="<?php echo e(route('profile.cv')); ?>" class="card p-6 space-y-5 no-print">
        <?php echo csrf_field(); ?>
        <?php if($errors->any()): ?>
            <div class="rounded-lg bg-rose-50 border border-rose-200 text-rose-700 text-sm px-4 py-3 space-y-1">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><div><?php echo e($e); ?></div><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php endif; ?>

        <section class="space-y-3">
            <h3 class="font-extrabold text-primary border-b border-mist pb-2">المعلومات الأساسية</h3>
            <div class="grid sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-bold text-primary mb-1">المسمى الوظيفي</label>
                    <input name="title" class="input" value="<?php echo e($g('title')); ?>" placeholder="Full-stack Developer">
                </div>
                <div>
                    <label class="block text-sm font-bold text-primary mb-1">سنوات الخبرة</label>
                    <input name="years_experience" class="input" value="<?php echo e($g('years_experience')); ?>" placeholder="مثلاً: 3 سنوات">
                </div>
            </div>
            <div>
                <label class="block text-sm font-bold text-primary mb-1">نبذة قصيرة</label>
                <textarea name="summary" rows="3" class="input" placeholder="من أنت وما الذي تبحث عنه؟"><?php echo e($g('summary')); ?></textarea>
            </div>
            <div class="grid sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-bold text-primary mb-1">الموقع</label>
                    <input name="location" class="input" value="<?php echo e($g('location', auth()->user()->location)); ?>" placeholder="القاهرة، مصر">
                </div>
                <div>
                    <label class="block text-sm font-bold text-primary mb-1">رقم التواصل</label>
                    <input name="phone" class="input" value="<?php echo e($g('phone')); ?>" placeholder="+20..." dir="ltr">
                </div>
            </div>
            <div class="grid sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-bold text-primary mb-1">التوفر</label>
                    <select name="availability" class="input">
                        <?php $__currentLoopData = ['','متاح فوراً','خلال أسبوعين','دوام جزئي','عن بُعد فقط','غير متاح حالياً']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $opt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($opt); ?>" <?php if($g('availability')===$opt): echo 'selected'; endif; ?>><?php echo e($opt ?: 'اختر...'); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-bold text-primary mb-1">الراتب المتوقع</label>
                    <input name="expected_salary" class="input" value="<?php echo e($g('expected_salary')); ?>" placeholder="اختياري">
                </div>
            </div>
        </section>

        <section class="space-y-3">
            <h3 class="font-extrabold text-primary border-b border-mist pb-2">المهارات واللغات</h3>
            <div>
                <label class="block text-sm font-bold text-primary mb-1">المهارات التقنية</label>
                <input name="skills" class="input" value="<?php echo e($g('skills', implode(', ', $skills))); ?>" placeholder="Laravel, React, SQL">
            </div>
            <div>
                <label class="block text-sm font-bold text-primary mb-1">اللغات</label>
                <input name="languages" class="input" value="<?php echo e($g('languages', implode(', ', $languages))); ?>" placeholder="العربية, الإنجليزية">
            </div>
            <div>
                <label class="block text-sm font-bold text-primary mb-1">الشهادات</label>
                <input name="certifications" class="input" value="<?php echo e($g('certifications', implode(', ', $certs))); ?>" placeholder="AWS, PMP...">
            </div>
        </section>

        <section class="space-y-3">
            <h3 class="font-extrabold text-primary border-b border-mist pb-2">الروابط</h3>
            <div>
                <label class="block text-sm font-bold text-primary mb-1">Portfolio</label>
                <input type="url" name="portfolio_url" class="input" value="<?php echo e($g('portfolio_url')); ?>" placeholder="https://" dir="ltr">
            </div>
            <div class="grid sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-bold text-primary mb-1">LinkedIn</label>
                    <input type="url" name="linkedin" class="input" value="<?php echo e($g('linkedin')); ?>" placeholder="https://linkedin.com/in/..." dir="ltr">
                </div>
                <div>
                    <label class="block text-sm font-bold text-primary mb-1">GitHub</label>
                    <input type="url" name="github" class="input" value="<?php echo e($g('github')); ?>" placeholder="https://github.com/..." dir="ltr">
                </div>
            </div>
        </section>

        <section class="space-y-3">
            <h3 class="font-extrabold text-primary border-b border-mist pb-2">الخبرة والتعليم والمشاريع</h3>
            <div>
                <label class="block text-sm font-bold text-primary mb-1">الخبرات العملية</label>
                <textarea name="experience" rows="4" class="input" placeholder="المسمى — الشركة — المدة — إنجازات..."><?php echo e($g('experience')); ?></textarea>
            </div>
            <div>
                <label class="block text-sm font-bold text-primary mb-1">التعليم</label>
                <textarea name="education" rows="2" class="input" placeholder="الدرجة — الجامعة — السنة"><?php echo e($g('education')); ?></textarea>
            </div>
            <div>
                <label class="block text-sm font-bold text-primary mb-1">أبرز المشاريع</label>
                <textarea name="projects" rows="3" class="input" placeholder="اسم المشروع — دورك — التقنيات — النتيجة"><?php echo e($g('projects')); ?></textarea>
            </div>
        </section>

        <label class="flex items-start gap-2 p-3 rounded-lg bg-neutral text-sm">
            <input type="checkbox" name="join_forum" value="1" class="mt-1 accent-secondary" <?php if(auth()->user()->wants_jobs_forum): echo 'checked'; endif; ?>>
            <span>
                <span class="font-bold text-primary">الظهور في منتدى التوظيف</span>
                <span class="block text-xs text-tertiary mt-0.5">يتطلب اجتياز KYC وموافقة الإدارة. تحكم إضافي من صفحة الإعدادات.</span>
            </span>
        </label>

        <button class="btn-primary w-full">حفظ السيرة</button>
    </form>

    
    <div class="card p-8 bg-white sticky top-20 self-start print:static print:shadow-none print:border-0" id="cv-preview">
        <div class="border-b-2 border-primary pb-5 mb-6">
            <div class="text-2xl font-extrabold text-primary"><?php echo e(auth()->user()->name); ?></div>
            <div class="text-secondary font-bold text-lg mt-0.5"><?php echo e($g('title') ?: 'المسمى الوظيفي'); ?></div>
            <div class="text-xs text-tertiary mt-3 space-y-1">
                <div><?php echo e(auth()->user()->email); ?><?php if($g('phone')): ?> · <?php echo e($g('phone')); ?><?php endif; ?></div>
                <?php if($g('location')): ?><div><?php echo e($g('location')); ?></div><?php endif; ?>
                <?php if($g('years_experience') || $g('availability')): ?>
                    <div><?php echo e($g('years_experience')); ?><?php if($g('years_experience') && $g('availability')): ?> · <?php endif; ?><?php echo e($g('availability')); ?></div>
                <?php endif; ?>
                <?php if($g('expected_salary')): ?><div>الراتب المتوقع: <?php echo e($g('expected_salary')); ?></div><?php endif; ?>
            </div>
            <div class="flex flex-wrap gap-3 mt-3 text-xs">
                <?php if($g('portfolio_url')): ?><a href="<?php echo e($g('portfolio_url')); ?>" class="text-primary underline" target="_blank">Portfolio</a><?php endif; ?>
                <?php if($g('linkedin')): ?><a href="<?php echo e($g('linkedin')); ?>" class="text-primary underline" target="_blank">LinkedIn</a><?php endif; ?>
                <?php if($g('github')): ?><a href="<?php echo e($g('github')); ?>" class="text-primary underline" target="_blank">GitHub</a><?php endif; ?>
            </div>
        </div>

        <?php if($g('summary')): ?>
            <h4 class="font-extrabold text-primary text-sm mb-1">نبذة</h4>
            <p class="text-sm text-tertiary mb-5 whitespace-pre-line leading-relaxed"><?php echo e($g('summary')); ?></p>
        <?php endif; ?>

        <?php if(count($skills)): ?>
            <h4 class="font-extrabold text-primary text-sm mb-2">المهارات</h4>
            <div class="flex flex-wrap gap-1.5 mb-5">
                <?php $__currentLoopData = $skills; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><span class="badge bg-mist text-primary"><?php echo e($s); ?></span><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php endif; ?>

        <?php if(count($languages)): ?>
            <h4 class="font-extrabold text-primary text-sm mb-2">اللغات</h4>
            <div class="flex flex-wrap gap-1.5 mb-5">
                <?php $__currentLoopData = $languages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><span class="badge bg-primary/10 text-primary"><?php echo e($s); ?></span><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php endif; ?>

        <?php if($g('experience')): ?>
            <h4 class="font-extrabold text-primary text-sm mb-1">الخبرات</h4>
            <p class="text-sm text-tertiary mb-5 whitespace-pre-line leading-relaxed"><?php echo e($g('experience')); ?></p>
        <?php endif; ?>

        <?php if($g('projects')): ?>
            <h4 class="font-extrabold text-primary text-sm mb-1">المشاريع</h4>
            <p class="text-sm text-tertiary mb-5 whitespace-pre-line leading-relaxed"><?php echo e($g('projects')); ?></p>
        <?php endif; ?>

        <?php if($g('education')): ?>
            <h4 class="font-extrabold text-primary text-sm mb-1">التعليم</h4>
            <p class="text-sm text-tertiary mb-5 whitespace-pre-line leading-relaxed"><?php echo e($g('education')); ?></p>
        <?php endif; ?>

        <?php if(count($certs)): ?>
            <h4 class="font-extrabold text-primary text-sm mb-2">الشهادات</h4>
            <div class="flex flex-wrap gap-1.5">
                <?php $__currentLoopData = $certs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><span class="badge bg-secondary/10 text-secondary"><?php echo e($s); ?></span><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\E\iti-open-source\Freelance\elitetech-laravel\resources\views/profile/cv-builder.blade.php ENDPATH**/ ?>