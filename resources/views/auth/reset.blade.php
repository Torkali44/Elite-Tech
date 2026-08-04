@extends('layouts.auth')
@section('title','إعادة تعيين كلمة المرور')
@section('content')
<h2 class="text-2xl font-black text-primary mb-2">إعادة تعيين كلمة المرور</h2>
<p class="text-sm text-tertiary mb-8">أدخل كلمة مرور جديدة لحسابك.</p>

@if ($errors->any())
    <div class="mb-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-sm px-4 py-3">{{ $errors->first() }}</div>
@endif

<form method="POST" action="{{ route('password.update') }}" class="space-y-4">
    @csrf
    <input type="hidden" name="token" value="{{ $token }}">
    <div>
        <label class="block text-sm font-semibold text-primary mb-1.5">البريد الإلكتروني</label>
        <input type="email" name="email" value="{{ old('email', $email) }}" class="input" required>
    </div>
    <div>
        <label class="block text-sm font-semibold text-primary mb-1.5">كلمة المرور الجديدة</label>
        <input type="password" name="password" class="input" required minlength="8" autocomplete="new-password">
    </div>
    <div>
        <label class="block text-sm font-semibold text-primary mb-1.5">تأكيد كلمة المرور</label>
        <input type="password" name="password_confirmation" class="input" required minlength="8" autocomplete="new-password">
    </div>
    <button type="submit" class="btn-primary">حفظ كلمة المرور</button>
</form>
@endsection
