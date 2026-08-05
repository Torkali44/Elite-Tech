@extends('layouts.admin')
@section('title', __('admin.overview'))
@section('content')
<div class="mb-6">
    <h1 class="text-2xl lg:text-3xl font-black text-primary mb-1">{{ __('admin.overview') }}</h1>
    <p class="text-tertiary text-sm">{{ __('admin.overview_desc') }}</p>
</div>

<div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-8">
    <div class="card p-5">
        <div class="text-xs text-tertiary mb-1">{{ __('admin.stats.users') }}</div>
        <div class="text-3xl font-black text-primary">{{ $stats['users'] }}</div>
    </div>
    <div class="card p-5">
        <div class="text-xs text-tertiary mb-1">{{ __('admin.stats.ideas') }}</div>
        <div class="text-3xl font-black text-primary">{{ $stats['ideas'] }}</div>
    </div>
    <div class="card p-5">
        <div class="text-xs text-tertiary mb-1">{{ __('admin.stats.published') }}</div>
        <div class="text-3xl font-black text-primary">{{ $stats['published'] }}</div>
    </div>
    <div class="card p-5 border-{{ app()->getLocale() === 'ar' ? 'r' : 'l' }}-4 border-indigo-500">
        <div class="text-xs font-bold text-indigo-600 mb-1">{{ __('admin.stats.conversion') }}</div>
        <div class="text-3xl font-black text-indigo-700">{{ $stats['conversion'] }}%</div>
        <div class="text-[10px] text-tertiary mt-1">{{ __('admin.stats.conversion_desc') }}</div>
    </div>
    <div class="card p-5 border-{{ app()->getLocale() === 'ar' ? 'r' : 'l' }}-4 border-emerald-500">
        <div class="text-xs font-bold text-emerald-600 mb-1">{{ __('admin.stats.avg_kyc_sla') }}</div>
        <div class="text-2xl font-black text-emerald-700">{{ $stats['avg_kyc_sla'] }}</div>
        <div class="text-[10px] text-tertiary mt-1">{{ __('admin.stats.avg_kyc_desc') }}</div>
    </div>
</div>

<div class="grid lg:grid-cols-2 gap-6">
    <div class="card p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold text-primary">{{ __('admin.recent_users') }}</h3>
            <a href="{{ route('admin.users') }}" class="text-xs text-secondary font-bold">{{ __('admin.view_all') }}</a>
        </div>
        <div class="space-y-3">
            @forelse($recentUsers as $u)
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-primary/10 grid place-items-center text-primary font-bold text-sm">{{ mb_substr($u->name,0,1) }}</div>
                <div class="flex-1 min-w-0">
                    <div class="font-bold text-primary text-sm truncate">{{ $u->name }}</div>
                    <div class="text-xs text-tertiary truncate">{{ $u->email }} · {{ $u->roleLabel() }}</div>
                </div>
                <span class="badge bg-mist text-tertiary">{{ $u->kyc_status }}</span>
            </div>
            @empty
                <p class="text-sm text-tertiary">{{ __('admin.no_users') }}</p>
            @endforelse
        </div>
    </div>

    <div class="card p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold text-primary">{{ __('admin.pending_kyc') }}</h3>
            <a href="{{ route('admin.verifications') }}" class="text-xs text-secondary font-bold">{{ __('admin.view_all') }}</a>
        </div>
        <div class="space-y-3">
            @forelse($pendingKyc as $v)
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-secondary/10 grid place-items-center text-secondary font-bold">KYC</div>
                <div class="flex-1 min-w-0">
                    <div class="font-bold text-primary text-sm">{{ $v->user->name ?? '—' }}</div>
                    <div class="text-xs text-tertiary">{{ $v->purposeLabel() }} · {{ $v->created_at->diffForHumans() }}</div>
                </div>
                <a href="{{ route('admin.verifications') }}" class="text-xs bg-primary text-white px-3 py-1.5 rounded-lg font-bold">{{ __('admin.review') }}</a>
            </div>
            @empty
                <p class="text-sm text-tertiary">{{ __('admin.no_pending_kyc') }}</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
