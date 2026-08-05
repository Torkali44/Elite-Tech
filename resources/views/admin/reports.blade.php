@extends('layouts.admin')
@section('title', __('admin.nav.reports'))
@section('content')
<h1 class="text-2xl font-black text-primary mb-2">{{ app()->getLocale()==='ar' ? 'الإحصائيات والتحليلات' : 'Statistics & Analytics' }}</h1>
<p class="text-sm text-tertiary mb-6">{{ app()->getLocale()==='ar' ? 'نمو المجتمع، معدل التحويل، ومتوسط استجابة KYC' : 'Community growth, conversion rate, and average KYC response time.' }}</p>

<div class="grid md:grid-cols-4 gap-4 mb-8">
    <div class="card p-5">
        <div class="text-xs text-tertiary">{{ __('admin.stats.published') }}</div>
        <div class="text-3xl font-black text-primary">{{ $ideasPublished }}</div>
        <div class="text-xs text-tertiary">{{ app()->getLocale()==='ar' ? 'من أصل' : 'of' }} {{ $ideasTotal }}</div>
    </div>
    <div class="card p-5">
        <div class="text-xs text-tertiary">{{ __('admin.nav.implementations') }}</div>
        <div class="text-3xl font-black text-primary">{{ $implementStarted }}</div>
    </div>
    <div class="card p-5">
        <div class="text-xs text-tertiary">{{ __('admin.stats.conversion') }}</div>
        <div class="text-3xl font-black text-secondary">{{ $conversion }}%</div>
        <div class="text-xs text-tertiary">{{ __('admin.stats.conversion_desc') }}</div>
    </div>
    <div class="card p-5">
        <div class="text-xs text-tertiary">{{ __('admin.stats.avg_kyc_sla') }}</div>
        <div class="text-3xl font-black text-primary">{{ $avgKycHours !== null ? round($avgKycHours, 1) : '—' }}</div>
        <div class="text-xs text-tertiary">{{ app()->getLocale()==='ar' ? 'ساعة حتى المراجعة' : 'hours until review' }}</div>
    </div>
</div>

<div class="grid lg:grid-cols-2 gap-6">
    <div class="card p-6">
        <h3 class="font-bold text-primary mb-4">{{ app()->getLocale()==='ar' ? 'المستخدمون حسب المسار' : 'Users by Path' }}</h3>
        <div class="space-y-3">
            @forelse($byRole as $role => $total)
                <div class="flex items-center justify-between">
                    <span class="text-sm text-tertiary">{{ $role }}</span>
                    <span class="font-black text-primary">{{ $total }}</span>
                </div>
                <div class="h-2 bg-mist rounded-full overflow-hidden">
                    <div class="h-full bg-secondary rounded-full" style="width: {{ min(100, ($total / max(1, array_sum($byRole->toArray()))) * 100) }}%"></div>
                </div>
            @empty
                <p class="text-sm text-tertiary">{{ app()->getLocale()==='ar' ? 'لا بيانات.' : 'No data.' }}</p>
            @endforelse
        </div>
    </div>
    <div class="card p-6">
        <h3 class="font-bold text-primary mb-4">{{ app()->getLocale()==='ar' ? 'مستخدمون جدد (14 يوم)' : 'New Users (14 days)' }}</h3>
        <div class="space-y-2 max-h-72 overflow-y-auto">
            @forelse($newUsersDaily as $row)
                <div class="flex justify-between text-sm border-b border-mist py-2">
                    <span class="text-tertiary">{{ $row->d }}</span>
                    <span class="font-bold text-primary">{{ $row->c }}</span>
                </div>
            @empty
                <p class="text-sm text-tertiary">{{ app()->getLocale()==='ar' ? 'لا تسجيلات حديثة.' : 'No recent registrations.' }}</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
