<footer class="mt-16 border-t border-mist bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="flex items-center gap-2.5">
                <x-logo class="h-8 w-8 object-cover rounded-md" />
                <span class="font-extrabold text-primary text-sm">
                    Elite <span class="text-secondary">Community</span>
                </span>
            </div>

            <nav class="flex flex-wrap items-center gap-x-5 gap-y-2 text-sm text-tertiary">
                <a href="{{ route('ideas.index') }}" class="hover:text-primary transition">بنك الأفكار</a>
                <a href="{{ route('jobs') }}" class="hover:text-primary transition">التوظيف</a>
                <a href="{{ route('about') }}" class="hover:text-primary transition">عن المنصة</a>
                <a href="{{ route('terms') }}" class="hover:text-primary transition">الشروط</a>
                <a href="{{ route('privacy') }}" class="hover:text-primary transition">الخصوصية</a>
                <a href="{{ route('agreement') }}" class="hover:text-primary transition">اتفاقية الاستخدام</a>
            </nav>
        </div>

        <div class="border-t border-mist mt-8 pt-5 text-xs text-tertiary flex flex-wrap justify-between gap-2">
            <span>© {{ date('Y') }} Elite Tech Community. جميع الحقوق محفوظة.</span>
            <span>توثيق KYC · حماية البيانات</span>
        </div>
    </div>
</footer>
