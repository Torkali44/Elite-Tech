<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_and_is_redirected_to_verify(): void
    {
        $response = $this->post('/register', [
            'name' => 'مستخدم جديد',
            'email' => 'newuser@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'terms' => '1',
        ]);

        $response->assertRedirect(route('auth.verify'));
        $this->assertDatabaseHas('users', ['email' => 'newuser@example.com']);
        $this->assertAuthenticated();
    }

    public function test_suspended_user_cannot_login(): void
    {
        $user = User::create([
            'name' => 'معلق',
            'email' => 'suspended@example.com',
            'password' => bcrypt('password123'),
            'role' => 'developer',
            'is_suspended' => true,
        ]);

        $response = $this->post('/login', [
            'email' => 'suspended@example.com',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_suspended_user_is_blocked_by_role_middleware(): void
    {
        $user = User::create([
            'name' => 'معلق',
            'email' => 'suspended_role@example.com',
            'password' => bcrypt('password123'),
            'role' => 'developer',
            'roles' => ['developer'],
            'is_suspended' => true,
        ]);

        $response = $this->actingAs($user)->get('/dashboard/developer');

        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_admin_user_logging_in_via_standard_login_is_redirected_to_admin_dashboard(): void
    {
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password123'),
            'role' => 'admin',
            'roles' => ['admin'],
        ]);

        $response = $this->post('/login', [
            'email' => 'admin@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertTrue((bool) session('is_admin'));
    }

    public function test_admin_login_via_admin_url_redirects_to_login(): void
    {
        $this->get('/admin/login')->assertRedirect('/login');
    }

    public function test_admin_credentials_via_unified_login_open_admin_dashboard(): void
    {
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@elitetech.com',
            'password' => bcrypt('password123'),
            'role' => 'admin',
            'roles' => ['admin'],
        ]);

        $response = $this->post('/login', [
            'email' => 'admin@elitetech.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticated();
        $this->assertTrue((bool) session('is_admin'));
    }

    public function test_idea_seeker_path_selection_activates_kyc_instantly(): void
    {
        $user = User::create([
            'name' => 'Idea Seeker User',
            'email' => 'seeker_test@example.com',
            'password' => bcrypt('password123'),
            'role' => 'idea_seeker',
            'kyc_status' => 'none',
        ]);

        $response = $this->actingAs($user)->post('/auth/path-selection', [
            'roles' => ['idea_seeker'],
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'kyc_status' => 'approved',
        ]);
    }
}
