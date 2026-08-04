<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Elite Tech Community — مجتمع النخبة التقنية')</title>
    <meta name="description" content="@yield('description', 'منصة لحرق الأفكار وتحويلها لمشاريع قابلة للتنفيذ — بيئة تشاركية شفافة وموثوقة.')">
    @include('partials.styles')
    @stack('head')
</head>
<body class="min-h-screen antialiased" x-data="{ gateOpen: false, gateMsg: '' }">
    @include('partials.navbar')

    <main class="min-h-[70vh]">
        @if (session('ok'))
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6 animate-fade-in">
                <div class="rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm px-4 py-3 font-medium">{{ session('ok') }}</div>
            </div>
        @endif
        @if (session('error'))
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6 animate-fade-in">
                <div class="rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-sm px-4 py-3 font-medium">{{ session('error') }}</div>
            </div>
        @endif
        @yield('content')
    </main>

    @include('partials.footer')
    @include('partials.gate-modal')

    @stack('scripts')
</body>
</html>
