<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', __('admin.panel_title')) — Elite Tech Admin</title>
    @include('partials.styles')
    @stack('head')
</head>
<body class="min-h-screen">
<div x-data="{ open:false }" class="flex min-h-screen">
    <aside :class="open ? 'translate-x-0' : ({{ app()->getLocale() === 'ar' ? "'translate-x-full lg:translate-x-0'" : "'-translate-x-full lg:translate-x-0'" }})"
           class="fixed lg:static inset-y-0 {{ app()->getLocale() === 'ar' ? 'right-0' : 'left-0' }} w-72 bg-primary text-white z-40 transform transition lg:transform-none flex flex-col">
        <div class="p-6 border-b border-white/10 flex items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <x-logo class="h-10 w-auto max-w-[140px] object-contain rounded-lg" />
                <div>
                    <div class="font-bold text-sm">Elite Tech</div>
                    <div class="text-xs text-white/60">{{ __('admin.panel_title') }}</div>
                </div>
            </div>
            {{-- Mobile close button --}}
            <button @click="open = false" type="button" class="lg:hidden p-2 rounded-lg bg-white/10 text-white hover:bg-white/20 transition shrink-0" aria-label="Close Sidebar">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <nav class="p-4 space-y-1 flex-1">
            @php $active = optional(request()->route())->getName(); @endphp
            @foreach([
                ['admin.dashboard', __('admin.nav.overview'), 'M3 12l9-9 9 9M5 10v10h14V10'],
                ['admin.verifications', __('admin.nav.kyc'), 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                ['admin.ideas', __('admin.nav.ideas'), 'M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3'],
                ['admin.users', __('admin.nav.users'), 'M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87'],
                ['admin.implementations', __('admin.nav.implementations'), 'M13 10V3L4 14h7v7l9-11h-7z'],
                ['admin.reports', __('admin.nav.reports'), 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6m6 0V9a2 2 0 012-2h2a2 2 0 012 2v10'],
            ] as $item)
            <a href="{{ route($item[0]) }}"
               class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm transition {{ $active===$item[0] ? 'bg-white/15 text-white font-bold' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item[2] }}"/></svg>
                {{ $item[1] }}
            </a>
            @endforeach
        </nav>
        <div class="p-4 border-t border-white/10">
            <form action="{{ route('admin.logout') }}" method="POST">
                @csrf
                <button class="w-full text-sm font-bold text-rose-200 border border-rose-300/30 rounded-lg py-2.5 hover:bg-rose-500/20">{{ __('admin.admin_logout') }}</button>
            </form>
        </div>
    </aside>

    <div class="flex-1 min-w-0 bg-neutral">
        <header class="bg-white border-b border-mist sticky top-0 z-30">
            <div class="flex items-center justify-between px-4 lg:px-8 h-16 gap-4">
                <button @click="open=!open" type="button" class="lg:hidden p-2 rounded-lg hover:bg-neutral">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <div class="flex items-center gap-3">
                    <span class="badge bg-primary/10 text-primary">{{ __('admin.active_admin_session') }}</span>
                    <!-- Language Switcher -->
                    <a href="{{ route('lang.switch', app()->getLocale() === 'ar' ? 'en' : 'ar') }}"
                       class="px-2.5 py-1.5 rounded-lg text-xs font-extrabold bg-neutral text-primary border border-mist hover:bg-mist transition flex items-center gap-1">
                        🌐 {{ app()->getLocale() === 'ar' ? 'English' : 'العربية' }}
                    </a>
                </div>
                <div class="flex items-center gap-2">
                    @auth
                    <span class="text-xs font-bold text-tertiary hidden sm:block">{{ auth()->user()->name }}</span>
                    <div class="w-9 h-9 rounded-full bg-primary text-white grid place-items-center font-bold text-xs shrink-0">
                        {{ mb_substr(auth()->user()->name ?? 'A', 0, 1) }}
                    </div>
                    @else
                    <div class="w-9 h-9 rounded-full bg-primary text-white grid place-items-center font-bold text-xs">A</div>
                    @endauth
                </div>
            </div>
        </header>
        <main class="p-4 lg:p-8">
            @if (session('ok'))
                <div class="mb-6 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm px-4 py-3">{{ session('ok') }}</div>
            @endif
            @if (session('error'))
                <div class="mb-6 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-sm px-4 py-3">{{ session('error') }}</div>
            @endif
            @yield('content')
        </main>
    </div>
    <div x-show="open" x-cloak @click="open=false" class="fixed inset-0 bg-ink/40 z-30 lg:hidden"></div>
</div>
</body>
</html>
