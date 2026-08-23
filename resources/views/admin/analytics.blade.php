@extends('layouts.admin')
@section('title', 'تحليل الزوار والأخطاء')
@section('content')
<div class="mb-6 flex flex-wrap items-end justify-between gap-3">
    <div>
        <h1 class="text-2xl font-black text-primary mb-1">تحليل الزوار والأخطاء التقنية</h1>
        <p class="text-sm text-tertiary">مراقبة حية لنشاط الزوار ورصد الأخطاء التي تواجههم تلقائياً.</p>
    </div>
</div>

<!-- Stats -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
    <div class="card p-5">
        <div class="text-xs text-tertiary mb-1">زيارات اليوم</div>
        <div class="text-3xl font-black text-primary">{{ $stats['visitors_today'] }}</div>
    </div>
    <div class="card p-5">
        <div class="text-xs text-tertiary mb-1">زوار فريدون (اليوم)</div>
        <div class="text-3xl font-black text-emerald-600">{{ $stats['unique_visitors_today'] }}</div>
    </div>
    <div class="card p-5">
        <div class="text-xs text-tertiary mb-1">أخطاء برمجية اليوم</div>
        <div class="text-3xl font-black text-rose-600">{{ $stats['errors_today'] }}</div>
    </div>
    <div class="card p-5">
        <div class="text-xs text-tertiary mb-1">إجمالي الأخطاء</div>
        <div class="text-3xl font-black text-amber-600">{{ $stats['total_errors'] }}</div>
    </div>
</div>

<div class="grid lg:grid-cols-2 gap-8 mb-8">
    <!-- Top Pages -->
    <div class="card p-6">
        <h3 class="font-bold text-primary mb-4 text-lg">أكثر الصفحات زيارة (آخر 7 أيام)</h3>
        <div class="space-y-3">
            @foreach($topPages as $page)
            <div class="flex items-center justify-between border-b border-mist pb-2 last:border-0">
                <a href="{{ $page->url }}" target="_blank" class="text-sm text-secondary hover:underline truncate max-w-[80%]" dir="ltr">{{ $page->url }}</a>
                <span class="badge bg-mist text-primary">{{ $page->total }}</span>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Recent Visitors Live -->
    <div class="card p-6 overflow-hidden flex flex-col h-full max-h-[400px]">
        <h3 class="font-bold text-primary mb-4 text-lg">تحركات الزوار المباشرة</h3>
        <div class="overflow-y-auto pr-2 space-y-3 flex-1">
            @forelse($recentVisitors as $log)
            <div class="border-b border-mist pb-2 last:border-0 text-sm">
                <div class="flex justify-between mb-1">
                    <span class="font-bold text-primary">{{ $log->user->name ?? 'زائر غير مسجل' }}</span>
                    <span class="text-xs text-tertiary">{{ $log->created_at->diffForHumans() }}</span>
                </div>
                <div class="text-xs text-tertiary mb-1" dir="ltr">{{ $log->url }}</div>
                <div class="flex items-center justify-between mt-1 text-[10px] text-slate-400">
                    <span class="badge {{ $log->method==='GET' ? 'bg-blue-50 text-blue-600' : 'bg-amber-50 text-amber-600' }} py-0 px-1">{{ $log->method }}</span>
                    <span>IP: {{ $log->ip_address }}</span>
                </div>
            </div>
            @empty
            <div class="text-center text-tertiary text-sm py-4">لا توجد زيارات بعد.</div>
            @endforelse
        </div>
    </div>
</div>

<!-- Errors Log -->
<div class="card p-6">
    <h3 class="font-bold text-rose-600 mb-4 text-lg">سجل الأخطاء التقنية (Exceptions)</h3>
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-right">
            <thead>
                <tr class="bg-mist/50 text-tertiary border-b border-mist">
                    <th class="py-3 px-4 font-bold">الوقت</th>
                    <th class="py-3 px-4 font-bold">المستخدم</th>
                    <th class="py-3 px-4 font-bold">الرابط / الصفحة</th>
                    <th class="py-3 px-4 font-bold">رسالة الخطأ</th>
                    <th class="py-3 px-4 font-bold text-center">التفاصيل</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-mist">
                @forelse($recentErrors as $err)
                <tr x-data="{ expanded: false }" class="hover:bg-slate-50 transition">
                    <td class="py-3 px-4 text-xs whitespace-nowrap">{{ $err->created_at->format('Y-m-d H:i') }}</td>
                    <td class="py-3 px-4 font-bold text-primary">{{ $err->user->name ?? 'زائر' }}</td>
                    <td class="py-3 px-4" dir="ltr">
                        <span class="truncate block max-w-[200px] text-xs text-tertiary" title="{{ $err->url }}">{{ $err->url }}</span>
                    </td>
                    <td class="py-3 px-4 text-rose-600 font-semibold text-xs">
                        <span class="line-clamp-2" title="{{ $err->message }}">{{ $err->message }}</span>
                    </td>
                    <td class="py-3 px-4 text-center">
                        <button @click="expanded = !expanded" class="btn-outline text-xs !py-1 !px-2">التفاصيل</button>
                    </td>
                </tr>
                <tr x-show="expanded" x-cloak style="display: none;">
                    <td colspan="5" class="p-4 bg-slate-900 text-slate-300 font-mono text-[11px] leading-relaxed">
                        <div class="mb-2 font-bold text-rose-400">{{ $err->message }}</div>
                        <div class="overflow-x-auto max-h-64 overflow-y-auto whitespace-pre">{{ $err->stack_trace }}</div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-10 text-center text-tertiary font-bold">النظام مستقر، لا توجد أخطاء مرصودة حالياً.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $recentErrors->links() }}</div>
</div>
@endsection
