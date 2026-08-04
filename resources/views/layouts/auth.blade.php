<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title','مصادقة') — Elite Tech</title>
    @include('partials.styles')
</head>
<body class="min-h-screen bg-neutral">
<div class="min-h-screen grid lg:grid-cols-2">
    <aside class="hidden lg:block relative overflow-hidden">
        <img src="https://images.unsplash.com/photo-1600880292203-757bb62b4baf?w=1200&q=80"
             alt="Elite Tech Community"
             class="absolute inset-0 w-full h-full object-cover">
        <div class="absolute inset-0 bg-primary/70"></div>
        <div class="relative z-10 h-full flex flex-col justify-between p-12 text-white">
            <div>
                <div class="font-extrabold text-lg">
                    Elite <span class="text-secondary">Community</span>
                </div>
            </div>
            <div class="max-w-md">
                <h1 class="text-3xl font-extrabold mb-3 leading-tight">أهلاً بك في Elite Tech</h1>
                <p class="text-white/80 leading-relaxed text-sm">
                    منصة تشاركية شفافة — تصفح بحرية، وتفاعل بعد التوثيق.
                </p>
            </div>
            <div class="flex gap-4 text-xs text-white/70">
                <a href="{{ route('privacy') }}" class="hover:text-white">سياسة الخصوصية</a>
                <a href="{{ route('terms') }}" class="hover:text-white">الشروط والأحكام</a>
            </div>
        </div>
    </aside>

    <div class="flex items-center justify-center p-6 sm:p-12">
        <div class="w-full max-w-md">
            <a href="{{ route('home') }}" class="flex items-center gap-2.5 mb-8 lg:hidden">
                <x-logo class="h-10 w-10 object-cover rounded-lg" />
                <div class="font-extrabold text-primary text-sm">
                    Elite <span class="text-secondary">Community</span>
                </div>
            </a>
            <div class="hidden lg:flex items-center gap-2.5 mb-8">
                <x-logo class="h-10 w-10 object-cover rounded-lg" />
                <div class="font-extrabold text-primary text-sm">
                    Elite <span class="text-secondary">Community</span>
                </div>
            </div>
            @yield('content')
        </div>
    </div>
</div>
</body>
</html>
