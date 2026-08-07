<?php

namespace App\Http\Controllers;

use App\Mail\OtpMail;
use App\Models\User;
use App\Support\AdminAuth;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;

class AuthController extends Controller
{
    // =========================================================================
    // LOGIN
    // =========================================================================

    public function showLogin(): \Illuminate\View\View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $email    = strtolower(trim($credentials['email']));
        $password = $credentials['password'];

        // ── Admin path ──────────────────────────────────────────────────────
        if (AdminAuth::isAdminEmail($email)) {
            if (! AdminAuth::passwordMatches($password)) {
                return back()
                    ->withInput($request->only('email'))
                    ->withErrors(['email' => __('auth.invalid_credentials')]);
            }

            $adminUser = AdminAuth::user();
            if ($adminUser?->is_suspended) {
                return back()->withErrors(['email' => __('auth.account_suspended')]);
            }

            if ($adminUser) {
                Auth::login($adminUser, $request->boolean('remember'));
            }

            $request->session()->regenerate();
            $request->session()->put('is_admin', true);

            return redirect()->route('admin.dashboard');
        }

        // ── Regular user path ────────────────────────────────────────────────
        if (! Auth::attempt(['email' => $email, 'password' => $password], $request->boolean('remember'))) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => __('auth.invalid_credentials')]);
        }

        $request->session()->regenerate();
        $user = Auth::user();

        // Suspended check (belt-and-suspenders; middleware also handles this)
        if ($user->is_suspended) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return back()->withErrors(['email' => __('auth.account_suspended')]);
        }

        // Unverified email → issue OTP and redirect to verify page
        if (! $user->email_verified_at) {
            $this->issueEmailOtpForUser($request, $user);
            return redirect()->route('auth.verify')
                ->with('ok', __('auth.please_verify_email'));
        }

        if ($user->isAdmin()) {
            $request->session()->put('is_admin', true);
            return redirect()->route('admin.dashboard');
        }

        if (empty($user->roles)) {
            return redirect()->route('auth.path');
        }

        return redirect()->intended(route('dashboard'));
    }

    // =========================================================================
    // REGISTER  — NO user is created in DB until OTP is verified
    // =========================================================================

    public function showRegister(): \Illuminate\View\View
    {
        return view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255'],
            'password' => ['required', 'confirmed', PasswordRule::min(8)],
            'terms'    => ['accepted'],
        ], [
            'terms.accepted'     => __('auth.terms_required'),
            'password.confirmed' => __('auth.password_mismatch'),
        ]);

        $email = strtolower(trim($data['email']));

        if (AdminAuth::isAdminEmail($email)) {
            return back()
                ->withInput($request->except('password', 'password_confirmation'))
                ->withErrors(['email' => __('auth.email_reserved')]);
        }

        // Block duplicate emails early (before creating anything)
        if (User::where('email', $email)->exists()) {
            return back()
                ->withInput($request->except('password', 'password_confirmation'))
                ->withErrors(['email' => __('auth.email_taken')]);
        }

        // Generate a cryptographically secure 6-digit OTP
        $code = $this->generateOtpCode();

        // Store the pending registration in session — NO database record yet
        $request->session()->put('pending_reg', [
            'name'               => $data['name'],
            'email'              => $email,
            'password_encrypted' => encrypt($data['password']),  // encrypt for session safety
            'otp_hash'           => Hash::make($code),
            'otp_expires'        => now()->addMinutes(5)->timestamp,
            'otp_attempts'       => 0,
            'resend_count'       => 0,
            'resend_last_at'     => null,
            'initiated_at'       => now()->timestamp,
        ]);

        $this->sendOtpEmail($email, $data['name'], $code);

        return redirect()->route('auth.verify');
    }

    // =========================================================================
    // VERIFY — handles both pending-registration flow and legacy auth flow
    // =========================================================================

    public function showVerify(Request $request): \Illuminate\View\View|RedirectResponse
    {
        // ── Case 1: authenticated user with verified email → nothing to verify ──
        if (auth()->check() && auth()->user()->email_verified_at) {
            return redirect()->route('auth.path');
        }

        // ── Case 2: authenticated user without verified email (legacy/edge case) ──
        if (auth()->check() && ! auth()->user()->email_verified_at) {
            if ($request->boolean('resend') || ! $request->session()->has('email_otp_hash')) {
                $this->issueEmailOtpForUser($request, auth()->user());
            }
            $expiresIn = max(0, (int) $request->session()->get('email_otp_expires', 0) - now()->timestamp);
            return view('auth.verify', [
                'maskedEmail'    => $this->maskEmail(auth()->user()->email),
                'expiresIn'      => $expiresIn,
                'resendCooldown' => 0,
                'attemptsLeft'   => null,
                'legacyFlow'     => true,
            ]);
        }

        // ── Case 3: new registration flow (pending_reg in session) ──
        $pending = $request->session()->get('pending_reg');

        if (! $pending) {
            return redirect()->route('register')
                ->with('error', __('auth.session_expired_register'));
        }

        // Abandon registrations older than 30 min
        if ($pending['initiated_at'] < now()->subMinutes(30)->timestamp) {
            $request->session()->forget('pending_reg');
            return redirect()->route('register')
                ->with('error', __('auth.session_expired_register'));
        }

        // Handle resend via GET ?resend=1
        if ($request->boolean('resend')) {
            return $this->handleResend($request, $pending);
        }

        $expiresIn      = max(0, $pending['otp_expires'] - now()->timestamp);
        $resendCooldown = 0;
        if ($pending['resend_last_at']) {
            $resendCooldown = max(0, 60 - (now()->timestamp - $pending['resend_last_at']));
        }
        $attemptsLeft = max(0, 5 - ($pending['otp_attempts'] ?? 0));
        $maskedEmail  = $this->maskEmail($pending['email']);

        return view('auth.verify', compact(
            'expiresIn', 'resendCooldown', 'attemptsLeft', 'maskedEmail'
        ));
    }

    public function verify(Request $request): RedirectResponse
    {
        // ── Legacy flow: already-authenticated, unverified user ──
        if (auth()->check() && ! auth()->user()->email_verified_at) {
            return $this->verifyLegacy($request);
        }

        // ── New registration flow ──
        $pending = $request->session()->get('pending_reg');

        if (! $pending) {
            return redirect()->route('register')
                ->with('error', __('auth.session_expired_register'));
        }

        $data = $request->validate([
            'code' => ['required', 'string', 'digits:6'],
        ], [
            'code.digits'   => __('auth.otp_digits'),
            'code.required' => __('auth.otp_required'),
        ]);

        // Check expiry
        if ($pending['otp_expires'] < now()->timestamp) {
            $request->session()->forget('pending_reg');
            return redirect()->route('register')
                ->with('error', __('auth.otp_expired_reregister'));
        }

        // Increment attempts first, then enforce max
        $pending['otp_attempts'] = ($pending['otp_attempts'] ?? 0) + 1;

        if ($pending['otp_attempts'] > 5) {
            $request->session()->forget('pending_reg');
            return redirect()->route('register')
                ->with('error', __('auth.otp_max_attempts'));
        }

        // Persist updated attempt count BEFORE checking hash (prevents race on reload)
        $request->session()->put('pending_reg', $pending);

        // Verify the OTP hash
        if (! Hash::check($data['code'], $pending['otp_hash'])) {
            $attemptsLeft = 5 - $pending['otp_attempts'];
            $msg = $attemptsLeft > 0
                ? __('auth.otp_wrong_attempts', ['count' => $attemptsLeft])
                : __('auth.otp_max_attempts');

            return back()->withErrors(['code' => $msg]);
        }

        // ✅ OTP correct — CREATE the user now (first time in database)
        // Check one more time for race condition (duplicate registration)
        if (User::where('email', $pending['email'])->exists()) {
            $request->session()->forget('pending_reg');
            return redirect()->route('login')
                ->with('ok', __('auth.account_already_exists_login'));
        }

        $user = User::create([
            'name'              => $pending['name'],
            'email'             => $pending['email'],
            'password'          => decrypt($pending['password_encrypted']),
            'role'              => 'idea_seeker',
            'kyc_status'        => 'none',
            'email_verified_at' => now(),
        ]);

        // Invalidate pending registration
        $request->session()->forget('pending_reg');

        // Authenticate and regenerate session
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('auth.path')
            ->with('ok', __('auth.email_verified_success'));
    }

    // =========================================================================
    // PATH SELECTION
    // =========================================================================

    public function showPathSelection(): \Illuminate\View\View
    {
        return view('auth.path-selection');
    }

    public function savePath(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'roles'           => ['required', 'array', 'min:1'],
            'roles.*'         => ['in:idea_owner,idea_seeker,developer'],
            'wants_jobs_forum' => ['nullable', 'boolean'],
        ], [
            'roles.required' => __('auth.role_required'),
        ]);

        $user     = $request->user();
        $roles    = $data['roles'];
        $wantsJobs = $request->boolean('wants_jobs_forum') && in_array('developer', $roles, true);

        if (in_array('idea_seeker', $roles, true)) {
            $user->forceFill(['kyc_status' => 'approved'])->save();
        }

        $user->forceFill([
            'roles'            => $roles,
            'role'             => $roles[0],
            'wants_jobs_forum' => $wantsJobs,
        ])->save();

        if (in_array('idea_owner', $roles, true) && ! $user->isKycApproved()) {
            return redirect()
                ->route('verification.kyc', ['purpose' => 'publish_idea'])
                ->with('ok', __('auth.idea_owner_kyc_required'));
        }

        if ($wantsJobs && ! $user->isKycApproved()) {
            return redirect()
                ->route('verification.kyc', ['purpose' => 'jobs_forum'])
                ->with('ok', __('auth.jobs_forum_kyc_required'));
        }

        if (in_array('developer', $roles, true) && ! $wantsJobs) {
            return redirect()
                ->route('profile.cv')
                ->with('ok', __('auth.developer_path_selected'));
        }

        return redirect()->route('dashboard')->with('ok', __('auth.path_saved'));
    }

    // =========================================================================
    // PASSWORD RESET (OTP BASED)
    // =========================================================================

    public function showForgot(): \Illuminate\View\View
    {
        return view('auth.forgot');
    }

    public function sendResetLink(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
        ]);

        $email = strtolower(trim($data['email']));
        $user  = User::where('email', $email)->first();

        // ── Indistinguishable response for existing & non-existing/suspended emails ──
        if (! $user || $user->is_suspended) {
            $request->session()->put('password_reset', [
                'email'          => $email,
                'name'           => 'User',
                'otp_hash'       => Hash::make(Str::random(32)),
                'otp_expires'    => now()->addMinutes(5)->timestamp,
                'otp_attempts'   => 0,
                'resend_count'   => 0,
                'resend_last_at' => now()->timestamp,
                'initiated_at'   => now()->timestamp,
                'dummy'          => true,
            ]);

            return redirect()->route('password.reset')
                ->with('ok', __('auth.reset_link_sent'));
        }

        $existing = $request->session()->get('password_reset');
        if ($existing && $existing['email'] === $user->email && empty($existing['dummy'])) {
            $maxResends      = 3;
            $cooldownSeconds = 60;

            if (($existing['resend_count'] ?? 0) >= $maxResends) {
                return redirect()->route('password.reset')
                    ->with('error', __('auth.resend_max_reached'));
            }

            $lastResent = $existing['resend_last_at'] ?? null;
            if ($lastResent && (now()->timestamp - $lastResent) < $cooldownSeconds) {
                $remaining = $cooldownSeconds - (now()->timestamp - $lastResent);
                return redirect()->route('password.reset')
                    ->with('error', __('auth.resend_cooldown', ['seconds' => $remaining]));
            }

            $resendCount = ($existing['resend_count'] ?? 0) + 1;
        } else {
            $resendCount = 0;
        }

        $code = $this->generateOtpCode();

        $request->session()->put('password_reset', [
            'email'          => $user->email,
            'name'           => $user->name,
            'otp_hash'       => Hash::make($code),
            'otp_expires'    => now()->addMinutes(5)->timestamp,
            'otp_attempts'   => 0,
            'resend_count'   => $resendCount,
            'resend_last_at' => now()->timestamp,
            'initiated_at'   => now()->timestamp,
        ]);

        $this->sendOtpEmail($user->email, $user->name, $code);

        return redirect()->route('password.reset')
            ->with('ok', __('auth.reset_link_sent'));
    }

    public function showReset(Request $request, ?string $token = null): \Illuminate\View\View|RedirectResponse
    {
        $reset = $request->session()->get('password_reset');

        if (! $reset) {
            return redirect()->route('password.request')
                ->withErrors(['email' => __('auth.session_expired_register')]);
        }

        if ($reset['otp_expires'] < now()->timestamp) {
            $request->session()->forget('password_reset');
            return redirect()->route('password.request')
                ->withErrors(['email' => __('auth.otp_expired_resend')]);
        }

        if ($request->boolean('resend')) {
            $maxResends      = 3;
            $cooldownSeconds = 60;

            if (($reset['resend_count'] ?? 0) >= $maxResends) {
                return redirect()->route('password.reset')
                    ->with('error', __('auth.resend_max_reached'));
            }

            $lastResent = $reset['resend_last_at'] ?? null;
            if ($lastResent && (now()->timestamp - $lastResent) < $cooldownSeconds) {
                $remaining = $cooldownSeconds - (now()->timestamp - $lastResent);
                return redirect()->route('password.reset')
                    ->with('error', __('auth.resend_cooldown', ['seconds' => $remaining]));
            }

            $code = $this->generateOtpCode();

            $reset['otp_hash']       = Hash::make(empty($reset['dummy']) ? $code : Str::random(32));
            $reset['otp_expires']    = now()->addMinutes(5)->timestamp;
            $reset['otp_attempts']   = 0;
            $reset['resend_count']   = ($reset['resend_count'] ?? 0) + 1;
            $reset['resend_last_at'] = now()->timestamp;

            $request->session()->put('password_reset', $reset);

            if (empty($reset['dummy'])) {
                $this->sendOtpEmail($reset['email'], $reset['name'], $code);
            }

            return redirect()->route('password.reset')
                ->with('ok', __('auth.otp_resent'));
        }

        $expiresIn      = max(0, $reset['otp_expires'] - now()->timestamp);
        $resendCooldown = 0;
        if (! empty($reset['resend_last_at'])) {
            $resendCooldown = max(0, 60 - (now()->timestamp - $reset['resend_last_at']));
        }
        $maskedEmail  = $this->maskEmail($reset['email']);
        $attemptsLeft = max(0, 5 - ($reset['otp_attempts'] ?? 0));

        return view('auth.reset', compact(
            'maskedEmail', 'expiresIn', 'resendCooldown', 'attemptsLeft'
        ));
    }

    public function resetPassword(Request $request): RedirectResponse
    {
        $reset = $request->session()->get('password_reset');

        if (! $reset) {
            return redirect()->route('password.request')
                ->withErrors(['email' => __('auth.session_expired_register')]);
        }

        $data = $request->validate([
            'code'     => ['required', 'string', 'digits:6'],
            'password' => ['required', 'confirmed', PasswordRule::min(8)],
        ], [
            'code.digits'        => __('auth.otp_digits'),
            'code.required'      => __('auth.otp_required'),
            'password.confirmed' => __('auth.password_mismatch'),
        ]);

        if ($reset['otp_expires'] < now()->timestamp) {
            $request->session()->forget('password_reset');
            return redirect()->route('password.request')
                ->withErrors(['email' => __('auth.otp_expired_resend')]);
        }

        $reset['otp_attempts'] = ($reset['otp_attempts'] ?? 0) + 1;

        if ($reset['otp_attempts'] > 5) {
            $request->session()->forget('password_reset');
            return redirect()->route('password.request')
                ->withErrors(['email' => __('auth.otp_max_attempts')]);
        }

        $request->session()->put('password_reset', $reset);

        if (! Hash::check($data['code'], $reset['otp_hash'])) {
            $attemptsLeft = 5 - $reset['otp_attempts'];
            $msg = $attemptsLeft > 0
                ? __('auth.otp_wrong_attempts', ['count' => $attemptsLeft])
                : __('auth.otp_max_attempts');

            return back()->withErrors(['code' => $msg]);
        }

        // OTP verified — update user password
        $user = User::where('email', $reset['email'])->first();

        if (! $user) {
            $request->session()->forget('password_reset');
            return redirect()->route('password.request')
                ->withErrors(['email' => __('auth.email_not_found')]);
        }

        $user->forceFill([
            'password'       => $data['password'],
            'remember_token' => Str::random(60),
        ])->save();

        event(new PasswordReset($user));

        $request->session()->forget('password_reset');

        return redirect()->route('login')
            ->with('ok', __('auth.password_reset_success'));
    }

    // =========================================================================
    // LOGOUT
    // =========================================================================

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->forget(['is_admin', 'pending_reg']);
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    // =========================================================================
    // PRIVATE HELPERS
    // =========================================================================

    /**
     * Verify OTP for an already-authenticated user who still needs email verification.
     */
    private function verifyLegacy(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'digits:6'],
        ]);

        $hash    = $request->session()->get('email_otp_hash');
        $expires = (int) $request->session()->get('email_otp_expires', 0);

        if (! $hash || $expires < now()->timestamp) {
            return back()->withErrors(['code' => __('auth.otp_expired_resend')]);
        }

        if (! Hash::check($data['code'], $hash)) {
            return back()->withErrors(['code' => __('auth.otp_wrong')]);
        }

        $request->session()->forget(['email_otp_hash', 'email_otp_expires']);
        $request->user()->forceFill(['email_verified_at' => now()])->save();

        return redirect()->route('auth.path')->with('ok', __('auth.email_verified_success'));
    }

    /**
     * Handle OTP resend for the new pending-registration flow.
     */
    private function handleResend(Request $request, array $pending): RedirectResponse
    {
        $maxResends      = 3;
        $cooldownSeconds = 60;

        if (($pending['resend_count'] ?? 0) >= $maxResends) {
            return redirect()->route('auth.verify')
                ->with('error', __('auth.resend_max_reached'));
        }

        $lastResent = $pending['resend_last_at'] ?? null;
        if ($lastResent && (now()->timestamp - $lastResent) < $cooldownSeconds) {
            $remaining = $cooldownSeconds - (now()->timestamp - $lastResent);
            return redirect()->route('auth.verify')
                ->with('error', __('auth.resend_cooldown', ['seconds' => $remaining]));
        }

        $code = $this->generateOtpCode();

        $pending['otp_hash']       = Hash::make($code);
        $pending['otp_expires']    = now()->addMinutes(5)->timestamp;
        $pending['otp_attempts']   = 0;
        $pending['resend_count']   = ($pending['resend_count'] ?? 0) + 1;
        $pending['resend_last_at'] = now()->timestamp;

        $request->session()->put('pending_reg', $pending);

        $this->sendOtpEmail($pending['email'], $pending['name'], $code);

        return redirect()->route('auth.verify')
            ->with('ok', __('auth.otp_resent'));
    }

    /**
     * Issue OTP for an already-authenticated (but unverified) user.
     */
    private function issueEmailOtpForUser(Request $request, User $user): void
    {
        $code = $this->generateOtpCode();
        $request->session()->put('email_otp_hash', Hash::make($code));
        $request->session()->put('email_otp_expires', now()->addMinutes(5)->timestamp);

        $this->sendOtpEmail($user->email, $user->name, $code);
    }

    /**
     * Generate a cryptographically secure 6-digit OTP string.
     */
    private function generateOtpCode(): string
    {
        return str_pad((string) random_int(0, 999_999), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Send OTP email, catching failures silently (logged to laravel.log).
     */
    private function sendOtpEmail(string $email, string $name, string $code): void
    {
        try {
            Mail::to($email)->send(new OtpMail($name, $code));
        } catch (\Throwable $e) {
            logger()->error("OTP mail failed [{$email}]: " . $e->getMessage());
        }
    }

    /**
     * Mask email for display: john.doe@example.com → jo**e@example.com
     */
    private function maskEmail(string $email): string
    {
        [$local, $domain] = explode('@', $email, 2);
        $len    = strlen($local);
        $masked = $len <= 2
            ? str_repeat('*', $len)
            : substr($local, 0, 2) . str_repeat('*', max(0, $len - 3)) . substr($local, -1);

        return $masked . '@' . $domain;
    }
}
