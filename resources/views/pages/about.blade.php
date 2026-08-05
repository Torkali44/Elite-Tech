@extends('layouts.app')
@section('title', __('general.about_page_title'))
@section('description', __('general.about_meta_desc'))

@section('content')
{{-- Hero Section --}}
<section class="bg-gradient-to-b from-[#0a192f] via-[#0d2240] to-[#14325a] text-white py-20 lg:py-24 relative overflow-hidden">
    <div class="absolute -top-32 -right-32 w-80 h-80 bg-blue-500/20 rounded-full blur-3xl pointer-events-none"></div>
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
        <span class="inline-block px-4 py-1.5 rounded-full bg-blue-500/20 text-blue-300 text-xs font-bold mb-4 border border-blue-400/30">
            {{ __('general.about_badge') }}
        </span>
        <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black tracking-tight mb-6">
            {{ __('general.about_hero_title') }}
        </h1>
        <p class="text-base sm:text-lg text-blue-100/80 max-w-3xl mx-auto leading-relaxed font-normal">
            {{ __('general.about_hero_subtitle') }}
        </p>
    </div>
</section>

{{-- Main Content Container --}}
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-16 space-y-16">
    {{-- Core Philosophy --}}
    <div class="grid lg:grid-cols-2 gap-10 items-center">
        <div class="space-y-5">
            <div class="inline-flex items-center gap-2 text-amber-600 font-extrabold text-sm">
                <span>💡</span> <span>{{ __('general.about_philosophy_badge') }}</span>
            </div>
            <h2 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
                {{ __('general.about_philosophy_title') }}
            </h2>
            <p class="text-slate-600 leading-relaxed text-sm sm:text-base">
                {{ __('general.about_philosophy_body') }}
            </p>
        </div>
        <div class="bg-gradient-to-br from-blue-900 to-slate-900 text-white rounded-3xl p-8 shadow-xl space-y-4">
            <h3 class="font-extrabold text-lg text-amber-400">{{ __('general.about_why_title') }}</h3>
            <ul class="space-y-3 text-sm text-slate-200">
                <li class="flex items-start gap-3">
                    <span class="text-amber-400 font-bold">✓</span>
                    <span><strong>{{ __('general.about_why_transparency') }}</strong> {{ __('general.about_why_transparency_desc') }}</span>
                </li>
                <li class="flex items-start gap-3">
                    <span class="text-amber-400 font-bold">✓</span>
                    <span><strong>{{ __('general.about_why_kyc') }}</strong> {{ __('general.about_why_kyc_desc') }}</span>
                </li>
                <li class="flex items-start gap-3">
                    <span class="text-amber-400 font-bold">✓</span>
                    <span><strong>{{ __('general.about_why_fork') }}</strong> {{ __('general.about_why_fork_desc') }}</span>
                </li>
            </ul>
        </div>
    </div>

    {{-- The 3 User Tracks --}}
    <div class="space-y-8">
        <div class="text-center max-w-2xl mx-auto">
            <h2 class="text-2xl sm:text-3xl font-black text-slate-900 mb-3">{{ __('general.about_tracks_title') }}</h2>
            <p class="text-slate-600 text-sm sm:text-base">{{ __('general.about_tracks_subtitle') }}</p>
        </div>

        <div class="grid md:grid-cols-3 gap-6">
            {{-- Track 1 --}}
            <div class="bg-white rounded-2xl p-7 border border-slate-200 shadow-sm flex flex-col justify-between">
                <div>
                    <div class="w-12 h-12 rounded-xl bg-amber-500/10 text-amber-600 grid place-items-center font-black text-xl mb-5">
                        💡
                    </div>
                    <h3 class="font-extrabold text-xl text-slate-900 mb-2">{{ __('general.about_track1_title') }}</h3>
                    <p class="text-slate-600 text-sm leading-relaxed mb-4">
                        {{ __('general.about_track1_body') }}
                    </p>
                </div>
                <div class="pt-4 border-t border-slate-100 text-xs font-bold text-slate-500">
                    {{ __('general.about_track1_badge') }}
                </div>
            </div>

            {{-- Track 2 --}}
            <div class="bg-white rounded-2xl p-7 border border-blue-200 ring-1 ring-blue-500/20 shadow-sm flex flex-col justify-between">
                <div>
                    <div class="w-12 h-12 rounded-xl bg-blue-500/10 text-blue-600 grid place-items-center font-black text-xl mb-5">
                        🔍
                    </div>
                    <h3 class="font-extrabold text-xl text-slate-900 mb-2">{{ __('general.about_track2_title') }}</h3>
                    <p class="text-slate-600 text-sm leading-relaxed mb-4">
                        {{ __('general.about_track2_body') }}
                    </p>
                </div>
                <div class="pt-4 border-t border-slate-100 text-xs font-bold text-emerald-600">
                    {{ __('general.about_track2_badge') }}
                </div>
            </div>

            {{-- Track 3 --}}
            <div class="bg-white rounded-2xl p-7 border border-slate-200 shadow-sm flex flex-col justify-between">
                <div>
                    <div class="w-12 h-12 rounded-xl bg-slate-900 text-white grid place-items-center font-black text-xl mb-5">
                        👨‍💻
                    </div>
                    <h3 class="font-extrabold text-xl text-slate-900 mb-2">{{ __('general.about_track3_title') }}</h3>
                    <p class="text-slate-600 text-sm leading-relaxed mb-4">
                        {{ __('general.about_track3_body') }}
                    </p>
                </div>
                <div class="pt-4 border-t border-slate-100 text-xs font-bold text-slate-500">
                    {{ __('general.about_track3_badge') }}
                </div>
            </div>
        </div>
    </div>

    {{-- Call to action --}}
    <div class="bg-slate-50 rounded-3xl p-10 text-center border border-slate-200 space-y-6">
        <h3 class="text-2xl font-black text-slate-900">{{ __('general.about_cta_title') }}</h3>
        <p class="text-slate-600 max-w-xl mx-auto text-sm leading-relaxed">
            {{ __('general.about_cta_body') }}
        </p>
        <div class="flex flex-wrap justify-center gap-4 pt-2">
            <a href="{{ route('register') }}" class="btn-secondary !py-3 !px-8">{{ __('general.about_register_btn') }}</a>
            <a href="{{ route('ideas.index') }}" class="btn-outline !py-3 !px-8">{{ __('general.about_browse_btn') }}</a>
        </div>
    </div>
</div>
@endsection
