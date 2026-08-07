<?php $__env->startSection('title', $title); ?>
<?php $__env->startSection('content'); ?>
<h1 class="text-2xl lg:text-3xl font-black text-primary mb-6"><?php echo e($title); ?></h1>
<div class="card p-6">
    <div class="flex items-center justify-between mb-4 flex-wrap gap-3">
        <div class="relative flex-1 max-w-sm"><input class="input pr-9" placeholder="بحث..."><svg class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="2" d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z"/></svg></div>
        <select class="input max-w-[160px]"><option>الكل</option><option>نشط</option><option>معلق</option></select>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-neutral text-tertiary text-xs">
                <tr><?php $__currentLoopData = $cols; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><th class="text-right p-3 font-semibold"><?php echo e($c); ?></th><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><th class="p-3">إجراءات</th></tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr class="border-t border-slate-100 hover:bg-neutral/50">
                    <?php $__currentLoopData = $r; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cell): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><td class="p-3"><?php echo e($cell); ?></td><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <td class="p-3"><button class="text-primary text-xs font-bold">إجراء</button></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\E\iti-open-source\Freelance\elitetech-laravel\resources\views\admin\table.blade.php ENDPATH**/ ?>