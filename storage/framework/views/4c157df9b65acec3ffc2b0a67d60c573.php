
<div x-show="gateOpen" x-cloak class="fixed inset-0 z-[60] flex items-center justify-center p-4"
     @keydown.escape.window="gateOpen=false">
    <div class="absolute inset-0 gate-backdrop" @click="gateOpen=false"></div>
    <div class="relative card max-w-md w-full p-7 animate-fade-up shadow-card-hover" @click.stop>
        <button type="button" @click="gateOpen=false" class="absolute left-4 top-4 text-tertiary hover:text-primary text-xl leading-none">×</button>
        <div class="w-12 h-12 rounded-xl bg-secondary/15 text-secondary grid place-items-center text-xl mb-4 font-black">!</div>
        <h3 class="text-xl font-black text-primary mb-2">سجّل للمتابعة</h3>
        <p class="text-sm text-tertiary leading-relaxed mb-6" x-text="gateMsg || 'التصفح متاح للجميع. التفاعل العميق (تعليق، تواصل، رغبة في التنفيذ، نشر فكرة) يتطلب حساباً.'"></p>
        <div class="flex flex-col sm:flex-row gap-2">
            <a href="<?php echo e(route('register')); ?>" class="btn-secondary flex-1 text-center">إنشاء حساب</a>
            <a href="<?php echo e(route('login')); ?>" class="btn-outline flex-1 text-center">تسجيل الدخول</a>
        </div>
        <button type="button" @click="gateOpen=false" class="w-full mt-3 text-sm text-tertiary hover:text-primary">المتابعة كزائر</button>
    </div>
</div>
<?php /**PATH C:\E\iti-open-source\Freelance\elitetech-laravel\resources\views/partials/gate-modal.blade.php ENDPATH**/ ?>