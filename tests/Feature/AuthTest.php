<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\OtpMail;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    // =========================================================================
    // REGISTRATION — pending flow, NO user created before OTP
    // =========================================================================

    public function test_register_stores_pending_in_session_not_database(): void
    {
        Mail::fake();

        $response = $this->post('/register', [
            'name'                  => 'مستخدم جديد',
            'email'                 => 'newuser@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
            'terms'                 => '1',
        ]);

        $response->assertRedirect(route('auth.verify'));

        // ✅ User must NOT be in the database yet
        $this->assertDatabaseMissing('users', ['email' => 'newuser@example.com']);

        // ✅ User must NOT be authenticated
        $this->assertGuest();

        // ✅ Pending registration is in session
        $response->assertSessionHas('pending_reg.email', 'newuser@example.com');

        // ✅ OTP email was sent
        Mail::assertSent(OtpMail::class, fn ($m) => $m->hasTo('newuser@example.com'));
    }

    public function test_register_with_existing_email_is_rejected(): void
    {
        Mail::fake();

        User::factory()->create(['email' => 'existing@example.com']);

        $response = $this->post('/register', [
            'name'                  => 'Test User',
            'email'                 => 'existing@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
            'terms'                 => '1',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
        Mail::assertNothingSent();
    }

    public function test_verify_page_redirects_to_register_without_pending_session(): void
    {
        $this->get('/auth/verify')->assertRedirect(route('register'));
    }

    public function test_correct_otp_creates_user_and_authenticates(): void
    {
        Mail::fake();

        $code = '123456';

        $this->withSession([
            'pending_reg' => [
                'name'               => 'مستخدم جديد',
                'email'              => 'otp_ok@example.com',
                'password_encrypted' => encrypt('password123'),
                'otp_hash'           => Hash::make($code),
                'otp_expires'        => now()->addMinutes(5)->timestamp,
                'otp_attempts'       => 0,
                'resend_count'       => 0,
                'resend_last_at'     => null,
                'initiated_at'       => now()->timestamp,
            ],
        ])->post('/auth/verify', ['code' => $code])
          ->assertRedirect(route('auth.path'));

        // ✅ User now exists in DB
        $this->assertDatabaseHas('users', [
            'email' => 'otp_ok@example.com',
        ]);

        // ✅ email_verified_at is set
        $user = User::where('email', 'otp_ok@example.com')->first();
        $this->assertNotNull($user->email_verified_at);

        // ✅ User is authenticated
        $this->assertAuthenticatedAs($user);

        // ✅ Pending session is cleared
        $this->assertFalse(session()->has('pending_reg'));
    }

    public function test_wrong_otp_is_rejected_and_user_not_created(): void
    {
        $this->withSession([
            'pending_reg' => [
                'name'               => 'مستخدم',
                'email'              => 'wrong_otp@example.com',
                'password_encrypted' => encrypt('password123'),
                'otp_hash'           => Hash::make('999999'),
                'otp_expires'        => now()->addMinutes(5)->timestamp,
                'otp_attempts'       => 0,
                'resend_count'       => 0,
                'resend_last_at'     => null,
                'initiated_at'       => now()->timestamp,
            ],
        ])->post('/auth/verify', ['code' => '111111'])
          ->assertSessionHasErrors('code');

        $this->assertDatabaseMissing('users', ['email' => 'wrong_otp@example.com']);
        $this->assertGuest();
    }

    public function test_expired_otp_clears_session_and_redirects_to_register(): void
    {
        $this->withSession([
            'pending_reg' => [
                'name'               => 'مستخدم',
                'email'              => 'expired_otp@example.com',
                'password_encrypted' => encrypt('password123'),
                'otp_hash'           => Hash::make('123456'),
                'otp_expires'        => now()->subMinutes(1)->timestamp, // EXPIRED
                'otp_attempts'       => 0,
                'resend_count'       => 0,
                'resend_last_at'     => null,
                'initiated_at'       => now()->timestamp,
            ],
        ])->post('/auth/verify', ['code' => '123456'])
          ->assertRedirect(route('register'));

        $this->assertDatabaseMissing('users', ['email' => 'expired_otp@example.com']);
        $this->assertGuest();
    }

    public function test_max_otp_attempts_blocks_and_clears_session(): void
    {
        // Simulate 5 failed attempts by starting at attempts=4 and submitting a wrong code
        // The controller increments to 5, checks > 5 (false), saves, then checks hash (fail)
        // On next wrong attempt with attempts=5: increments to 6, checks > 5 (true) → clears session
        $session = [
            'pending_reg' => [
                'name'               => 'مستخدم',
                'email'              => 'max_attempts@example.com',
                'password_encrypted' => encrypt('password123'),
                'otp_hash'           => Hash::make('999999'),
                'otp_expires'        => now()->addMinutes(5)->timestamp,
                'otp_attempts'       => 5, // already maxed (next attempt will be #6 → blocked)
                'resend_count'       => 0,
                'resend_last_at'     => null,
                'initiated_at'       => now()->timestamp,
            ],
        ];

        // 6th attempt: should clear session and redirect to register
        $this->withSession($session)
             ->post('/auth/verify', ['code' => '000000'])
             ->assertRedirect(route('register'));

        $this->assertDatabaseMissing('users', ['email' => 'max_attempts@example.com']);
        $this->assertGuest();
    }

    public function test_otp_cannot_be_reused_after_success(): void
    {
        Mail::fake();

        $code = '777777';
        $session = [
            'pending_reg' => [
                'name'               => 'مستخدم',
                'email'              => 'reuse_otp@example.com',
                'password_encrypted' => encrypt('password123'),
                'otp_hash'           => Hash::make($code),
                'otp_expires'        => now()->addMinutes(5)->timestamp,
                'otp_attempts'       => 0,
                'resend_count'       => 0,
                'resend_last_at'     => null,
                'initiated_at'       => now()->timestamp,
            ],
        ];

        // First attempt: succeeds → creates user, clears pending_reg, logs in
        $this->withSession($session)->post('/auth/verify', ['code' => $code])->assertRedirect();

        // Verify user was created
        $this->assertDatabaseHas('users', ['email' => 'reuse_otp@example.com']);

        // Now logout to start fresh
        $this->post('/logout');

        // Second attempt with same code on fresh session: no pending_reg → redirect to register
        $this->post('/auth/verify', ['code' => $code])->assertRedirect(route('register'));
    }


    public function test_verify_page_not_accessible_with_verified_user(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $this->actingAs($user)->get('/auth/verify')->assertRedirect(route('auth.path'));
    }

    // =========================================================================
    // SUSPENDED USER
    // =========================================================================

    public function test_suspended_user_cannot_login(): void
    {
        $user = User::factory()->create([
            'email'            => 'suspended@example.com',
            'password'         => bcrypt('password123'),
            'role'             => 'developer',
            'is_suspended'     => true,
            'email_verified_at' => now(),
        ]);

        $this->post('/login', [
            'email'    => 'suspended@example.com',
            'password' => 'password123',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_suspended_user_is_blocked_by_middleware(): void
    {
        $user = User::factory()->create([
            'role'             => 'developer',
            'roles'            => ['developer'],
            'is_suspended'     => true,
            'email_verified_at' => now(),
        ]);

        $this->actingAs($user)->get('/dashboard/developer')->assertRedirect(route('login'));
        $this->assertGuest();
    }

    // =========================================================================
    // ADMIN
    // =========================================================================

    public function test_admin_user_logging_in_is_redirected_to_admin_dashboard(): void
    {
        $admin = User::factory()->create([
            'email'             => 'admin@example.com',
            'password'          => bcrypt('password123'),
            'role'              => 'admin',
            'roles'             => ['admin'],
            'email_verified_at' => now(),
        ]);

        $this->post('/login', [
            'email'    => 'admin@example.com',
            'password' => 'password123',
        ])->assertRedirect(route('admin.dashboard'));

        $this->assertTrue((bool) session('is_admin'));
    }

    public function test_admin_login_via_admin_url_redirects_to_login(): void
    {
        $this->get('/admin/login')->assertRedirect('/login');
    }

    public function test_admin_credentials_via_unified_login_open_admin_dashboard(): void
    {
        User::factory()->create([
            'email'             => 'admin@elitetech.com',
            'password'          => bcrypt('password123'),
            'role'              => 'admin',
            'roles'             => ['admin'],
            'email_verified_at' => now(),
        ]);

        $this->post('/login', [
            'email'    => 'admin@elitetech.com',
            'password' => 'password123',
        ])->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticated();
        $this->assertTrue((bool) session('is_admin'));
    }

    // =========================================================================
    // PATH SELECTION
    // =========================================================================

    public function test_idea_seeker_path_selection_activates_kyc_instantly(): void
    {
        $user = User::factory()->create([
            'role'             => 'idea_seeker',
            'kyc_status'       => 'none',
            'email_verified_at' => now(),
        ]);

        $this->actingAs($user)
             ->post('/auth/path-selection', ['roles' => ['idea_seeker']])
             ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('users', [
            'id'         => $user->id,
            'kyc_status' => 'approved',
        ]);
    }

    // =========================================================================
    // LOGOUT
    // =========================================================================

    public function test_logout_invalidates_session(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);

        $this->actingAs($user)->post('/logout')->assertRedirect(route('home'));
        $this->assertGuest();
    }

    // =========================================================================
    // PASSWORD RESET (OTP BASED)
    // =========================================================================

    public function test_password_reset_returns_indistinguishable_response_for_non_existent_email(): void
    {
        Mail::fake();

        // 1. Non-existent email request: redirects to reset with success message, sends no mail
        $response = $this->post('/forgot-password', [
            'email' => 'nonexistent@example.com',
        ]);

        $response->assertRedirect(route('password.reset'));
        $response->assertSessionHas('ok', __('auth.reset_link_sent'));
        Mail::assertNothingSent();

        // 2. Existing email request: yields exact same redirect & flash message, sends mail
        $user = User::factory()->create(['email' => 'existing@example.com']);

        $responseExisting = $this->post('/forgot-password', [
            'email' => 'existing@example.com',
        ]);

        $responseExisting->assertRedirect(route('password.reset'));
        $responseExisting->assertSessionHas('ok', __('auth.reset_link_sent'));
        Mail::assertSent(OtpMail::class, fn ($m) => $m->hasTo('existing@example.com'));
    }

    public function test_password_reset_sends_otp_and_updates_password_on_valid_otp(): void
    {
        Mail::fake();

        $user = User::factory()->create([
            'email'    => 'reset_test@example.com',
            'password' => bcrypt('old_password123'),
        ]);

        // 1. Request OTP
        $response = $this->post('/forgot-password', [
            'email' => 'reset_test@example.com',
        ]);

        $response->assertRedirect(route('password.reset'));
        $this->assertTrue(session()->has('password_reset'));
        Mail::assertSent(OtpMail::class, fn ($m) => $m->hasTo('reset_test@example.com'));

        // 2. Submit OTP + new password
        $code = '123456';
        $resetSession = session('password_reset');
        $resetSession['otp_hash'] = Hash::make($code);
        session(['password_reset' => $resetSession]);

        $response2 = $this->post('/reset-password', [
            'code'                  => $code,
            'password'              => 'new_password123',
            'password_confirmation' => 'new_password123',
        ]);

        $response2->assertRedirect(route('login'));
        $response2->assertSessionHas('ok');
        $this->assertFalse(session()->has('password_reset'));

        // 3. Verify new password works
        $user->refresh();
        $this->assertTrue(Hash::check('new_password123', $user->password));
    }

    public function test_password_reset_rejects_wrong_otp(): void
    {
        $user = User::factory()->create(['email' => 'wrong_reset@example.com']);

        $this->withSession([
            'password_reset' => [
                'email'        => 'wrong_reset@example.com',
                'name'         => 'User',
                'otp_hash'     => Hash::make('999999'),
                'otp_expires'  => now()->addMinutes(5)->timestamp,
                'otp_attempts' => 0,
                'initiated_at' => now()->timestamp,
            ],
        ])->post('/reset-password', [
            'code'                  => '111111',
            'password'              => 'new_password123',
            'password_confirmation' => 'new_password123',
        ])->assertSessionHasErrors('code');

        $user->refresh();
        $this->assertFalse(Hash::check('new_password123', $user->password));
    }

    public function test_password_reset_rejects_expired_otp(): void
    {
        $user = User::factory()->create(['email' => 'expired_reset@example.com']);

        $this->withSession([
            'password_reset' => [
                'email'        => 'expired_reset@example.com',
                'name'         => 'User',
                'otp_hash'     => Hash::make('123456'),
                'otp_expires'  => now()->subMinute()->timestamp, // EXPIRED
                'otp_attempts' => 0,
                'initiated_at' => now()->timestamp,
            ],
        ])->post('/reset-password', [
            'code'                  => '123456',
            'password'              => 'new_password123',
            'password_confirmation' => 'new_password123',
        ])->assertRedirect(route('password.request'));
    }

    public function test_password_reset_enforces_max_attempts(): void
    {
        $user = User::factory()->create(['email' => 'max_reset@example.com']);

        $this->withSession([
            'password_reset' => [
                'email'        => 'max_reset@example.com',
                'name'         => 'User',
                'otp_hash'     => Hash::make('999999'),
                'otp_expires'  => now()->addMinutes(5)->timestamp,
                'otp_attempts' => 5, // maxed out
                'initiated_at' => now()->timestamp,
            ],
        ])->post('/reset-password', [
            'code'                  => '000000',
            'password'              => 'new_password123',
            'password_confirmation' => 'new_password123',
        ])->assertRedirect(route('password.request'));

        $this->assertFalse(session()->has('password_reset'));
    }

    // =========================================================================
    // SEEDED ACCOUNTS VERIFICATION (ADMIN & TORK)
    // =========================================================================

    public function test_seeded_admin_account_can_login_and_access_admin_dashboard(): void
    {
        $this->seed(\Database\Seeders\DatabaseSeeder::class);

        $response = $this->post('/login', [
            'email'    => 'etech1596@gmail.com',
            'password' => 'elitetech_Admin_2026_Password',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticated();
        $this->assertTrue((bool) session('is_admin'));

        $admin = User::where('email', 'etech1596@gmail.com')->first();
        $this->assertTrue($admin->isAdmin());

        $this->actingAs($admin)
             ->withSession(['is_admin' => true])
             ->get('/admin/dashboard')
             ->assertStatus(200);
    }

    public function test_seeded_tork_admin_account_can_login_and_access_admin_dashboard(): void
    {
        $this->seed(\Database\Seeders\DatabaseSeeder::class);

        $response = $this->post('/login', [
            'email'    => 'tork932@gmail.com',
            'password' => 'passwordAdmin',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticated();
        $this->assertTrue((bool) session('is_admin'));

        $tork = User::where('email', 'tork932@gmail.com')->first();
        $this->assertTrue($tork->isAdmin());

        $this->actingAs($tork)
             ->withSession(['is_admin' => true])
             ->get('/admin/dashboard')
             ->assertStatus(200);
    }
}




