<?php

namespace Tests\Feature;

use App\Http\Requests\KycSubmissionRequest;
use App\Http\Requests\SaveCvRequest;
use App\Http\Requests\StoreIdeaRequest;
use App\Http\Requests\UpdateIdeaRequest;
use App\Http\Requests\UpdateSettingsRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FormRequestsTest extends TestCase
{
    use RefreshDatabase;

    private function makeUser(): User
    {
        return User::forceCreate([
            'name'              => 'FormRequest User',
            'email'             => 'fr_' . uniqid() . '@example.com',
            'password'          => bcrypt('password123'),
            'role'              => 'idea_owner',
            'kyc_status'        => 'approved',
            'email_verified_at' => now(),
        ]);
    }

    public function test_form_requests_have_authorize_true_when_authenticated(): void
    {
        $user = $this->makeUser();
        $this->actingAs($user);

        $settingsReq = new UpdateSettingsRequest();
        $this->assertTrue($settingsReq->authorize());

        $storeIdeaReq = new StoreIdeaRequest();
        $this->assertTrue($storeIdeaReq->authorize());

        $updateIdeaReq = new UpdateIdeaRequest();
        $this->assertTrue($updateIdeaReq->authorize());

        $kycReq = new KycSubmissionRequest();
        $this->assertTrue($kycReq->authorize());

        $saveCvReq = new SaveCvRequest();
        $this->assertTrue($saveCvReq->authorize());
    }

    public function test_update_settings_request_validates_required_and_unique_fields(): void
    {
        $user = $this->makeUser();
        $this->actingAs($user);

        $response = $this->post('/settings', [
            'name'  => '',
            'email' => 'invalid-email',
        ]);

        $response->assertSessionHasErrors(['name', 'email']);
    }
}
