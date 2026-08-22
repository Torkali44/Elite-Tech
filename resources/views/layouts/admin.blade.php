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
<div x-data="{ open:false }" class="flex min-h-screen relative overflow-x-hidden">
    <aside :class="open ? 'translate-x-0 shadow-2xl' : ({{ app()->getLocale() === 'ar' ? "'translate-x-full lg:translate-x-0'" : "'-translate-x-full lg:translate-x-0'" }})"
           class="fixed lg:sticky top-0 inset-y-0 {{ app()->getLocale() === 'ar' ? 'right-0 border-l border-white/10' : 'left-0 border-r border-white/10' }} w-72 h-screen bg-primary text-white z-50 transform transition-transform duration-250 ease-out flex flex-col justify-between">
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
        <nav class="p-4 space-y-1 flex-1 overflow-y-auto">
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

    <div class="flex-1 min-w-0 bg-neutral flex flex-col min-h-screen">
        <header class="bg-white border-b border-mist sticky top-0 z-30">
            <div class="flex items-center justify-between px-4 lg:px-8 h-16 gap-4">
                <button @click="open=!open" type="button" class="lg:hidden p-2 rounded-lg hover:bg-neutral">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <div class="flex items-center gap-3">
                    <span class="badge bg-primary/10 text-primary">{{ __('admin.active_admin_session') }}</span>
                    <!-- Notifications -->
                    <a href="{{ route('notifications.index') }}" class="relative p-2 rounded-lg hover:bg-neutral transition text-slate-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                        @php $unreadCount = auth()->user()->unreadNotifications->count(); @endphp
                        @if($unreadCount > 0)
                            <span class="absolute top-1 {{ app()->getLocale() === 'ar' ? 'left-1' : 'right-1' }} bg-rose-500 text-white text-[10px] font-bold w-4 h-4 rounded-full flex items-center justify-center border border-white">{{ $unreadCount > 9 ? '9+' : $unreadCount }}</span>
                        @endif
                    </a>
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
        <main class="p-4 lg:p-8 flex-1">
            @if (session('ok'))
                <div class="mb-6 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm px-4 py-3">{{ session('ok') }}</div>
            @endif
            @if (session('error'))
                <div class="mb-6 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-sm px-4 py-3">{{ session('error') }}</div>
            @endif
            @yield('content')
        </main>
    </div>
    <div x-show="open" x-cloak @click="open=false" class="fixed inset-0 bg-ink/40 z-40 lg:hidden"></div>

    {{-- Toast Notification for unread alerts --}}
    @if(auth()->check() && auth()->user()->unreadNotifications->count() > 0 && !request()->routeIs('notifications.index'))
    <div x-data="{ showToast: false }" 
         x-init="setTimeout(() => { showToast = true; const audio = new Audio('https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3'); audio.volume = 0.5; audio.play().catch(e => {}); }, 1000); setTimeout(() => showToast = false, 8000)"
         x-show="showToast"
         x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform translate-y-4"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 transform translate-y-0"
         x-transition:leave-end="opacity-0 transform translate-y-4"
         class="fixed bottom-6 {{ app()->getLocale() === 'ar' ? 'left-6' : 'right-6' }} z-[80] bg-white border border-mist shadow-lg rounded-xl p-4 flex items-start gap-3 max-w-sm">
        
        <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center shrink-0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
        </div>
        <div class="flex-1">
            <h4 class="text-sm font-bold text-primary mb-1">لديك إشعارات جديدة</h4>
            <p class="text-xs text-tertiary mb-3">يرجى التحقق من قائمة الإشعارات لمعرفة التحديثات الجديدة على حسابك.</p>
            <div class="flex gap-2">
                <a href="{{ route('notifications.index') }}" class="btn-primary !py-1.5 !px-3 text-xs w-full text-center">عرض الإشعارات</a>
                <button @click="showToast = false" class="btn-outline !py-1.5 !px-3 text-xs">إغلاق</button>
            </div>
        </div>
    </div>
    @endif
</div>
</body>
</html>
