@extends('layouts.app')
@section('title', $user->name)
@section('content')
@php
    $cv = is_array($user->cv?->data) ? $user->cv->data : [];
    $vis = is_array($user->cv?->visibility) ? $user->cv->visibility : [];
    $str = fn ($k) => \App\Http\Controllers\ProfileController::asString($cv[$k] ?? '');
    $list = fn ($k) => \App\Http\Controllers\ProfileController::asSkills($cv[$k] ?? []);
    $showEmail = (bool) ($vis['show_email'] ?? false);
    $showPhone = (bool) ($vis['show_phone'] ?? false);
    $empLabels = ['full_time'=>'دوام كلي','part_time'=>'دوام جزئي','contract'=>'عقود'];
    $workLabels = ['remote'=>'عن بعد','hybrid'=>'هجين','onsite'=>'مقر الشركة'];
@endphp
<div class="max-w-3xl mx-auto px-4 py-12">
    <div class="card p-8">
        <div class="flex items-center gap-4 mb-6">
            <div class="w-20 h-20 rounded-2xl bg-primary text-white grid place-items-center text-3xl font-black">
                {{ mb_substr($user->name, 0, 1) }}
            </div>
            <div>
                <h1 class="text-2xl font-extrabold text-primary">{{ $user->name }}</h1>
                <p class="text-tertiary text-sm">{{ $str('title') ?: ($user->title ?: $user->roleLabel()) }}</p>
                @if($user->isKycApproved())
                    <span class="badge bg-emerald-50 text-emerald-700 mt-1">{{ __('general.verified_badge') }}</span>
                @endif
                @if($user->available_for_hire)
                    <span class="badge bg-secondary/15 text-secondary mt-1">{{ __('general.available_badge') }}</span>
                @endif
            </div>
        </div>

        <div class="text-xs text-tertiary mb-4 space-y-1">
            @if($showEmail)<div>{{ $user->email }}</div>@endif
            @if($showPhone && $str('phone'))<div>{{ $str('phone') }}</div>@endif
            @if($str('location') || $user->location)<div>{{ $str('location') ?: $user->location }}</div>@endif
            @if($str('years_experience'))<div>{{ __('general.experience_label') }}: {{ $str('years_experience') }}</div>@endif
            @if($str('availability'))<div>{{ __('general.availability_label') }}: {{ $str('availability') }}</div>@endif
            @if(!empty($vis['employment_type']))
                <div>{{ $empLabels[$vis['employment_type']] ?? '' }}@if(!empty($vis['work_style'])) · {{ $workLabels[$vis['work_style']] ?? '' }}@endif</div>
            @endif
            <div class="flex flex-wrap gap-3 pt-1">
                @if($str('portfolio_url'))<a class="text-primary underline" href="{{ $str('portfolio_url') }}" target="_blank">Portfolio</a>@endif
                @if($str('linkedin'))<a class="text-primary underline" href="{{ $str('linkedin') }}" target="_blank">LinkedIn</a>@endif
                @if($str('github'))<a class="text-primary underline" href="{{ $str('github') }}" target="_blank">GitHub</a>@endif
            </div>
        </div>

        <p class="text-sm text-tertiary leading-relaxed mb-6 whitespace-pre-line">{{ $str('summary') ?: ($user->bio ?: __('general.community_member')) }}</p>

        @if(count($list('skills')))
            <h3 class="font-bold text-primary text-sm mb-2">{{ __('general.skills_label') }}</h3>
            <div class="flex flex-wrap gap-2 mb-5">
                @foreach($list('skills') as $skill)<span class="badge bg-mist text-primary">{{ $skill }}</span>@endforeach
            </div>
        @endif

        @if($str('experience'))
            <h3 class="font-bold text-primary text-sm mb-1">{{ __('general.experience_section') }}</h3>
            <p class="text-sm text-tertiary whitespace-pre-line mb-5">{{ $str('experience') }}</p>
        @endif

        @if($str('projects'))
            <h3 class="font-bold text-primary text-sm mb-1">{{ __('general.projects_section') }}</h3>
            <p class="text-sm text-tertiary whitespace-pre-line mb-5">{{ $str('projects') }}</p>
        @endif

        @auth
            @if(auth()->id() !== $user->id)
                <a href="{{ route('network.index', ['with' => $user->id]) }}" class="btn-secondary text-sm">{{ __('general.connect_btn') }}</a>
            @else
                <a href="{{ route('profile.cv') }}" class="btn-outline text-sm">{{ __('general.edit_cv_btn') }}</a>
            @endif
        @endauth
    </div>
</div>
@endsection
