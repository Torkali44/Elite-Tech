@extends('layouts.app')
@section('title','مجتمع النخبة — المواهب')
@section('content')
<section class="bg-gradient-to-br from-primary to-primary-600 text-white py-14">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl sm:text-4xl font-black mb-2">أفكار ومواهب المجتمع</h1>
            <p class="text-white/80">استكشف مواهب النخبة التقنية، تواصل، أعرض أفكارك</p>
        </div>
        <div class="flex gap-3">
            <div class="bg-white/10 rounded-xl p-3 text-center min-w-[100px]">
                <div class="text-xs opacity-80">إجمالي الأعضاء</div>
                <div class="text-2xl font-black">{{ $members->total() }}</div>
            </div>
        </div>
    </div>
</section>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="grid lg:grid-cols-[260px_1fr] gap-6">
        {{-- Search / Filter --}}
        <aside class="card p-5 h-fit sticky top-20">
            <h3 class="font-bold text-primary mb-4 flex items-center gap-2">▽ البحث والتصفية</h3>
            <form method="GET" action="{{ route('community') }}" class="space-y-4 text-sm">
                <div>
                    <label class="block font-semibold text-primary mb-1.5">البحث باسم أو مهارة</label>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="ابحث عن اسم، مسمى..." class="input">
                </div>
                <button type="submit" class="btn-primary w-full">بحث</button>
                @if(request('q'))
                    <a href="{{ route('community') }}" class="btn-secondary w-full text-center block mt-2">إعادة تعيين</a>
                @endif
            </form>
        </aside>

        {{-- List --}}
        <div>
            @if($members->isEmpty())
                <div class="card p-12 text-center text-tertiary">
                    لا يوجد أعضاء مطابقون للبحث حالياً.
                </div>
            @else
                <div class="grid sm:grid-cols-2 gap-4">
                    @foreach($members as $m)
                    @php
                        $cvData = is_array($m->cv?->data) ? $m->cv->data : [];
                        $title = $m->title ?: ($cvData['title'] ?? $m->roleLabel());
                        $bio = $m->bio ?: ($cvData['summary'] ?? 'عضو في مجتمع إليت تك');
                        $skills = \App\Http\Controllers\ProfileController::asSkills($cvData['skills'] ?? []);
                        $projectTitle = $m->ideas->first()?->title ?? $title;
                    @endphp
                    <a href="{{ route('profile.show', $m->id) }}" class="card p-5 hover:shadow-card-hover transition">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-12 h-12 rounded-full bg-primary/10 grid place-items-center text-primary font-bold">
                                {{ mb_substr($m->name, 0, 1) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="font-bold text-primary truncate">{{ $m->name }}</div>
                                <div class="text-xs text-tertiary truncate">{{ $title }}</div>
                            </div>
                            @if($m->isKycApproved())
                                <span class="badge bg-emerald-50 text-emerald-600 text-[10px]">✓ Elite</span>
                            @endif
                        </div>
                        <h4 class="font-bold text-primary text-sm mb-2 truncate">{{ $projectTitle }}</h4>
                        <p class="text-xs text-tertiary line-clamp-2 mb-3">{{ $bio }}</p>
                        @if(count($skills))
                            <div class="flex flex-wrap gap-1.5 mb-3">
                                @foreach(array_slice($skills, 0, 4) as $s)
                                    <span class="badge bg-neutral text-primary text-[10px] border border-slate-200">{{ $s }}</span>
                                @endforeach
                            </div>
                        @endif
                        <div class="flex items-center justify-between text-xs text-tertiary border-t border-slate-100 pt-3">
                            <span class="text-primary font-bold">عرض الملف الشخصي ←</span>
                        </div>
                    </a>
                    @endforeach
                </div>

                {{-- Pagination --}}
                <div class="mt-8">
                    {{ $members->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
