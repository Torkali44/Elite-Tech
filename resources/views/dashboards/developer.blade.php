@extends('layouts.dashboard')
@section('title','مسار المطور')
@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-4">
    <div>
        <span class="badge bg-emerald-50 text-emerald-600 mb-2">Status: Enabled (Active)</span>
        <h1 class="text-2xl lg:text-3xl font-black text-primary">مسار باحث عن عمل - التفاصيل</h1>
    </div>
    <div class="flex items-center gap-2">
        <button class="btn-primary text-sm !w-auto">Upgrade to Pro</button>
    </div>
</div>

<div class="grid lg:grid-cols-[260px_1fr] gap-6">
    {{-- Visibility --}}
    <div class="space-y-4">
        <div class="card p-5">
            <h3 class="font-bold text-primary mb-4 flex items-center gap-2">👁 Visibility Settings</h3>
            <label class="flex items-center justify-between mb-3 text-sm">
                <span class="text-tertiary">Available for Hire</span>
                <input type="checkbox" checked class="toggle accent-primary">
            </label>
            <label class="flex items-center justify-between text-sm">
                <span class="text-tertiary">Show Salary Expectation</span>
                <input type="checkbox" class="toggle accent-primary">
            </label>
        </div>
        <div class="card p-5 space-y-2">
            <button class="btn-primary w-full text-sm">Manage Profile ✎</button>
            <button class="w-full border border-red-300 text-red-600 font-bold py-2.5 rounded-lg text-sm hover:bg-red-50">Deactivate Track ✕</button>
        </div>
    </div>

    {{-- Timeline --}}
    <div class="card p-6">
        <h3 class="font-bold text-primary mb-6">Verified Steps</h3>
        <div class="space-y-6">
            @foreach([
                ['t'=>'Resume Submitted','n'=>1,'s'=>'Verified on Oct 12, 2023. Parsing complete.','st'=>'Verified','color'=>'emerald'],
                ['t'=>'Identity Verification','n'=>2,'s'=>'Elite Member ID check successful.','st'=>'Verified','color'=>'emerald'],
                ['t'=>'Profile Visible','n'=>3,'s'=>'Your profile is now active on the global network.','st'=>'Active','color'=>'blue'],
            ] as $step)
            <div class="flex items-start gap-4">
                <div class="w-10 h-10 rounded-full bg-{{ $step['color'] }}-50 text-{{ $step['color'] }}-600 grid place-items-center shrink-0">✓</div>
                <div class="flex-1">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="badge bg-{{ $step['color'] }}-50 text-{{ $step['color'] }}-600">{{ $step['st'] }}</span>
                        <span class="font-bold text-primary">{{ $step['t'] }} .{{ $step['n'] }}</span>
                    </div>
                    <p class="text-xs text-tertiary mt-1">{{ $step['s'] }}</p>
                </div>
            </div>
            @endforeach
        </div>

        <div class="mt-8 bg-primary rounded-2xl p-6 text-white">
            <h3 class="font-bold text-lg mb-2">Explore the Job Board</h3>
            <p class="text-white/80 text-sm mb-4">Access exclusive opportunities tailored for EliteTech members. New openings in engineering and product management updated daily.</p>
            <a href="{{ route('jobs') }}" class="inline-block bg-white text-primary font-bold px-5 py-2 rounded-lg text-sm">تصفح الوظائف ←</a>
        </div>
    </div>
</div>
@endsection

@push('head')<style>.btn-primary { @apply inline-flex items-center gap-2 bg-primary text-white font-bold px-5 py-2.5 rounded-lg hover:bg-primary-600; }</style>@endpush
