@extends('layouts.dashboard')
@section('title','المسارات المهنية')
@section('content')
<h1 class="text-2xl lg:text-3xl font-black text-primary mb-2">المسارات المهنية</h1>
<p class="text-tertiary text-sm mb-6">مسارات موثقة لتفعيل دورك في مجتمع النخبة.</p>

<div class="grid md:grid-cols-2 lg:grid-cols-3 gap-5">
    @foreach($tracks as $t)
    <a href="{{ route('career-tracks.show',$t['slug']) }}" class="card p-6 hover:shadow-card-hover transition">
        <div class="w-12 h-12 rounded-xl bg-primary/10 text-primary grid place-items-center text-2xl mb-4">{{ $t['icon'] }}</div>
        <h3 class="font-bold text-primary mb-2">{{ $t['title'] }}</h3>
        <p class="text-sm text-tertiary mb-4">{{ $t['subtitle'] }}</p>
        <div class="flex items-center justify-between text-xs">
            <span class="badge {{ $t['statusColor'] }}">{{ $t['status'] }}</span>
            <span class="text-primary font-bold">التفاصيل ←</span>
        </div>
    </a>
    @endforeach
</div>
@endsection
