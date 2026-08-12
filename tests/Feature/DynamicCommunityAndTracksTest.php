<?php

namespace Tests\Feature;

use App\Models\CareerTrack;
use App\Models\Idea;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DynamicCommunityAndTracksTest extends TestCase
{
    use RefreshDatabase;

    private function makeUser(array $attrs = []): User
    {
        return User::forceCreate(array_merge([
            'name'              => 'Dynamic Test User',
            'email'             => 'dyn_' . uniqid() . '@example.com',
            'password'          => bcrypt('password123'),
            'role'              => 'developer',
            'title'             => 'Senior Engineer',
            'bio'               => 'Test Bio Info',
            'kyc_status'        => 'approved',
            'email_verified_at' => now(),
        ], $attrs));
    }

    public function test_community_index_loads_real_database_members(): void
    {
        $user1 = $this->makeUser(['name' => 'Alice Member']);
        $user2 = $this->makeUser(['name' => 'Bob Member']);

        $response = $this->get('/community');

        $response->assertStatus(200);
        $response->assertSee('Alice Member');
        $response->assertSee('Bob Member');
    }

    public function test_community_show_loads_real_user_profile(): void
    {
        $user = $this->makeUser(['name' => 'Charlie Member']);

        $response = $this->get("/community/{$user->id}");

        $response->assertStatus(200);
        $response->assertSee('Charlie Member');
    }

    public function test_career_tracks_index_and_show_load_user_data_from_database(): void
    {
        $user = $this->makeUser(['role' => 'idea_owner']);

        Idea::create([
            'user_id'     => $user->id,
            'title'       => 'مشروع الذكاء التفاعلي',
            'category'    => 'AI & Data',
            'description' => 'وصف الفكرة',
            'status'      => 'published',
        ]);

        $this->actingAs($user);

        // 1. Index
        $response = $this->get('/career-tracks');
        $response->assertStatus(200);
        $response->assertSee('مسار صاحب فكرة');

        // 2. Show idea-owner track
        $showResponse = $this->get('/career-tracks/idea-owner');
        $showResponse->assertStatus(200);
        $showResponse->assertSee('مشروع الذكاء التفاعلي');
    }

    public function test_career_tracks_update_saves_to_database(): void
    {
        $user = $this->makeUser();
        $this->actingAs($user);

        $response = $this->post('/career-tracks/developer/update', [
            'github' => 'https://github.com/my-awesome-repo',
            'notes'  => 'تحديث ملحوظات المطور',
        ]);

        $response->assertRedirect(route('career-tracks.show', 'developer'));

        $this->assertDatabaseHas('career_tracks', [
            'user_id'     => $user->id,
            'slug'        => 'developer',
            'github'      => 'https://github.com/my-awesome-repo',
            'admin_notes' => 'تحديث ملحوظات المطور',
        ]);
    }
}
