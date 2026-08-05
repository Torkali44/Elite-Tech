@extends('layouts.dashboard')
@section('title', __('general.impl_req_title'))
@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-black text-primary mb-1">{{ __('general.impl_req_title') }}</h1>
    <p class="text-sm text-tertiary">{{ __('general.impl_req_subtitle') }}</p>
</div>

<div class="space-y-4">
@forelse($requests as $r)
<div class="card p-5">
    <div class="flex flex-wrap justify-between gap-3">
        <div>
            <div class="font-bold text-primary">{{ $r->idea->title ?? '—' }}</div>
            <div class="text-sm text-tertiary mt-1">
                {{ __('general.from_label') }}: <b>{{ $r->user->name ?? '—' }}</b>
                · {{ $r->via === 'elite_tech' ? __('general.via_elite') : __('general.via_direct') }}
                · {{ $r->created_at->diffForHumans() }}
            </div>
            @if($r->note)<p class="text-xs text-tertiary mt-2 bg-mist rounded-lg px-3 py-2">{{ $r->note }}</p>@endif
        </div>
        <span class="badge {{ $r->status==='pending'?'bg-amber-50 text-amber-700':($r->status==='approved'?'bg-emerald-50 text-emerald-700':'bg-rose-50 text-rose-700') }}">{{ $r->status }}</span>
    </div>
    @if($r->status === 'pending')
    <div class="flex flex-wrap gap-2 mt-4 pt-3 border-t border-mist">
        <form method="POST" action="{{ route('dashboard.implementRespond', $r->id) }}">@csrf
            <input type="hidden" name="action" value="approved">
            <button class="btn-primary text-sm !py-2">{{ __('general.accept_btn') }}</button>
        </form>
        <form method="POST" action="{{ route('dashboard.implementRespond', $r->id) }}" class="flex gap-2 flex-1 min-w-[200px]">@csrf
            <input type="hidden" name="action" value="rejected">
            <input name="note" class="input !py-2 text-sm" placeholder="{{ __('general.rejection_reason_placeholder') }}">
            <button class="btn-outline text-sm !py-2 !border-rose-300 !text-rose-600">{{ __('general.reject_btn') }}</button>
        </form>
        <a href="{{ route('network.index', ['with' => $r->user_id]) }}" class="btn-ghost text-sm">{{ __('general.message_btn') }}</a>
    </div>
    @endif
</div>
@empty
<div class="card p-10 text-center text-tertiary">{{ __('general.no_impl_requests') }}</div>
@endforelse
</div>
<div class="mt-6">{{ $requests->links() }}</div>
@endsection
