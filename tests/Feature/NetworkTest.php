<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NetworkTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_cannot_reply_to_self(): void
    {
        $user = User::create([
            'name'              => 'User1',
            'email'             => 'user1@example.com',
            'password'          => bcrypt('password123'),
            'role'              => 'developer',
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($user)->post("/network/{$user->id}/reply", [
            'body' => 'رسالة لنفسي',
        ]);

        // Intentional UX: redirect + popup instead of raw 403 page
        $response->assertRedirect(route('network.index'));
        $response->assertSessionHas('popup');
    }

    public function test_user_can_send_message_to_another_user(): void
    {
        $user1 = User::create([
            'name'              => 'User1',
            'email'             => 'user1@example.com',
            'password'          => bcrypt('password123'),
            'role'              => 'developer',
            'email_verified_at' => now(),
        ]);

        $user2 = User::create([
            'name'              => 'User2',
            'email'             => 'user2@example.com',
            'password'          => bcrypt('password123'),
            'role'              => 'idea_owner',
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($user1)->post('/network/start', [
            'recipient_id' => $user2->id,
            'body'         => 'مرحباً بك',
        ]);

        $response->assertRedirect(route('network.index', ['with' => $user2->id]));
        $this->assertDatabaseHas('messages', [
            'sender_id'    => $user1->id,
            'recipient_id' => $user2->id,
            'body'         => 'مرحباً بك',
        ]);
    }
}
