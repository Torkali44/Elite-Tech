@props(['idea'])

@php
    $isModel = $idea instanceof \App\Models\Idea;
    $id = $isModel ? $idea->id : ($idea['id'] ?? 0);
    $title = $isModel ? $idea->title : ($idea['title'] ?? '');
    $desc = $isModel ? $idea->shortDesc(110) : ($idea['desc'] ?? '');
    $category = $isModel ? $idea->category : ($idea['category'] ?? '');
    $author = $isModel ? ($idea->user->name ?? 'عضو') : ($idea['author'] ?? '');
    $likes = $isModel ? $idea->likes_count : ($idea['likes'] ?? 0);
    $comments = $isModel ? ($idea->comments_count ?? $idea->comments()->count()) : 0;
    $techs = $isModel ? (array) ($idea->technologies ?? []) : [];
    $featured = $isModel ? ($idea->likes_count >= 200) : ($idea['featured'] ?? false);
    $parent = $isModel ? $idea->parent : null;
    $status = $isModel ? $idea->status : 'published';
    $isFavorited = $isModel && auth()->check()
        ? (bool) ($idea->is_favorited ?? $idea->isFavoritedBy(auth()->user()))
        : false;
@endphp

<div class="card p-5 flex flex-col {{ $featured ? 'bg-primary text-white border-primary' : 'bg-white' }}">
    <div class="flex items-center justify-between gap-2 mb-3">
        <div class="flex items-center gap-2 min-w-0">
            <div class="w-7 h-7 rounded-full {{ $featured ? 'bg-white/20 text-white' : 'bg-primary/10 text-primary' }} grid place-items-center text-xs font-extrabold shrink-0">
                {{ mb_substr($author, 0, 1) }}
            </div>
            <span class="text-xs font-semibold truncate {{ $featured ? 'text-white/80' : 'text-tertiary' }}">{{ $author }}</span>
        </div>
        <span class="badge shrink-0 {{ $featured ? 'bg-white/15 text-white' : 'bg-neutral text-tertiary' }}">{{ $category }}</span>
    </div>

    @if($parent)
        <div class="text-[11px] mb-2 px-2 py-1 rounded font-semibold {{ $featured ? 'bg-white/10 text-white/90' : 'bg-secondary/10 text-secondary' }}">
            مستلهمة من «{{ $parent->title }}»
        </div>
    @endif

    @if($status !== 'published')
        <span class="badge mb-2 {{ $status === 'pending' ? 'bg-amber-50 text-amber-700' : 'bg-mist text-tertiary' }}">
            {{ ['draft'=>'مسودة','pending'=>'قيد المراجعة','archived'=>'مؤرشفة'][$status] ?? $status }}
        </span>
    @endif

    <h3 class="font-extrabold text-base mb-2 leading-snug {{ $featured ? 'text-white' : 'text-primary' }} line-clamp-2">
        <a href="{{ route('ideas.show', $id) }}" class="hover:text-secondary">{{ $title }}</a>
    </h3>

    <p class="text-sm {{ $featured ? 'text-white/75' : 'text-tertiary' }} mb-3 line-clamp-2 leading-relaxed flex-1">
        {{ $desc }}
    </p>

    @if(count($techs))
        <div class="flex flex-wrap gap-1.5 mb-4">
            @foreach(array_slice($techs, 0, 3) as $tech)
                <span class="badge text-[10px] {{ $featured ? 'bg-white/15 text-white' : 'bg-mist text-tertiary' }}">{{ $tech }}</span>
            @endforeach
        </div>
    @endif

    <div class="flex items-center justify-between gap-2 pt-3 border-t {{ $featured ? 'border-white/15' : 'border-mist' }} mt-auto">
        <a href="{{ route('ideas.show', $id) }}" class="text-xs font-bold {{ $featured ? 'text-secondary' : 'text-secondary' }} hover:underline">
            عرض التفاصيل
        </a>

        <div class="flex items-center gap-3 text-xs font-semibold {{ $featured ? 'text-white/70' : 'text-tertiary' }}">
            <span title="إعجابات">▲ {{ number_format($likes) }}</span>
            <span title="تعليقات">💬 {{ number_format($comments) }}</span>

            @auth
                @if($status === 'published')
                    <form method="POST" action="{{ route('ideas.favorite', $id) }}" class="inline">
                        @csrf
                        <button type="submit"
                                title="{{ $isFavorited ? 'إزالة من المفضلة' : 'إضافة للمفضلة' }}"
                                class="transition {{ $isFavorited ? 'text-secondary' : ($featured ? 'text-white/70 hover:text-secondary' : 'text-tertiary hover:text-secondary') }}">
                            {{ $isFavorited ? '★' : '☆' }}
                        </button>
                    </form>
                @endif
            @else
                <button type="button" title="المفضلة"
                        class="{{ $featured ? 'text-white/70' : 'text-tertiary' }}"
                        onclick="window.dispatchEvent(new CustomEvent('open-gate',{detail:'احفظ الأفكار في المفضلة بعد تسجيل الدخول.'}))"
                        @click="gateOpen=true; gateMsg='احفظ الأفكار في المفضلة بعد تسجيل الدخول.'">
                    ☆
                </button>
            @endauth
        </div>
    </div>
</div>
