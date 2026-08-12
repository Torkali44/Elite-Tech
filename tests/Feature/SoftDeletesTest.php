<?php

namespace Tests\Feature;

use App\Models\Idea;
use App\Models\User;
use App\Models\Verification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SoftDeletesTest extends TestCase
{
    use RefreshDatabase;

    private function makeUser(): User
    {
        return User::forceCreate([
            'name'              => 'SoftDelete User',
            'email'             => 'sd_' . uniqid() . '@example.com',
            'password'          => bcrypt('password123'),
            'role'              => 'idea_owner',
            'kyc_status'        => 'approved',
            'email_verified_at' => now(),
        ]);
    }

    public function test_user_soft_delete_and_restore(): void
    {
        $user = $this->makeUser();

        $user->delete();

        $this->assertSoftDeleted('users', ['id' => $user->id]);
        $this->assertNull(User::find($user->id));
        $this->assertNotNull(User::withTrashed()->find($user->id));

        $user->restore();
        $this->assertNotNull(User::find($user->id));
    }

    public function test_idea_soft_delete_and_restore(): void
    {
        $user = $this->makeUser();
        $idea = Idea::forceCreate([
            'user_id'     => $user->id,
            'title'       => 'فكرة تجريبية للـ Soft Delete',
            'category'    => 'AI',
            'description' => 'وصف تفصيلي للفكرة',
            'status'      => 'published',
        ]);

        $idea->delete();

        $this->assertSoftDeleted('ideas', ['id' => $idea->id]);
        $this->assertNull(Idea::find($idea->id));
        $this->assertNotNull(Idea::withTrashed()->find($idea->id));

        $idea->restore();
        $this->assertNotNull(Idea::find($idea->id));
    }

    public function test_verification_soft_delete_and_restore(): void
    {
        $user = $this->makeUser();
        $v = Verification::create([
            'user_id'  => $user->id,
            'doc_type' => 'national_id',
            'purpose'  => 'publish_idea',
            'id_front' => 'kyc/test.jpg',
            'status'   => 'pending',
        ]);

        $v->delete();

        $this->assertSoftDeleted('verifications', ['id' => $v->id]);
        $this->assertNull(Verification::find($v->id));
        $this->assertNotNull(Verification::withTrashed()->find($v->id));
    }
}
