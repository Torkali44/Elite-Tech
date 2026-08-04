@extends('layouts.dashboard')
@section('title','التواصل والرسائل')
@section('content')
<div class="mb-6 flex flex-wrap items-end justify-between gap-3">
    <div>
        <h1 class="text-2xl lg:text-3xl font-black text-primary mb-1">التواصل والرسائل</h1>
        <p class="text-tertiary text-sm">تواصل مع أصحاب الأفكار والمواهب الموثّقة داخل المنصة.</p>
    </div>
    <div class="flex bg-mist rounded-xl p-1 text-sm font-bold">
        <a href="{{ route('network.index', ['tab' => 'inbox']) }}"
           class="px-4 py-2 rounded-lg {{ $tab==='inbox' ? 'bg-white shadow-soft text-primary' : 'text-tertiary' }}">الوارد</a>
        <a href="{{ route('network.index', ['tab' => 'archive']) }}"
           class="px-4 py-2 rounded-lg {{ $tab==='archive' ? 'bg-white shadow-soft text-primary' : 'text-tertiary' }}">الأرشيف</a>
    </div>
</div>

<div class="grid lg:grid-cols-[300px_1fr_260px] gap-5">
    {{-- Threads --}}
    <div class="card overflow-hidden max-h-[70vh] overflow-y-auto">
        @forelse($threads as $t)
            <a href="{{ route('network.index', ['tab' => $tab, 'with' => $t['id']]) }}"
               class="block p-4 border-b border-mist last:border-0 hover:bg-neutral transition {{ $withId === $t['id'] ? 'bg-primary/5 border-r-4 border-r-primary' : '' }}">
                <div class="flex items-center justify-between gap-2 mb-1">
                    <span class="font-bold text-primary text-sm truncate">{{ $t['partner']->name }}</span>
                    <span class="text-[10px] text-tertiary shrink-0">{{ $t['time']->diffForHumans() }}</span>
                </div>
                <div class="text-[11px] text-tertiary mb-1">{{ $t['partner']->title ?: $t['partner']->roleLabel() }}</div>
                <p class="text-xs text-tertiary line-clamp-1">{{ $t['preview'] }}</p>
                @if($t['unread'] > 0)
                    <span class="badge bg-secondary text-white mt-2">{{ $t['unread'] }} جديدة</span>
                @endif
            </a>
        @empty
            <div class="p-8 text-center text-sm text-tertiary">لا محادثات في هذا القسم بعد. ابدأ محادثة من الدليل.</div>
        @endforelse
    </div>

    {{-- Conversation --}}
    <div class="card flex flex-col min-h-[420px] max-h-[70vh]">
        @if($activePartner)
            <div class="flex items-center justify-between border-b border-mist p-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-primary text-white grid place-items-center font-bold">
                        {{ mb_substr($activePartner->name, 0, 1) }}
                    </div>
                    <div>
                        <div class="font-bold text-primary">{{ $activePartner->name }}</div>
                        <div class="text-xs text-tertiary">{{ $activePartner->title ?: $activePartner->roleLabel() }}</div>
                    </div>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('profile.show', $activePartner->id) }}" class="btn-ghost text-xs">الملف</a>
                    @if($tab !== 'archive')
                    <form action="{{ route('network.archive', $activePartner->id) }}" method="POST">@csrf
                        <button class="btn-outline text-xs !py-1.5">أرشفة</button>
                    </form>
                    @endif
                </div>
            </div>

            <div class="flex-1 overflow-y-auto p-4 space-y-3 bg-neutral/40">
                @forelse($messages as $m)
                    @php $mine = $m->sender_id === auth()->id(); @endphp
                    <div class="flex {{ $mine ? 'justify-start' : 'justify-end' }}">
                        <div class="max-w-[80%] rounded-2xl px-4 py-3 text-sm leading-relaxed {{ $mine ? 'bg-primary text-white rounded-tr-sm' : 'bg-white border border-mist text-tertiary rounded-tl-sm' }}">
                            {{ $m->body }}
                            <div class="text-[10px] mt-1 {{ $mine ? 'text-white/60' : 'text-slate-400' }}">{{ $m->created_at->format('Y-m-d H:i') }}</div>
                        </div>
                    </div>
                @empty
                    <p class="text-center text-sm text-tertiary py-10">ابدأ المحادثة بأول رسالة.</p>
                @endforelse
            </div>

            <form action="{{ route('network.reply', $activePartner->id) }}" method="POST" class="p-4 border-t border-mist flex gap-2">
                @csrf
                <textarea name="body" rows="2" class="input flex-1" placeholder="اكتب رسالتك..." required></textarea>
                <button class="btn-secondary self-end !py-2">إرسال</button>
            </form>
        @else
            <div class="flex-1 grid place-items-center p-8 text-center text-tertiary text-sm">
                اختر محادثة أو ابدأ تواصلاً جديداً من الدليل على اليسار.
            </div>
        @endif
    </div>

    {{-- Directory / new message --}}
    <div class="card p-4 max-h-[70vh] overflow-y-auto">
        <h3 class="font-bold text-primary mb-3 text-sm">بدء محادثة جديدة</h3>
        <form action="{{ route('network.start') }}" method="POST" class="space-y-3 mb-4">
            @csrf
            <select name="recipient_id" class="input text-sm" required>
                <option value="">اختر عضواً...</option>
                @foreach($directory as $u)
                    <option value="{{ $u->id }}">{{ $u->name }} — {{ $u->roleLabel() }}</option>
                @endforeach
            </select>
            <textarea name="body" rows="3" class="input text-sm" placeholder="رسالتك الأولى..." required></textarea>
            <button class="btn-primary w-full text-sm !py-2">إرسال</button>
        </form>
        <p class="text-[11px] text-tertiary leading-relaxed">للتواصل العميق مع المواهب أو أصحاب الأفكار استخدم هذه الصفحة بعد تسجيل الدخول.</p>
    </div>
</div>
@endsection
