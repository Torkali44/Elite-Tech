<?php

namespace Tests\Feature;

use App\Mail\OtpMail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AsyncEmailQueueTest extends TestCase
{
    use RefreshDatabase;

    public function test_otp_email_is_pushed_to_queue(): void
    {
        Mail::fake();

        $response = $this->post('/register', [
            'name'                  => 'Queue User',
            'email'                 => 'queue_' . uniqid() . '@example.com',
            'password'              => 'Password123!',
            'password_confirmation' => 'Password123!',
            'terms'                 => '1',
        ]);

        $response->assertRedirect('/auth/verify');

        // Verify that OtpMail was queued, NOT sent synchronously
        Mail::assertQueued(OtpMail::class);
    }
}
