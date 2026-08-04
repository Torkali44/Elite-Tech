@extends('layouts.auth')
@section('title','نسيت كلمة المرور')
@section('content')
<h2 class="text-2xl font-black text-primary mb-2">استعادة كلمة المرور</h2>
<p class="text-sm text-tertiary mb-8">أدخل بريدك الإلكتروني وسنرسل لك رابط إعادة التعيين.</p>

@if (session('ok'))
    <div class="mb-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm px-4 py-3">{{ session('ok') }}</div>
@endif
@if ($errors->any())
    <div class="mb-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-sm px-4 py-3">{{ $errors->first() }}</div>
@endif

<form method="POST" action="{{ route('password.email') }}" class="space-y-4">
    @csrf
    <div>
        <label class="block text-sm font-semibold text-primary mb-1.5">البريد الإلكتروني</label>
        <input type="email" name="email" value="{{ old('email') }}" class="input" required>
    </div>
    <button type="submit" class="btn-primary">إرسال الرابط ←</button>
</form>
<p class="text-center text-sm text-tertiary mt-6"><a href="{{ route('login') }}" class="text-primary hover:underline">← العودة لتسجيل الدخول</a></p>
@endsection
