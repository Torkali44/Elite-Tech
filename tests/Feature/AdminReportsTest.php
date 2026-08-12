<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Verification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminReportsTest extends TestCase
{
    use RefreshDatabase;

    private function makeAdminUser(): User
    {
        return User::forceCreate([
            'name'              => 'Admin User',
            'email'             => 'admin_' . uniqid() . '@example.com',
            'password'          => bcrypt('Password123!'),
            'role'              => 'admin',
            'roles'             => ['admin'],
            'email_verified_at' => now(),
        ]);
    }

    public function test_admin_reports_loads_successfully_and_computes_avg_kyc_hours(): void
    {
        $admin = $this->makeAdminUser();
        $user = User::forceCreate([
            'name'              => 'KYC User',
            'email'             => 'kyc_' . uniqid() . '@example.com',
            'password'          => bcrypt('Password123!'),
            'role'              => 'developer',
            'email_verified_at' => now(),
        ]);

        Verification::create([
            'user_id'     => $user->id,
            'doc_type'    => 'national_id',
            'purpose'     => 'publish_idea',
            'status'      => 'approved',
            'created_at'  => now()->subHours(4),
            'reviewed_at' => now(),
        ]);

        $response = $this->actingAs($admin)->get('/admin/reports');

        $response->assertStatus(200);
        $response->assertViewHas('avgKycHours');
    }
}
