<header x-data="{ mobileOpen: false }" class="sticky top-0 z-50 bg-white border-b border-mist">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16 gap-4">
            <a href="{{ route('home') }}" class="flex items-center gap-2 shrink-0">
                <x-logo class="h-9 sm:h-10 w-auto max-w-[130px] sm:max-w-[180px] object-contain rounded-lg" />
                <div class="leading-tight hidden sm:block">
                    <div class="font-extrabold text-primary text-sm tracking-tight">
                        Elite <span class="text-secondary">Community</span>
                    </div>
                </div>
            </a>

            <nav class="hidden lg:flex items-center gap-1 text-sm font-semibold">
                @php
                    $links = [
                        ['home', __('navigation.home')],
                        ['ideas.index', __('navigation.ideas_bank')],
                        ['jobs', __('navigation.jobs_forum')],
                        ['about', __('navigation.about')],
                    ];
                @endphp
                @foreach($links as [$route, $label])
                    @php
                        $isActive = request()->routeIs($route) || ($route === 'ideas.index' && request()->routeIs('ideas.*'));
                    @endphp
                    <a href="{{ route($route) }}"
                       class="px-3.5 py-2 rounded-md transition-colors {{ $isActive ? 'text-primary font-extrabold' : 'text-tertiary hover:text-primary' }}">
                        {{ $label }}
                    </a>
                @endforeach
                <a href="https://elitemsr.com/contactus" target="_blank" rel="noopener noreferrer"
                   class="px-3.5 py-2 rounded-md transition-colors text-tertiary hover:text-primary">
                    {{ __('navigation.contact_us') }}
                </a>
            </nav>

            <div class="flex items-center gap-1.5 sm:gap-2 shrink-0">
                @auth
                    <!-- Notifications -->
                    <a href="{{ route('notifications.index') }}" class="relative p-1.5 sm:p-2 rounded-lg hover:bg-neutral transition text-slate-500 h-9 w-9 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                        @php $unreadCount = auth()->user()->unreadNotifications->count(); @endphp
                        @if($unreadCount > 0)
                            <span class="absolute top-0.5 {{ app()->getLocale() === 'ar' ? 'left-0.5' : 'right-0.5' }} bg-rose-500 text-white text-[9px] font-bold w-4 h-4 rounded-full flex items-center justify-center border border-white">{{ $unreadCount > 9 ? '9+' : $unreadCount }}</span>
                        @endif
                    </a>
                @endauth
                
                <!-- Language Switcher -->
                <a href="{{ route('lang.switch', app()->getLocale() === 'ar' ? 'en' : 'ar') }}"
                   class="px-2 py-1.5 sm:px-2.5 sm:py-1.5 rounded-lg text-xs font-extrabold bg-neutral text-primary border border-mist hover:bg-mist transition flex items-center gap-1 shrink-0 h-9">
                    🌐 {{ app()->getLocale() === 'ar' ? 'English' : 'العربية' }}
                </a>

                @auth
                    <a href="{{ route('dashboard') }}" class="btn-primary text-xs sm:text-sm !py-1.5 !px-2.5 sm:!py-2 sm:!px-4 shrink-0 !min-h-0 h-9 sm:h-10">{{ __('navigation.dashboard') }}</a>
                    <form action="{{ route('logout') }}" method="POST" class="inline-block">
                        @csrf
                        <button type="submit" class="btn-ghost text-xs sm:text-sm !py-1.5 !px-2 text-rose-600 font-bold shrink-0 h-9 flex items-center gap-1">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                            <span class="hidden sm:inline">{{ __('navigation.logout') }}</span>
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="btn-ghost text-xs sm:text-sm hidden sm:inline-flex">{{ __('navigation.login') }}</a>
                    <a href="{{ route('register') }}" class="btn-secondary text-xs sm:text-sm !py-1.5 !px-2.5 sm:!py-2 sm:!px-4 shrink-0 !min-h-0 h-9 sm:h-10">{{ __('navigation.register') }}</a>
                @endauth

                <button @click="mobileOpen = !mobileOpen"
                        type="button"
                        class="lg:hidden p-1.5 sm:p-2 rounded-md bg-neutral text-primary shrink-0 h-9 w-9 flex items-center justify-center"
                        aria-label="{{ __('navigation.menu') }}">
                    <svg x-show="!mobileOpen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    <svg x-show="mobileOpen" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>

        <nav x-show="mobileOpen"
             x-cloak
             x-transition
             class="lg:hidden py-3 flex flex-col gap-1 border-t border-mist">
            @foreach($links as [$route, $label])
                @php
                    $isActive = request()->routeIs($route) || ($route === 'ideas.index' && request()->routeIs('ideas.*'));
                @endphp
                <a href="{{ route($route) }}"
                   class="px-3 py-2.5 rounded-md font-semibold text-sm {{ $isActive ? 'bg-primary/5 text-primary' : 'text-tertiary hover:bg-neutral' }}">
                    {{ $label }}
                </a>
            @endforeach
            <a href="https://elitemsr.com/contactus" target="_blank" rel="noopener noreferrer"
               class="px-3 py-2.5 rounded-md font-semibold text-sm text-tertiary hover:bg-neutral">
                {{ __('navigation.contact_us') }}
            </a>

            <div class="pt-3 border-t border-mist mt-1 flex flex-col gap-2">
                @auth
                    <a href="{{ route('dashboard') }}" class="btn-primary w-full text-center text-sm">{{ __('navigation.dashboard') }}</a>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button class="btn-ghost w-full text-center text-sm text-rose-600">{{ __('navigation.logout_full') }}</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="btn-outline w-full text-center text-sm">{{ __('navigation.login') }}</a>
                    <a href="{{ route('register') }}" class="btn-secondary w-full text-center text-sm">{{ __('navigation.register') }}</a>
                @endauth
            </div>
        </nav>
    </div>
</header>
