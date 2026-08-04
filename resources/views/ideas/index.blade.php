@extends('layouts.app')
@section('title', 'أفكار المجتمع — Elite Tech Community')
@section('description', 'استكشف أفكار المجتمع التقنية، صفِّ النتائج، واحفظ المفضلة.')

@section('content')
@php
    $tab = $tab ?? request('tab', 'all');
    $sort = $sort ?? request('sort', 'newest');
@endphp

<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-10">
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-6">
        <div>
            <h1 class="text-3xl font-extrabold text-primary mb-2">أفكار المجتمع</h1>
            <p class="text-sm text-tertiary max-w-2xl leading-relaxed">
                استكشف أحدث الأفكار والابتكارات. صفِّ النتائج، احفظ المفضلة، وشارك فكرتك.
            </p>
        </div>
        @auth
            <a href="{{ route('ideas.create') }}" class="btn-secondary text-sm !py-2.5 !px-5 shrink-0">+ أضف فكرة جديدة</a>
        @else
            <button type="button" class="btn-secondary text-sm !py-2.5 !px-5 shrink-0"
                    @click="gateOpen=true; gateMsg='لنشر فكرة تحتاج حساباً، ومسار صاحب الفكرة يتطلب KYC قبل النشر.'">
                + أضف فكرة جديدة
            </button>
        @endauth
    </div>

    {{-- Tabs --}}
    <div class="flex flex-wrap items-center gap-1 border-b border-mist mb-6 text-sm font-semibold">
        @foreach([
            'all' => 'الكل',
            'community' => 'أفكار المجتمع',
            'my' => 'أفكاري',
            'favorites' => 'المفضلة',
        ] as $key => $label)
            @if(in_array($key, ['my', 'favorites'], true) && !auth()->check())
                <button type="button"
                        class="px-4 py-2.5 border-b-2 border-transparent text-tertiary hover:text-primary"
                        @click="gateOpen=true; gateMsg='سجّل الدخول لعرض {{ $label }}.'">
                    {{ $label }}
                </button>
            @else
                <a href="{{ route('ideas.index', array_filter(['tab' => $key === 'all' ? null : $key, 'category' => request('category'), 'q' => request('q'), 'sort' => $sort])) }}"
                   class="px-4 py-2.5 border-b-2 transition {{ $tab === $key || ($key === 'all' && !in_array($tab, ['community','my','favorites'], true)) ? 'border-secondary text-primary font-extrabold' : 'border-transparent text-tertiary hover:text-primary' }}">
                    {{ $label }}
                </a>
            @endif
        @endforeach
    </div>

    {{-- Stats --}}
    <div class="grid sm:grid-cols-3 gap-4 mb-8">
        <div class="card p-4 flex items-center justify-between">
            <div>
                <div class="text-xs text-tertiary mb-0.5">إجمالي أفكار المجتمع</div>
                <div class="text-2xl font-extrabold text-primary">{{ number_format($stats['total'] ?? 0) }}</div>
            </div>
            <div class="w-10 h-10 rounded-lg bg-primary/10 text-primary grid place-items-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
        </div>
        <div class="card p-4 flex items-center justify-between">
            <div>
                <div class="text-xs text-tertiary mb-0.5">المساهمون النشطون</div>
                <div class="text-2xl font-extrabold text-primary">{{ number_format($stats['contributors'] ?? 0) }}</div>
            </div>
            <div class="w-10 h-10 rounded-lg bg-secondary/15 text-secondary grid place-items-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
        </div>
        <div class="card p-4 flex items-center justify-between">
            <div>
                <div class="text-xs text-tertiary mb-0.5">الأكثر بحثاً</div>
                <div class="text-lg font-extrabold text-primary">{{ $stats['top_category'] ?? '—' }}</div>
            </div>
            <div class="w-10 h-10 rounded-lg bg-neutral text-tertiary grid place-items-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
            </div>
        </div>
    </div>

    <div class="grid lg:grid-cols-[260px_1fr] gap-6 items-start">
        {{-- Filter sidebar --}}
        <aside class="card p-5 sticky top-20">
            <div class="flex items-center gap-2 mb-4">
                <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                <h3 class="font-extrabold text-primary text-sm">تصفية النتائج</h3>
            </div>

            <form method="GET" action="{{ route('ideas.index') }}" class="space-y-4">
                @if($tab && $tab !== 'all')
                    <input type="hidden" name="tab" value="{{ $tab }}">
                @endif

                <div>
                    <label class="block text-xs font-bold text-tertiary mb-1.5">بحث</label>
                    <input type="text" name="q" value="{{ request('q') }}" class="input !py-2 text-sm" placeholder="كلمة مفتاحية...">
                </div>

                <div>
                    <label class="block text-xs font-bold text-tertiary mb-1.5">الفئة</label>
                    <select name="category" class="input !py-2 text-sm">
                        <option value="">كل الفئات</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}" @selected(request('category') === $cat)>{{ $cat }}</option>
                        @endforeach
                        @foreach(['الذكاء الاصطناعي','تطوير الويب','تطبيقات المحمول','الأمن السيبراني','البلوكشين'] as $fallback)
                            @if(!$categories->contains($fallback))
                                <option value="{{ $fallback }}" @selected(request('category') === $fallback)>{{ $fallback }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>

                <div>
                    <div class="text-xs font-bold text-tertiary mb-2">ترتيب حسب</div>
                    <label class="flex items-center gap-2 text-sm text-primary mb-2 cursor-pointer">
                        <input type="radio" name="sort" value="newest" class="accent-primary" @checked($sort === 'newest')>
                        الأحدث
                    </label>
                    <label class="flex items-center gap-2 text-sm text-primary cursor-pointer">
                        <input type="radio" name="sort" value="popular" class="accent-primary" @checked($sort === 'popular')>
                        الأكثر شهرة
                    </label>
                </div>

                <button type="submit" class="btn-primary w-full text-sm !py-2.5">تطبيق التصفية</button>
                <a href="{{ route('ideas.index', $tab && $tab !== 'all' ? ['tab' => $tab] : []) }}"
                   class="btn-ghost w-full text-sm text-center block !py-2">إعادة تعيين</a>
            </form>
        </aside>

        {{-- Ideas grid --}}
        <div>
            @if($ideas->isEmpty())
                <div class="card p-12 text-center">
                    <h3 class="font-extrabold text-primary text-lg mb-2">لا توجد أفكار مطابقة</h3>
                    <p class="text-sm text-tertiary mb-4">جرّب تغيير الفلاتر أو التبويب.</p>
                    <a href="{{ route('ideas.index') }}" class="btn-outline text-sm">إعادة ضبط</a>
                </div>
            @else
                <div class="grid md:grid-cols-2 xl:grid-cols-3 gap-5">
                    @foreach($ideas as $idea)
                        <x-idea-card :idea="$idea" />
                    @endforeach
                </div>
                <div class="mt-8 flex justify-center">
                    {{ $ideas->links() }}
                </div>
            @endif
        </div>
    </div>
</section>
@endsection
