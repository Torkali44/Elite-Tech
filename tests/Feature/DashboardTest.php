<?php

namespace Tests\Feature;

use App\Models\Cv;
use App\Models\Idea;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Regression: DashboardController::index() used SUM(status = "published") with
     * double-quoted string literal, invalid in SQLite (treats it as a column identifier),
     * causing SQLSTATE[HY000] General error on dashboard load for any user with ideas.
     * Fix: switched raw SQL to SUM(status = 'published') with single-quoted literals.
     */
    public function test_dashboard_loads_correctly_when_user_has_ideas(): void
    {
        $user = User::create([
            'name'              => 'Owner',
            'email'             => 'owner@example.com',
            'password'          => bcrypt('password123'),
            'role'              => 'idea_owner',
            'roles'             => ['idea_owner'],
            'kyc_status'        => 'approved',
            'email_verified_at' => now(),
        ]);

        Idea::create([
            'user_id'     => $user->id,
            'title'       => 'Published Idea',
            'category'    => 'Tech',
            'description' => 'Description',
            'status'      => 'published',
        ]);

        Idea::create([
            'user_id'     => $user->id,
            'title'       => 'Draft Idea',
            'category'    => 'Tech',
            'description' => 'Description',
            'status'      => 'draft',
        ]);

        // Previously crashed with SQLSTATE[HY000] because of SUM(status = "published")
        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
    }

    /**
     * Regression: ProfileController::saveCv() only triggered KYC re-evaluation when
     * portfolio_url was set to a non-empty different value. Clearing an existing
     * portfolio URL (setting it to '') bypassed the PRD Section 7 requirement.
     * Fix: aligned the condition with SettingsController to flag removal too.
     */
    public function test_clearing_portfolio_url_triggers_kyc_rereview(): void
    {
        $user = User::create([
            'name'              => 'Verified',
            'email'             => 'verified@example.com',
            'password'          => bcrypt('password123'),
            'role'              => 'developer',
            'roles'             => ['developer'],
            'kyc_status'        => 'approved',
            'portfolio_url'     => 'https://old-portfolio.example.com',
            'email_verified_at' => now(),
        ]);

        Cv::create([
            'user_id' => $user->id,
            'data'    => ['portfolio_url' => 'https://old-portfolio.example.com'],
        ]);

        $response = $this->actingAs($user)->post('/profile/cv-builder', [
            'title'          => 'Developer',
            'summary'        => '',
            'skills'         => '',
            'experience'     => '',
            'education'      => '',
            'portfolio_url'  => '',
            'phone'          => '',
            'location'       => '',
            'linkedin'       => '',
            'github'         => '',
            'languages'      => '',
            'certifications' => '',
            'projects'       => '',
            'years_experience' => '',
            'availability'   => '',
            'expected_salary' => '',
        ]);

        $response->assertRedirect(route('profile.cv'));
        $response->assertSessionHas('error');

        $this->assertDatabaseHas('users', [
            'id'         => $user->id,
            'kyc_status' => 'pending',
        ]);
    }
}
