
<div x-show="gateOpen" x-cloak class="fixed inset-0 z-[60] flex items-center justify-center p-4"
     @keydown.escape.window="gateOpen=false">
    <div class="absolute inset-0 gate-backdrop" @click="gateOpen=false"></div>
    <div class="relative card max-w-md w-full p-7 animate-fade-up shadow-card-hover" @click.stop>
        <button type="button" @click="gateOpen=false" class="absolute <?php echo e(app()->getLocale()==='ar' ? 'left-4' : 'right-4'); ?> top-4 text-tertiary hover:text-primary text-xl leading-none">×</button>
        <div class="w-12 h-12 rounded-xl bg-secondary/15 text-secondary grid place-items-center text-xl mb-4 font-black">!</div>
        <h3 class="text-xl font-black text-primary mb-2"><?php echo e(app()->getLocale()==='ar' ? 'سجّل للمتابعة' : 'Sign Up to Continue'); ?></h3>
        <p class="text-sm text-tertiary leading-relaxed mb-6" x-text="gateMsg || '<?php echo e(app()->getLocale()==='ar' ? 'التصفح متاح للجميع. التفاعل العميق (تعليق، تواصل، رغبة في التنفيذ، نشر فكرة) يتطلب حساباً.' : 'Browsing is available for everyone. Deep interaction (comment, connect, implement request, publish idea) requires an account.'); ?>'"></p>
        <div class="flex flex-col sm:flex-row gap-2">
            <a href="<?php echo e(route('register')); ?>" class="btn-secondary flex-1 text-center"><?php echo e(__('navigation.register')); ?></a>
            <a href="<?php echo e(route('login')); ?>" class="btn-outline flex-1 text-center"><?php echo e(__('navigation.login')); ?></a>
        </div>
        <button type="button" @click="gateOpen=false" class="w-full mt-3 text-sm text-tertiary hover:text-primary"><?php echo e(__('navigation.browse_as_guest')); ?></button>
    </div>
</div>
<?php /**PATH C:\E\iti-open-source\Freelance\elitetech-laravel\resources\views/partials/gate-modal.blade.php ENDPATH**/ ?>