<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'البريد الإلكتروني أو كلمة المرور غير صحيحة.']);
        }

        $request->session()->regenerate();
        $user = Auth::user();

        if ($user->is_suspended) {
            Auth::logout();

            return back()->withErrors(['email' => 'هذا الحساب معلّق. تواصل مع الإدارة.']);
        }

        if ($user->role === 'admin' || $user->hasRole('admin')) {
            $request->session()->put('is_admin', true);

            return redirect()->route('admin.dashboard');
        }

        if (empty($user->roles)) {
            return redirect()->route('auth.path');
        }

        return redirect()->intended(route('dashboard'));
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', PasswordRule::min(8)],
            'terms' => ['accepted'],
        ], [
            'terms.accepted' => 'يجب الموافقة على الشروط للمتابعة.',
            'email.unique' => 'هذا البريد مسجّل بالفعل.',
            'password.confirmed' => 'كلمتا المرور غير متطابقتين.',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'role' => 'idea_seeker',
            'kyc_status' => 'none',
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('auth.verify');
    }

    public function showVerify(Request $request)
    {
        $user = $request->user();

        if ($user->email_verified_at) {
            return redirect()->route('auth.path');
        }

        if ($request->boolean('resend') || ! $request->session()->has('email_otp_hash')) {
            $this->issueEmailOtp($request);
        }

        return view('auth.verify');
    }

    public function verify(Request $request)
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'size:6'],
        ], [
            'code.size' => 'رمز التحقق يجب أن يكون 6 أرقام.',
        ]);

        $hash = $request->session()->get('email_otp_hash');
        $expires = (int) $request->session()->get('email_otp_expires', 0);

        if (! $hash || $expires < now()->timestamp) {
            return back()->withErrors(['code' => 'انتهت صلاحية الرمز. اطلب رمزاً جديداً.']);
        }

        if (! Hash::check($data['code'], $hash)) {
            return back()->withErrors(['code' => 'رمز التحقق غير صحيح.']);
        }

        $request->session()->forget(['email_otp_hash', 'email_otp_expires']);
        $request->user()->forceFill(['email_verified_at' => now()])->save();

        return redirect()->route('auth.path')->with('ok', 'تم تأكيد البريد بنجاح.');
    }

    public function showPathSelection()
    {
        return view('auth.path-selection');
    }

    public function savePath(Request $request)
    {
        $data = $request->validate([
            'roles' => ['required', 'array', 'min:1'],
            'roles.*' => ['in:idea_owner,idea_seeker,developer'],
            'wants_jobs_forum' => ['nullable', 'boolean'],
        ], [
            'roles.required' => 'يرجى اختيار مسار واحد على الأقل.',
        ]);

        $user = $request->user();
        $roles = $data['roles'];
        $wantsJobs = $request->boolean('wants_jobs_forum') && in_array('developer', $roles, true);

        // باحث عن فكرة → تفعيل فوري للـ KYC
        if (in_array('idea_seeker', $roles, true)) {
            $user->forceFill(['kyc_status' => 'approved'])->save();
        }

        $user->forceFill([
            'roles' => $roles,
            'role' => $roles[0],
            'wants_jobs_forum' => $wantsJobs,
        ])->save();

        // PRD: صاحب فكرة → KYC قبل النشر
        if (in_array('idea_owner', $roles, true) && ! $user->isKycApproved()) {
            return redirect()
                ->route('verification.kyc', ['purpose' => 'publish_idea'])
                ->with('ok', 'مسار صاحب الفكرة يتطلب KYC قبل النشر. أكمل التحقق الآن.');
        }

        // PRD: باحث عن عمل → KYC فقط عند الانضمام لمنتدى التوظيف
        if ($wantsJobs && ! $user->isKycApproved()) {
            return redirect()
                ->route('verification.kyc', ['purpose' => 'jobs_forum'])
                ->with('ok', 'للانضمام لمنتدى التوظيف أكمل التحقق من الهوية.');
        }

        if (in_array('developer', $roles, true) && ! $wantsJobs) {
            return redirect()
                ->route('profile.cv')
                ->with('ok', 'يمكنك بناء سيرتك واستخراج PDF بحرية دون KYC.');
        }

        // PRD: باحث عن أفكار → تصفح حر؛ KYC عند الرغبة في التنفيذ فقط
        return redirect()->route('dashboard')->with('ok', 'تم حفظ مسارك بنجاح.');
    }

    public function showForgot()
    {
        return view('auth.forgot');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => ['required', 'email']]);

        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? back()->with('ok', 'إن وُجد الحساب، تم إرسال رابط إعادة التعيين إلى بريدك.')
            : back()->with('ok', 'إن وُجد الحساب، تم إرسال رابط إعادة التعيين إلى بريدك.');
    }

    public function showReset(Request $request, string $token)
    {
        return view('auth.reset', [
            'token' => $token,
            'email' => $request->query('email', ''),
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', PasswordRule::min(8)],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => $password,
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('ok', 'تم تحديث كلمة المرور. يمكنك تسجيل الدخول.')
            : back()->withErrors(['email' => 'رابط إعادة التعيين غير صالح أو منتهٍ.']);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    private function issueEmailOtp(Request $request): void
    {
        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $request->session()->put('email_otp_hash', Hash::make($code));
        $request->session()->put('email_otp_expires', now()->addMinutes(15)->timestamp);

        $email = $request->user()->email;

        Mail::raw(
            "رمز التحقق لحسابك في Elite Tech Community: {$code}\nصالح لمدة 15 دقيقة.",
            function ($message) use ($email) {
                $message->to($email)->subject('رمز التحقق — Elite Tech Community');
            }
        );
    }
}
