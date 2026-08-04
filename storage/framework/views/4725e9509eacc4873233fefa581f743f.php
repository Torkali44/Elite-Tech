<?php $__env->startSection('title','المستخدمون'); ?>
<?php $__env->startSection('content'); ?>
<div class="mb-6">
    <h1 class="text-2xl font-black text-primary mb-1">المستخدمون</h1>
    <p class="text-sm text-tertiary">فلترة حسب التوثيق والمسار — ملاحظات داخلية وتعليق الحساب</p>
</div>

<form class="card p-4 mb-6 grid sm:grid-cols-4 gap-3">
    <input name="q" value="<?php echo e(request('q')); ?>" class="input" placeholder="اسم / بريد / رقم">
    <select name="kyc" class="input">
        <option value="">كل حالات KYC</option>
        <?php $__currentLoopData = ['none','pending','approved','rejected','suspended']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($s); ?>" <?php if(request('kyc')===$s): echo 'selected'; endif; ?>><?php echo e($s); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
    <select name="role" class="input">
        <option value="">كل المسارات</option>
        <?php $__currentLoopData = ['idea_owner','idea_seeker','developer']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($r); ?>" <?php if(request('role')===$r): echo 'selected'; endif; ?>><?php echo e($r); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
    <button class="btn-primary">تطبيق الفلاتر</button>
</form>

<div class="card overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-mist text-tertiary text-xs">
                <tr>
                    <th class="text-right p-3">#</th>
                    <th class="text-right p-3">الاسم</th>
                    <th class="text-right p-3">المسار</th>
                    <th class="text-right p-3">KYC</th>
                    <th class="text-right p-3">منتدى</th>
                    <th class="text-right p-3">تاريخ</th>
                    <th class="p-3">إجراءات</th>
                </tr>
            </thead>
            <tbody>
            <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $roleMap = [
                        'idea_owner' => 'صاحب فكرة',
                        'idea_seeker' => 'باحث عن فكرة',
                        'developer' => 'باحث عن عمل',
                        'admin' => 'إدارة',
                    ];
                    $userRoles = is_array($u->roles) && count($u->roles) > 0 ? $u->roles : [$u->role];
                    $hasMultiple = count($userRoles) > 1;
                ?>
                <tr class="border-t border-mist hover:bg-neutral/60" x-data="{ n:false }">
                    <td class="p-3 text-tertiary"><?php echo e($u->id); ?></td>
                    <td class="p-3">
                        <div class="font-bold text-primary"><?php echo e($u->name); ?></div>
                        <div class="text-xs text-tertiary"><?php echo e($u->email); ?></div>
                    </td>
                    <td class="p-3">
                        <?php if($hasMultiple): ?>
                            <div class="mb-1">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-extrabold bg-purple-100 text-purple-800 border border-purple-200">
                                    ⚡ أكثر من مسار (<?php echo e(count($userRoles)); ?>)
                                </span>
                            </div>
                            <div class="text-xs text-tertiary flex flex-wrap gap-1">
                                <?php $__currentLoopData = $userRoles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rKey): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <span class="inline-block bg-neutral px-1.5 py-0.5 rounded border border-mist text-[11px] font-medium text-primary">
                                        <?php echo e($roleMap[$rKey] ?? $rKey); ?>

                                    </span>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        <?php else: ?>
                            <span class="font-medium text-primary"><?php echo e($u->roleLabel()); ?></span>
                        <?php endif; ?>
                    </td>
                    <td class="p-3">
                        <?php if($u->is_suspended): ?>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-100 text-rose-800 border border-rose-300">
                                ⛔ معلّق
                            </span>
                        <?php else: ?>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">
                                ✅ نشط
                            </span>
                        <?php endif; ?>
                        <div class="text-[10px] text-tertiary mt-1">KYC: <?php echo e($u->kyc_status); ?></div>
                    </td>
                    <td class="p-3"><?php echo e($u->show_in_jobs_forum ? 'نعم' : 'لا'); ?></td>
                    <td class="p-3 text-xs text-tertiary"><?php echo e($u->created_at->format('Y-m-d')); ?></td>
                    <td class="p-3">
                        <div class="flex gap-2 justify-end items-center">
                            <button type="button" @click="n=!n" class="text-xs font-bold text-primary hover:underline">ملاحظات</button>
                            <?php if($u->role !== 'admin'): ?>
                                <?php if($u->is_suspended): ?>
                                    <form method="POST" action="<?php echo e(route('admin.users.activate', $u->id)); ?>" onsubmit="return confirm('إعادة تفعيل الحساب؟')"><?php echo csrf_field(); ?>
                                        <button class="px-2.5 py-1 rounded text-xs font-bold bg-emerald-600 text-white hover:bg-emerald-700 transition">تفعيل</button>
                                    </form>
                                <?php else: ?>
                                    <form method="POST" action="<?php echo e(route('admin.users.suspend', $u->id)); ?>" onsubmit="return confirm('تعليق الحساب؟')"><?php echo csrf_field(); ?>
                                        <button class="px-2.5 py-1 rounded text-xs font-bold bg-rose-600 text-white hover:bg-rose-700 transition">تعليق</button>
                                    </form>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="text-xs font-bold text-tertiary">حساب الإدارة</span>
                            <?php endif; ?>
                        </div>
                        <form x-show="n" x-cloak method="POST" action="<?php echo e(route('admin.users.notes', $u->id)); ?>" class="mt-2"><?php echo csrf_field(); ?>
                            <textarea name="admin_notes" rows="2" class="input text-xs" placeholder="ملاحظات داخلية..."><?php echo e($u->admin_notes); ?></textarea>
                            <button class="btn-ghost text-xs mt-1">حفظ</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>
</div>
<div class="mt-6"><?php echo e($users->links()); ?></div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\E\iti-open-source\Freelance\elitetech-laravel\resources\views/admin/users.blade.php ENDPATH**/ ?>