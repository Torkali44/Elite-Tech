@extends('layouts.app')
@section('title','الموجهون')
@section('content')
<div class="max-w-7xl mx-auto px-4 py-10">
    <h1 class="text-3xl font-black text-primary mb-2">الموجهون (Mentors)</h1>
    <p class="text-tertiary mb-8">تواصل مع خبراء متخصصين في مجالات مختلفة لتوجيه مسيرتك المهنية.</p>
    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-5">
        @foreach($mentors as $m)
        <div class="card p-6 text-center">
            <div class="w-20 h-20 rounded-full bg-primary/10 grid place-items-center mx-auto mb-4 text-primary text-2xl font-black">{{ mb_substr($m['name'],0,1) }}</div>
            <h3 class="font-bold text-primary">{{ $m['name'] }}</h3>
            <p class="text-xs text-tertiary mb-3">{{ $m['expertise'] }}</p>
            <div class="flex flex-wrap gap-1.5 justify-center mb-4">
                @foreach($m['tags'] as $t)<span class="badge bg-neutral text-primary text-[10px]">{{ $t }}</span>@endforeach
            </div>
            <button class="btn-outline w-full text-sm">طلب جلسة إرشاد</button>
        </div>
        @endforeach
    </div>
</div>
@endsection

@push('head')
<style>.btn-outline { @apply inline-flex items-center justify-center gap-2 border border-primary text-primary font-semibold px-4 py-2 rounded-lg hover:bg-primary hover:text-white; }</style>
@endpush
