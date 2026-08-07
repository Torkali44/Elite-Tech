<?php

namespace Tests\Feature;

use App\Models\Idea;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IdeaTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_view_published_ideas(): void
    {
        $user = User::create([
            'name'              => 'Owner',
            'email'             => 'owner@example.com',
            'password'          => bcrypt('password123'),
            'role'              => 'idea_owner',
            'email_verified_at' => now(),
        ]);

        Idea::create([
            'user_id' => $user->id,
            'title' => 'فكرة ممتازة',
            'category' => 'الذكاء الاصطناعي',
            'description' => 'وصف الفكرة',
            'status' => 'published',
        ]);

        $response = $this->get('/ideas');

        $response->assertStatus(200);
        $response->assertSee('فكرة ممتازة');
    }

    public function test_unverified_idea_owner_cannot_access_create_idea_page(): void
    {
        $user = User::create([
            'name'              => 'Owner',
            'email'             => 'unverified@example.com',
            'password'          => bcrypt('password123'),
            'role'              => 'idea_owner',
            'roles'             => ['idea_owner'],
            'kyc_status'        => 'none',
            'email_verified_at' => now(), // email verified, KYC not yet
        ]);

        $response = $this->actingAs($user)->get('/ideas/create');

        $response->assertRedirect(route('verification.kyc', ['purpose' => 'publish_idea']));
    }

    public function test_verified_idea_owner_can_store_idea(): void
    {
        $user = User::create([
            'name'              => 'Verified Owner',
            'email'             => 'verified@example.com',
            'password'          => bcrypt('password123'),
            'role'              => 'idea_owner',
            'roles'             => ['idea_owner'],
            'kyc_status'        => 'approved',
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($user)->post('/ideas', [
            'title' => 'فكرة جديدة للنشر',
            'summary' => 'ملخص الفكرة',
            'problem' => 'هذه مشكلة معقدة تحتاج حلاً ذكياً جدًا',
            'solution' => 'هذا حل مبتكر جداً لهذه المشكلة',
            'category' => 'الذكاء الاصطناعي',
            'ip_agreement' => '1',
            'intent' => 'draft',
        ]);

        $response->assertRedirect(route('dashboard.ideaOwner'));
        $this->assertDatabaseHas('ideas', [
            'title' => 'فكرة جديدة للنشر',
            'status' => 'draft',
            'user_id' => $user->id,
        ]);
    }
}
