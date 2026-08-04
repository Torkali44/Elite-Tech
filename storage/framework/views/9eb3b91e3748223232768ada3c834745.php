<?php $__env->startSection('title', 'منتدى التوظيف والمواهب الموثقة — Elite Tech Community'); ?>
<?php $__env->startSection('description', 'تصفح مواهب المطورين والخبراء الموثقين بالنظام (KYC). تواصل مع الكفاءات التقنية واكتشف السير الذاتية.'); ?>

<?php $__env->startSection('content'); ?>
<section class="bg-primary text-white py-14 sm:py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl sm:text-4xl font-extrabold mb-3">منتدى التوظيف</h1>
        <p class="text-white/75 text-base max-w-2xl leading-relaxed">
            استعرض بطاقات المطورين والمهندسين الموثّقين (KYC). يمكنك تصفح الملفات بحرية.
        </p>
    </div>
</section>

<!-- Content Container -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-14">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-10 pb-6 border-b border-slate-200">
        <div>
            <h2 class="text-xl font-black text-primary">المواهب المتاحة للمشاريع</h2>
            <p class="text-xs sm:text-sm text-slate-500 font-semibold mt-1">يتم إظهار الأعضاء الموثقين فقط لحماية جودة البيئة التنافسية.</p>
        </div>
        
        <?php if(auth()->guard()->check()): ?>
            <a href="<?php echo e(route('profile.cv')); ?>" class="btn-secondary text-sm !py-2.5 shrink-0">
                ابنِ سيرتك / انضم للمنتدى
            </a>
        <?php else: ?>
            <button type="button"
                    class="btn-secondary text-sm !py-2.5 shrink-0"
                    @click="gateOpen=true; gateMsg='لبناء سيرتك الذاتية والانضمام لمنتدى التوظيف أنشئ حساباً أولاً.'">
                أظهر موهبتك
            </button>
        <?php endif; ?>
    </div>

    <?php if($talents->isEmpty()): ?>
        <div class="card p-12 text-center max-w-xl mx-auto shadow-card">
            <div class="w-16 h-16 rounded-2xl bg-blue-50 text-blue-500 grid place-items-center text-3xl font-bold mx-auto mb-4">👨‍💻</div>
            <h3 class="font-extrabold text-primary text-xl mb-2">لا توجد بطاقات مواهب حالياً</h3>
            <p class="text-sm text-slate-500 font-medium mb-6">سيظهر هنا المطورون والمهندسون فور اجتيازهم لتوثيق الهوية KYC.</p>
            <?php if(auth()->guard()->check()): ?>
                <a href="<?php echo e(route('profile.cv')); ?>" class="btn-primary text-sm">أضف ملفك الشخصي الآن</a>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php $__currentLoopData = $talents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="card card-hover p-6 flex flex-col justify-between group transition-all duration-300">
                    <div>
                        <!-- Header user identity -->
                        <div class="flex items-start justify-between gap-3 mb-4">
                            <div class="flex items-center gap-3">
                                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-primary to-slate-800 text-white grid place-items-center font-black text-xl shadow-md group-hover:scale-105 transition-transform">
                                    <?php echo e(mb_substr($t->name, 0, 1)); ?>

                                </div>
                                <div>
                                    <div class="font-extrabold text-primary text-base group-hover:text-secondary transition-colors"><?php echo e($t->name); ?></div>
                                    <div class="text-xs font-semibold text-slate-500"><?php echo e($t->title ?: 'مطور برمجيات'); ?></div>
                                </div>
                            </div>
                            <span class="badge bg-emerald-50 text-emerald-700 border border-emerald-200">✓ موثّق</span>
                        </div>

                        <?php
                            $vis = is_array($t->cv?->visibility) ? $t->cv->visibility : [];
                            $emp = ['full_time'=>'دوام كلي','part_time'=>'دوام جزئي','contract'=>'عقود'][$vis['employment_type'] ?? ''] ?? null;
                            $work = ['remote'=>'عن بعد','hybrid'=>'هجين','onsite'=>'مقر'][$vis['work_style'] ?? ''] ?? null;
                        ?>

                        <p class="text-sm text-slate-600 font-medium line-clamp-3 mb-3 leading-relaxed">
                            <?php echo e($t->bio ?: 'عضو متميز في مجتمع النخبة التقنية.'); ?>

                        </p>

                        <?php if($emp || $work || !empty($t->available_for_hire)): ?>
                            <div class="flex flex-wrap gap-1.5 mb-3 text-[11px]">
                                <?php if($t->available_for_hire): ?><span class="badge bg-secondary/15 text-secondary">متاح</span><?php endif; ?>
                                <?php if($emp): ?><span class="badge bg-primary/10 text-primary"><?php echo e($emp); ?></span><?php endif; ?>
                                <?php if($work): ?><span class="badge bg-mist text-tertiary"><?php echo e($work); ?></span><?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <?php if($t->cv?->data['skills'] ?? false): ?>
                            <div class="flex flex-wrap gap-1.5 mb-6">
                                <?php $__currentLoopData = array_slice($t->cv->data['skills'], 0, 4); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $skill): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <span class="badge bg-slate-100 text-slate-700 border border-slate-200"><?php echo e($skill); ?></span>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center gap-2.5 pt-4 border-t border-slate-100">
                        <a href="<?php echo e(route('profile.show', $t->id)); ?>" class="btn-outline text-sm flex-1 text-center !py-2">
                            عرض الملف
                        </a>
                        <?php if(auth()->guard()->check()): ?>
                            <a href="<?php echo e(route('network.index', ['with' => $t->id])); ?>" class="btn-primary text-sm flex-1 text-center !py-2">
                                تواصل
                            </a>
                        <?php else: ?>
                            <button type="button" 
                                    class="btn-primary text-sm flex-1 !py-2"
                                    @click="gateOpen=true; gateMsg='التواصل مع المواهب الموثقة يتطلب تسجيل الدخول.'">
                                تواصل
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <div class="mt-10 flex justify-center">
            <?php echo e($talents->links()); ?>

        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\E\iti-open-source\Freelance\elitetech-laravel\resources\views/jobs/index.blade.php ENDPATH**/ ?>