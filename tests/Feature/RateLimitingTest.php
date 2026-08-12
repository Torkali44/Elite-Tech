<?php

namespace Tests\Feature;

use App\Models\Idea;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RateLimitingTest extends TestCase
{
    use RefreshDatabase;

    private function makeVerifiedUser(): User
    {
        return User::forceCreate([
            'name'              => 'Test Rate Limit User',
            'email'             => 'ratelimit_' . uniqid() . '@example.com',
            'password'          => bcrypt('Password123!'),
            'role'              => 'developer',
            'roles'             => ['developer'],
            'kyc_status'        => 'approved',
            'email_verified_at' => now(),
        ]);
    }

    public function test_network_start_rate_limiting_triggers_429(): void
    {
        $user1 = $this->makeVerifiedUser();
        $user2 = $this->makeVerifiedUser();

        $this->actingAs($user1);

        // Limit is 5 per minute
        for ($i = 0; $i < 5; $i++) {
            $response = $this->post('/network/start', [
                'recipient_id' => $user2->id,
                'body'         => "Test message {$i}",
            ]);
            $this->assertNotEquals(429, $response->getStatusCode());
        }

        // 6th request must trigger 429 Too Many Requests
        $response = $this->post('/network/start', [
            'recipient_id' => $user2->id,
            'body'         => "Over limit message",
        ]);

        $response->assertStatus(429);
    }

    public function test_network_reply_rate_limiting_triggers_429(): void
    {
        $user1 = $this->makeVerifiedUser();
        $user2 = $this->makeVerifiedUser();

        $this->actingAs($user1);

        // Limit is 20 per minute
        for ($i = 0; $i < 20; $i++) {
            $response = $this->post("/network/{$user2->id}/reply", [
                'body' => "Reply message {$i}",
            ]);
            $this->assertNotEquals(429, $response->getStatusCode());
        }

        // 21st request must trigger 429 Too Many Requests
        $response = $this->post("/network/{$user2->id}/reply", [
            'body' => "Over limit reply",
        ]);

        $response->assertStatus(429);
    }

    public function test_idea_comment_rate_limiting_triggers_429(): void
    {
        $user = $this->makeVerifiedUser();
        $idea = Idea::create([
            'user_id'     => $user->id,
            'title'       => 'Test Idea for Commenting Rate Limit',
            'category'    => 'Tech',
            'description' => 'Description',
            'status'      => 'published',
        ]);

        $this->actingAs($user);

        // Limit is 10 per minute
        for ($i = 0; $i < 10; $i++) {
            $response = $this->post("/ideas/{$idea->id}/comment", [
                'body' => "Comment {$i}",
            ]);
            $this->assertNotEquals(429, $response->getStatusCode());
        }

        // 11th request must trigger 429
        $response = $this->post("/ideas/{$idea->id}/comment", [
            'body' => "Over limit comment",
        ]);

        $response->assertStatus(429);
    }

    public function test_kyc_submission_rate_limiting_triggers_429(): void
    {
        $user = $this->makeVerifiedUser();
        $this->actingAs($user);

        // Limit is 3 per minute
        for ($i = 0; $i < 3; $i++) {
            $response = $this->post('/verification/kyc', [
                'doc_type' => 'national_id',
                'purpose'  => 'publish_idea',
            ]);
            $this->assertNotEquals(429, $response->getStatusCode());
        }

        // 4th request must trigger 429
        $response = $this->post('/verification/kyc', [
            'doc_type' => 'national_id',
            'purpose'  => 'publish_idea',
        ]);

        $response->assertStatus(429);
    }
}
