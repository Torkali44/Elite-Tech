@extends('layouts.auth')
@section('title','التحقق من الحساب')
@section('content')
<h2 class="text-2xl font-black text-primary mb-1">تأكيد البريد</h2>
<p class="text-sm text-tertiary mb-6">
    أدخل رمز التحقق المكوّن من 6 أرقام المرسل إلى:<br>
    <span class="text-primary font-bold">{{ auth()->user()->email ?? '' }}</span>
</p>

@if (session('ok'))
    <div class="mb-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm px-4 py-3">{{ session('ok') }}</div>
@endif
@if ($errors->any())
    <div class="mb-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-sm px-4 py-3">{{ $errors->first() }}</div>
@endif

<form action="{{ route('auth.verify') }}" method="POST" class="space-y-5">
    @csrf
    <div>
        <label class="block text-sm font-bold text-primary mb-1.5">رمز التحقق</label>
        <input type="text" name="code" inputmode="numeric" pattern="[0-9]{6}" maxlength="6"
               class="input text-center text-2xl font-black tracking-[0.4em]" placeholder="••••••" required autofocus>
    </div>
    <button type="submit" class="btn-primary w-full">تأكيد والمتابعة</button>
</form>

<p class="text-center text-sm text-tertiary mt-6">
    لم تستلم الرمز؟
    <a href="{{ route('auth.verify', ['resend' => 1]) }}" class="text-secondary font-bold hover:underline">إعادة إرسال الرمز</a>
</p>
@endsection
