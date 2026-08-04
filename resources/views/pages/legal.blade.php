@extends('layouts.app')
@section('title', $title)

@section('content')
<section class="bg-primary text-white py-12">
    <div class="max-w-3xl mx-auto px-4">
        <h1 class="text-3xl sm:text-4xl font-extrabold mb-2">{{ $title }}</h1>
        <p class="text-white/70 text-sm">آخر تحديث: {{ $updated }}</p>
    </div>
</section>

<article class="max-w-3xl mx-auto px-4 py-12 space-y-6">
    @foreach($sections as $s)
        <div class="card p-6">
            <h2 class="font-black text-primary text-lg mb-2">{{ $s['h'] }}</h2>
            <p class="text-sm text-tertiary leading-relaxed">{{ $s['p'] }}</p>
        </div>
    @endforeach

    <div class="flex flex-wrap gap-3 text-sm pt-2">
        <a href="{{ route('terms') }}" class="{{ request()->routeIs('terms') ? 'text-primary font-bold' : 'text-tertiary hover:text-primary' }}">الشروط والأحكام</a>
        <span class="text-mist">|</span>
        <a href="{{ route('privacy') }}" class="{{ request()->routeIs('privacy') ? 'text-primary font-bold' : 'text-tertiary hover:text-primary' }}">سياسة الخصوصية</a>
        <span class="text-mist">|</span>
        <a href="{{ route('agreement') }}" class="{{ request()->routeIs('agreement') ? 'text-primary font-bold' : 'text-tertiary hover:text-primary' }}">اتفاقية الاستخدام</a>
    </div>
</article>
@endsection
