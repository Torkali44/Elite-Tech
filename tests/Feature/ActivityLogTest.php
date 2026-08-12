<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\Idea;
use App\Models\User;
use App\Models\Verification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActivityLogTest extends TestCase
{
    use RefreshDatabase;

    private function makeAdmin(): User
    {
        return User::forceCreate([
            'name'              => 'Admin Logger',
            'email'             => 'admin_log_' . uniqid() . '@example.com',
            'password'          => bcrypt('password123'),
            'role'              => 'admin',
            'kyc_status'        => 'approved',
            'email_verified_at' => now(),
        ]);
    }

    public function test_admin_actions_create_activity_log_records(): void
    {
        $admin = $this->makeAdmin();
        $this->actingAs($admin);

        // 1. Suspend user action
        $targetUser = User::forceCreate([
            'name'              => 'Target User',
            'email'             => 'target_' . uniqid() . '@example.com',
            'password'          => bcrypt('password123'),
            'role'              => 'idea_owner',
            'kyc_status'        => 'approved',
            'email_verified_at' => now(),
        ]);

        $this->post("/admin/users/{$targetUser->id}/suspend", [
            'admin_notes' => 'ملاحظة التعليق التجريبية',
        ]);

        $this->assertDatabaseHas('activity_logs', [
            'admin_id'     => $admin->id,
            'action'       => 'suspend_user',
            'subject_type' => User::class,
            'subject_id'   => $targetUser->id,
        ]);

        // 2. Publish idea action
        $idea = Idea::forceCreate([
            'user_id'     => $targetUser->id,
            'title'       => 'فكرة تجريبية للـ Log',
            'category'    => 'Tech',
            'description' => 'وصف الفكرة',
            'status'      => 'pending',
        ]);

        $this->post("/admin/ideas/{$idea->id}/publish");

        $this->assertDatabaseHas('activity_logs', [
            'admin_id'     => $admin->id,
            'action'       => 'publish_idea',
            'subject_type' => Idea::class,
            'subject_id'   => $idea->id,
        ]);
    }
}
