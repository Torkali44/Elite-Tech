<?php

namespace Tests\Feature;

use App\Models\Idea;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RouteModelBindingTest extends TestCase
{
    use RefreshDatabase;

    private function makeUser(array $attrs = []): User
    {
        return User::forceCreate(array_merge([
            'name'              => 'RMB User',
            'email'             => 'rmb_' . uniqid() . '@example.com',
            'password'          => bcrypt('password123'),
            'role'              => 'idea_owner',
            'kyc_status'        => 'approved',
            'email_verified_at' => now(),
        ], $attrs));
    }

    public function test_non_existent_idea_returns_404_automatically(): void
    {
        $response = $this->get('/ideas/9999999');
        $response->assertStatus(404);
    }

    public function test_route_model_binding_resolves_published_idea_page(): void
    {
        $user = $this->makeUser();
        $idea = Idea::forceCreate([
            'user_id'     => $user->id,
            'title'       => 'فكرة البث المباشر',
            'category'    => 'Tech',
            'description' => 'وصف تفصيلي للفكرة الذكية',
            'status'      => 'published',
        ]);

        $response = $this->get("/ideas/{$idea->id}");

        $response->assertStatus(200);
        $response->assertSee('فكرة البث المباشر');
    }

    public function test_route_model_binding_works_for_edit_and_update(): void
    {
        $user = $this->makeUser();
        $idea = Idea::forceCreate([
            'user_id'     => $user->id,
            'title'       => 'عنوان المسودة القديم',
            'category'    => 'Software',
            'description' => 'وصف المسودة القديم',
            'status'      => 'draft',
        ]);

        $this->actingAs($user);

        // Edit page
        $editResp = $this->get("/ideas/{$idea->id}/edit");
        $editResp->assertStatus(200);
        $editResp->assertSee('عنوان المسودة القديم');

        // Update action
        $updateResp = $this->put("/ideas/{$idea->id}", [
            'title'             => 'العنوان المحدث الجديد',
            'summary'           => 'ملخص جديد',
            'problem'           => 'هنا مشكلة حقيقية واضحة وموسعة جداً',
            'solution'          => 'هنا حل حقيقي وواضح ومفصل جداً',
            'category'          => 'Software',
            'based_on_previous' => 'no',
            'intent'            => 'draft',
        ]);

        $updateResp->assertRedirect(route('dashboard.ideaOwner'));
        $this->assertDatabaseHas('ideas', [
            'id'    => $idea->id,
            'title' => 'العنوان المحدث الجديد',
        ]);
    }
}
