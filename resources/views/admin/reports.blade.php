@extends('layouts.admin')
@section('title','الإحصائيات')
@section('content')
<h1 class="text-2xl font-black text-primary mb-2">الإحصائيات والتحليلات</h1>
<p class="text-sm text-tertiary mb-6">نمو المجتمع، معدل التحويل، ومتوسط استجابة KYC</p>

<div class="grid md:grid-cols-4 gap-4 mb-8">
    <div class="card p-5">
        <div class="text-xs text-tertiary">أفكار منشورة</div>
        <div class="text-3xl font-black text-primary">{{ $ideasPublished }}</div>
        <div class="text-xs text-tertiary">من أصل {{ $ideasTotal }}</div>
    </div>
    <div class="card p-5">
        <div class="text-xs text-tertiary">طلبات تنفيذ</div>
        <div class="text-3xl font-black text-primary">{{ $implementStarted }}</div>
    </div>
    <div class="card p-5">
        <div class="text-xs text-tertiary">Conversion Rate</div>
        <div class="text-3xl font-black text-secondary">{{ $conversion }}%</div>
        <div class="text-xs text-tertiary">تنفيذ ÷ أفكار منشورة</div>
    </div>
    <div class="card p-5">
        <div class="text-xs text-tertiary">متوسط SLA لـ KYC</div>
        <div class="text-3xl font-black text-primary">{{ $avgKycHours !== null ? round($avgKycHours, 1) : '—' }}</div>
        <div class="text-xs text-tertiary">ساعة حتى المراجعة</div>
    </div>
</div>

<div class="grid lg:grid-cols-2 gap-6">
    <div class="card p-6">
        <h3 class="font-bold text-primary mb-4">المستخدمون حسب المسار</h3>
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
                <p class="text-sm text-tertiary">لا بيانات.</p>
            @endforelse
        </div>
    </div>
    <div class="card p-6">
        <h3 class="font-bold text-primary mb-4">مستخدمون جدد (14 يوم)</h3>
        <div class="space-y-2 max-h-72 overflow-y-auto">
            @forelse($newUsersDaily as $row)
                <div class="flex justify-between text-sm border-b border-mist py-2">
                    <span class="text-tertiary">{{ $row->d }}</span>
                    <span class="font-bold text-primary">{{ $row->c }}</span>
                </div>
            @empty
                <p class="text-sm text-tertiary">لا تسجيلات حديثة.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
