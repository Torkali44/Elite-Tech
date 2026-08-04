<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'لوحة التحكم') — Elite Tech Community</title>
    @include('partials.styles')
    <style>
      @media print {
        aside, header, .no-print, .gate-backdrop { display: none !important; }
        body { background: #fff !important; }
        main { padding: 0 !important; max-width: 100% !important; }
        .card { box-shadow: none !important; border: none !important; }
      }
    </style>
    @stack('head')
</head>
<body class="min-h-screen bg-neutral text-ink">
@php
    $roleLabels = [
        'idea_owner' => 'صاحب فكرة',
        'idea_seeker' => 'باحث عن فكرة',
        'developer' => 'باحث عن عمل',
        'admin' => 'إدارة',
    ];
    $user = auth()->user();
    $primaryRole = $user->role ?? 'developer';
    $active = optional(request()->route())->getName();
    $latestVerification = $user->latestVerification;
    $effectiveKycStatus = ($user->hasRole('idea_seeker') && ($user->kyc_status ?? 'none') === 'approved')
        ? 'approved'
        : ($latestVerification && in_array($latestVerification->status, ['pending', 'rejected'], true)
            ? $latestVerification->status
            : ($user->kyc_status ?? 'none'));
@endphp

<div x-data="{ open: false, popup: @js(session('popup')) }"
     x-init="if (popup) { setTimeout(() => {}, 0) }"
     class="flex min-h-screen relative overflow-x-hidden">

    <aside :class="open ? 'translate-x-0 shadow-lg' : 'translate-x-full lg:translate-x-0'"
           class="no-print fixed lg:sticky top-0 inset-y-0 right-0 w-64 h-screen bg-white border-l border-mist z-50 transform transition-transform duration-250 ease-out flex flex-col justify-between">

        <div class="overflow-y-auto">
            <div class="p-5 border-b border-mist">
                <div class="flex items-center gap-3">
                    <x-logo class="h-10 w-10 object-cover rounded-lg shrink-0" />
                    <div class="min-w-0 flex-1">
                        <div class="font-extrabold text-primary text-sm truncate">{{ $user->name ?? 'عضو' }}</div>
                        <div class="text-xs text-tertiary truncate">{{ $roleLabels[$primaryRole] ?? ($user->title ?? 'عضو') }}</div>
                    </div>
                </div>

                @if(!$user->hasRole('idea_seeker') || $user->hasRole('idea_owner'))
                    @if($effectiveKycStatus === 'approved')
                        <div class="mt-3 badge bg-emerald-50 text-emerald-700 w-full justify-center py-1.5">موثّق KYC</div>
                    @elseif($effectiveKycStatus === 'pending')
                        <div class="mt-3 badge bg-amber-50 text-amber-700 w-full justify-center py-1.5">قيد مراجعة KYC</div>
                    @elseif($effectiveKycStatus === 'rejected')
                        <div class="mt-3 badge bg-rose-50 text-rose-700 w-full justify-center py-1.5">مرفوض — أكمل البيانات</div>
                    @else
                        <a href="{{ route('verification.kyc') }}" class="mt-3 badge bg-secondary/15 text-secondary w-full justify-center py-1.5 hover:bg-secondary/25 transition">
                            أكمل التوثيق الآن
                        </a>
                    @endif
                @endif
            </div>

            <nav class="p-3 space-y-0.5">
                <a href="{{ route('dashboard') }}" class="side-link {{ $active==='dashboard' ? 'active':'' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                    <span>لوحة التحكم</span>
                </a>

                @if($user->hasRole('idea_owner'))
                    <a href="{{ route('dashboard.ideaOwner') }}" class="side-link {{ $active==='dashboard.ideaOwner' ? 'active':'' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                        <span>إدارة أفكاري</span>
                    </a>
                    <a href="{{ route('dashboard.implementRequests') }}" class="side-link {{ $active==='dashboard.implementRequests' ? 'active':'' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                        <span>طلبات التنفيذ</span>
                    </a>
                @endif

                <a href="{{ route('ideas.index') }}" class="side-link {{ str_starts_with((string)$active,'ideas') ? 'active':'' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    <span>بنك الأفكار</span>
                </a>
                <a href="{{ route('jobs') }}" class="side-link {{ $active==='jobs' ? 'active':'' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    <span>منتدى التوظيف</span>
                </a>
                <a href="{{ route('network.index') }}" class="side-link {{ str_starts_with((string)$active,'network') ? 'active':'' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                    <span>الرسائل</span>
                </a>
                <a href="{{ route('profile.cv') }}" class="side-link {{ str_starts_with((string)$active,'profile') ? 'active':'' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    <span>الملف و CV</span>
                </a>
                @if(!$user->hasRole('idea_seeker') || $user->hasRole('idea_owner'))
                <a href="{{ route('verification.kyc') }}" class="side-link {{ str_starts_with((string)$active,'verification') ? 'active':'' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    <span>التحقق KYC</span>
                </a>
                @endif
                <a href="{{ route('auth.path') }}" class="side-link {{ $active==='auth.path' ? 'active':'' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    <span>تغيير المسار</span>
                </a>
                <a href="{{ route('settings') }}" class="side-link {{ $active==='settings' ? 'active':'' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <span>الإعدادات</span>
                </a>
            </nav>
        </div>

        <div class="p-3 border-t border-mist space-y-0.5">
            <a href="{{ route('home') }}" class="side-link text-tertiary">
                <span>العودة للموقع</span>
            </a>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button class="side-link w-full text-rose-600 hover:!bg-rose-50">
                    <span>تسجيل الخروج</span>
                </button>
            </form>
        </div>
    </aside>

    <div class="flex-1 min-w-0 flex flex-col min-h-screen">
        <header class="no-print bg-white border-b border-mist sticky top-0 z-30">
            <div class="flex items-center justify-between px-4 lg:px-8 h-14 gap-4">
                <div class="flex items-center gap-3">
                    <button @click="open = !open"
                            type="button"
                            class="lg:hidden p-2 rounded-md bg-neutral text-primary">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <div>
                        <div class="text-sm font-extrabold text-primary">{{ $roleLabels[$primaryRole] ?? 'لوحة العضو' }}</div>
                        <div class="text-xs text-tertiary">مرحباً، {{ $user->name }}</div>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    @if($user->hasRole('idea_owner'))
                        <a href="{{ route('ideas.create') }}" class="btn-secondary text-sm !py-2 !px-4">+ فكرة جديدة</a>
                    @elseif($user->hasRole('idea_seeker') && ! $user->isKycApproved())
                        <a href="{{ route('verification.kyc', ['purpose' => 'implement']) }}" class="btn-secondary text-sm !py-2 !px-4">أكمل KYC</a>
                    @else
                        <a href="{{ route('auth.path') }}" class="btn-outline text-sm !py-2 !px-4 hidden sm:inline-flex">تغيير المسار</a>
                    @endif
                </div>
            </div>
        </header>

        <main class="p-4 sm:p-6 lg:p-8 flex-1 max-w-7xl w-full mx-auto">
            @if (session('ok'))
                <div class="mb-5 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm px-4 py-3 font-semibold">
                    {{ session('ok') }}
                </div>
            @endif
            @if (session('error'))
                <div class="mb-5 rounded-lg bg-rose-50 border border-rose-200 text-rose-700 text-sm px-4 py-3 font-semibold">
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <div x-show="open"
         x-cloak
         @click="open = false"
         x-transition.opacity
         class="fixed inset-0 bg-primary/40 z-40 lg:hidden">
    </div>

    {{-- Simple alert popup --}}
    <div x-show="popup"
         x-cloak
         class="fixed inset-0 z-[70] flex items-center justify-center p-4"
         @keydown.escape.window="popup = null">
        <div class="absolute inset-0 bg-primary/40" @click="popup = null"></div>
        <div class="relative bg-white rounded-xl border border-mist shadow-card max-w-sm w-full p-6 text-center"
             x-transition>
            <div class="w-12 h-12 rounded-full bg-secondary/15 text-secondary grid place-items-center mx-auto mb-4 text-xl font-bold">!</div>
            <p class="text-sm text-primary font-semibold leading-relaxed mb-5" x-text="popup"></p>
            <button type="button" @click="popup = null" class="btn-primary w-full text-sm">حسناً</button>
        </div>
    </div>
</div>
@stack('scripts')
</body>
</html>
