<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', __('auth.welcome')) — Elite Tech</title>
    @include('partials.styles')
</head>
<body class="min-h-screen bg-neutral relative">
<div class="absolute top-4 {{ app()->getLocale() === 'ar' ? 'left-4' : 'right-4' }} z-50">
    <a href="{{ route('lang.switch', app()->getLocale() === 'ar' ? 'en' : 'ar') }}"
       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-extrabold bg-white border border-mist text-primary hover:bg-neutral transition shadow-sm">
        🌐 {{ app()->getLocale() === 'ar' ? 'English' : 'العربية' }}
    </a>
</div>
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
                <h1 class="text-3xl font-extrabold mb-3 leading-tight">{{ __('auth.welcome') }}</h1>
                <p class="text-white/80 leading-relaxed text-sm">
                    {{ __('auth.tagline') }}
                </p>
            </div>
            <div class="flex gap-4 text-xs text-white/70">
                <a href="{{ route('privacy') }}" class="hover:text-white">{{ __('navigation.privacy') }}</a>
                <a href="{{ route('terms') }}" class="hover:text-white">{{ __('navigation.terms') }}</a>
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
