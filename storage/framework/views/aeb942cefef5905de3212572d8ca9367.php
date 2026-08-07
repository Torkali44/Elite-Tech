<?php $__env->startSection('title','مجتمع النخبة — المواهب'); ?>
<?php $__env->startSection('content'); ?>
<section class="bg-gradient-to-br from-primary to-primary-600 text-white py-14">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl sm:text-4xl font-black mb-2">أفكار المجتمع</h1>
            <p class="text-white/80">استكشف مواهب النخبة التقنية، تواصل، أعرض أفكارك</p>
        </div>
        <div class="flex gap-3">
            <div class="bg-white/10 rounded-xl p-3 text-center min-w-[100px]">
                <div class="text-xs opacity-80">إجمالي المشاركين</div>
                <div class="text-2xl font-black">456</div>
            </div>
            <div class="bg-white/10 rounded-xl p-3 text-center min-w-[100px]">
                <div class="text-xs opacity-80">النقاش الأسبوعي</div>
                <div class="text-2xl font-black">78</div>
            </div>
        </div>
    </div>
</section>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="grid lg:grid-cols-[260px_1fr] gap-6">
        
        <aside class="card p-5 h-fit sticky top-20">
            <h3 class="font-bold text-primary mb-4 flex items-center gap-2">▽ تصفية النتائج</h3>
            <div class="space-y-4 text-sm">
                <div>
                    <label class="block font-semibold text-primary mb-1.5">الفئة</label>
                    <select class="input"><option>الكل</option><option>مطور</option><option>مصمم</option><option>Data Scientist</option></select>
                </div>
                <div>
                    <label class="block font-semibold text-primary mb-1.5">ترتيب حسب</label>
                    <label class="flex items-center gap-2 mt-2"><input type="radio" name="sort" checked> الأحدث</label>
                    <label class="flex items-center gap-2 mt-1"><input type="radio" name="sort"> الأكثر تفاعلاً</label>
                </div>
                <button class="btn-secondary w-full">إعادة تعيين</button>
            </div>
        </aside>

        
        <div class="grid sm:grid-cols-2 gap-4">
            <?php $__currentLoopData = $members; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <a href="<?php echo e(route('community.show',$m['id'])); ?>" class="card p-5 hover:shadow-card-hover transition">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-12 h-12 rounded-full bg-primary/10 grid place-items-center text-primary font-bold"><?php echo e(mb_substr($m['name'],0,1)); ?></div>
                    <div class="flex-1 min-w-0">
                        <div class="font-bold text-primary truncate"><?php echo e($m['name']); ?></div>
                        <div class="text-xs text-tertiary"><?php echo e($m['title']); ?></div>
                    </div>
                    <span class="badge bg-emerald-50 text-emerald-600 text-[10px]">Elite</span>
                </div>
                <h4 class="font-bold text-primary text-sm mb-2"><?php echo e($m['project']); ?></h4>
                <p class="text-xs text-tertiary line-clamp-2 mb-3"><?php echo e($m['bio']); ?></p>
                <div class="flex flex-wrap gap-1.5 mb-3">
                    <?php $__currentLoopData = $m['skills']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <span class="badge bg-neutral text-primary text-[10px] border border-slate-200"><?php echo e($s); ?></span>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <div class="flex items-center justify-between text-xs text-tertiary border-t border-slate-100 pt-3">
                    <span>👍 <?php echo e($m['likes']); ?></span>
                    <span class="text-primary font-bold">عرض التفاصيل ←</span>
                </div>
            </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>

    
    <div class="flex items-center justify-center gap-2 mt-10 text-sm">
        <?php $__currentLoopData = [1,2,3,4,5]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <a href="?page=<?php echo e($p); ?>" class="w-9 h-9 grid place-items-center rounded-lg <?php echo e($p==1 ? 'bg-primary text-white' : 'bg-white border border-slate-200 text-tertiary hover:border-primary'); ?>"><?php echo e($p); ?></a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\E\iti-open-source\Freelance\elitetech-laravel\resources\views\community\index.blade.php ENDPATH**/ ?>