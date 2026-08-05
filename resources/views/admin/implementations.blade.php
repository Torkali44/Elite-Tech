@extends('layouts.admin')
@section('title', __('admin.implementations_title'))
@section('content')
<h1 class="text-2xl font-black text-primary mb-2">{{ __('admin.implementations_title') }}</h1>
<p class="text-sm text-tertiary mb-6">{{ __('admin.implementations_desc') }}</p>

<div class="space-y-4">
@forelse($requests as $r)
<div class="card p-5" x-data="{ rej:false }">
    <div class="flex flex-wrap justify-between gap-3">
        <div>
            <div class="font-bold text-primary">{{ $r->idea->title ?? '—' }}</div>
            <div class="text-xs text-tertiary mt-1">
                {{ __('admin.applicant') }}: {{ $r->user->name ?? '—' }} ({{ $r->user->email ?? '' }})
                · {{ __('admin.idea_owner') }}: {{ $r->idea->user->name ?? '—' }}
                · {{ $r->via === 'elite_tech' ? __('admin.elite_tech_via') : __('admin.direct_via') }}
            </div>
            @if($r->note)<p class="text-xs mt-2 bg-mist rounded-lg px-2 py-1">{{ $r->note }}</p>@endif
        </div>
        <span class="badge bg-mist text-primary">{{ $r->status }}</span>
    </div>
    <div class="flex gap-2 mt-3 items-center">
        <a href="{{ route('admin.implementations.show', $r->id) }}" class="btn-ghost text-sm !py-2 border border-mist hover:bg-mist">{{ __('admin.view_details') }}</a>
        @if($r->status === 'pending')
        <form method="POST" action="{{ route('admin.implementations.approve', $r->id) }}">@csrf
            <button class="btn-primary text-sm !py-2">{{ __('admin.approve') }}</button>
        </form>
        <button type="button" @click="rej=!rej" class="btn-outline text-sm !py-2 !text-rose-600 !border-rose-300">{{ __('admin.reject') }}</button>
        @endif
    </div>
    @if($r->status === 'pending')
    <form x-show="rej" x-cloak method="POST" action="{{ route('admin.implementations.reject', $r->id) }}" class="mt-2 space-y-2">@csrf
        <textarea name="reason" class="input" rows="2" required placeholder="{{ __('admin.rejection_reason') }}"></textarea>
        <button class="btn-secondary text-sm !py-2">{{ __('admin.confirm_reject') }}</button>
    </form>
    @endif
</div>
@empty
<div class="card p-10 text-center text-tertiary">{{ __('admin.no_requests') }}</div>
@endforelse
</div>
<div class="mt-6">{{ $requests->links() }}</div>
@endsection
