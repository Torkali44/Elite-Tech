<?php

namespace Tests\Feature;

use App\Models\Idea;
use App\Models\ImplementRequest;
use App\Models\User;
use App\Models\Verification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * TEST-02 — Security Test Coverage
 *
 * Covers the security scenarios identified in the audit:
 *  1. User A cannot access User B's KYC documents (admin route IDOR)
 *  2. Normal user cannot access /admin routes
 *  3. User A cannot edit User B's idea (IDOR)
 *  4. User A cannot respond to User B's implementation request (IDOR)
 *  5. SVG avatar upload rejected
 *  6. Fake/non-image file rejected
 *  7. Legacy OTP brute-force protection (HIGH-03)
 *  8. Role manipulation cannot escalate privileges via path-selection
 *  9. Idea mass-assignment — user cannot set status/admin_notes via request (HIGH-04)
 */
class SecurityTest extends TestCase
{
    use RefreshDatabase;

    // ──────────────────────────────────────────────────────────────────────────
    // Helpers
    // ──────────────────────────────────────────────────────────────────────────

    private function makeVerifiedUser(array $attrs = []): User
    {
        return User::forceCreate(array_merge([
            'name'              => 'Test User',
            'email'             => 'testuser_' . uniqid() . '@example.com',
            'password'          => bcrypt('Password123!'),
            'role'              => 'developer',
            'roles'             => ['developer'],
            'kyc_status'        => 'approved',
            'email_verified_at' => now(),
        ], $attrs));
    }

    private function makeVerifiedIdeaOwner(): User
    {
        return $this->makeVerifiedUser([
            'role'  => 'idea_owner',
            'roles' => ['idea_owner'],
        ]);
    }

    /** Create an idea directly bypassing $fillable for test setup */
    private function makeIdea(User $owner, string $status = 'draft'): Idea
    {
        $idea = new Idea([
            'user_id'     => $owner->id,
            'title'       => 'Test Idea',
            'category'    => 'Tech',
            'description' => 'Description',
        ]);
        $idea->status = $status;
        $idea->save();

        return $idea;
    }

    // ──────────────────────────────────────────────────────────────────────────
    // 1. KYC document IDOR — admin route requires admin session
    // ──────────────────────────────────────────────────────────────────────────

    public function test_normal_user_cannot_access_kyc_document_via_admin_route(): void
    {
        $normalUser = $this->makeVerifiedUser();
        $targetUser = $this->makeVerifiedUser();

        Verification::create([
            'user_id'  => $targetUser->id,
            'doc_type' => 'national_id',
            'purpose'  => 'publish_idea',
            'id_front' => 'kyc/' . $targetUser->id . '/fake_front.jpg',
            'status'   => 'pending',
        ]);

        // Non-admin user tries to access admin KYC file route
        $response = $this->actingAs($normalUser)
            ->get("/admin/verifications/{$targetUser->id}/file/id_front");

        // Must be blocked — admin.auth middleware redirects to /login
        $response->assertRedirect('/login');
    }

    // ──────────────────────────────────────────────────────────────────────────
    // 2. Admin route isolation — non-admin user blocked
    // ──────────────────────────────────────────────────────────────────────────

    public function test_non_admin_user_cannot_access_admin_dashboard(): void
    {
        $user = $this->makeVerifiedUser();

        $response = $this->actingAs($user)->get('/admin/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_non_admin_user_cannot_access_admin_users_list(): void
    {
        $user = $this->makeVerifiedUser();

        $response = $this->actingAs($user)->get('/admin/users');
        $response->assertRedirect('/login');
    }

    public function test_guest_cannot_access_admin_dashboard(): void
    {
        $response = $this->get('/admin/dashboard');
        $response->assertRedirect('/login');
    }

    // ──────────────────────────────────────────────────────────────────────────
    // 3. Idea IDOR — User A cannot edit User B's idea
    // ──────────────────────────────────────────────────────────────────────────

    public function test_user_cannot_edit_another_users_idea(): void
    {
        $owner    = $this->makeVerifiedIdeaOwner();
        $attacker = $this->makeVerifiedIdeaOwner();

        $idea = $this->makeIdea($owner, 'draft');

        // Attacker tries to GET edit page for owner's idea
        $response = $this->actingAs($attacker)->get("/ideas/{$idea->id}/edit");
        $response->assertStatus(404);
    }

    public function test_user_cannot_submit_update_for_another_users_idea(): void
    {
        $owner    = $this->makeVerifiedIdeaOwner();
        $attacker = $this->makeVerifiedIdeaOwner();

        $idea = $this->makeIdea($owner, 'draft');

        // Attacker tries to PUT update to owner's idea
        $response = $this->actingAs($attacker)->put("/ideas/{$idea->id}", [
            'title'        => 'Hacked Title',
            'summary'      => 'Summary here',
            'problem'      => 'This is a problem statement',
            'solution'     => 'This is a solution statement',
            'category'     => 'Tech',
            'intent'       => 'draft',
            'ip_agreement' => '1',
        ]);
        $response->assertStatus(404);

        // Verify the idea was NOT modified
        $this->assertDatabaseHas('ideas', [
            'id'    => $idea->id,
            'title' => 'Test Idea',
        ]);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // 4. Implementation request IDOR — User A cannot respond to User B's request
    // ──────────────────────────────────────────────────────────────────────────

    public function test_user_cannot_respond_to_another_users_implementation_request(): void
    {
        $ideaOwner = $this->makeVerifiedIdeaOwner();
        $developer = $this->makeVerifiedUser();
        $attacker  = $this->makeVerifiedIdeaOwner();

        $idea = $this->makeIdea($ideaOwner, 'published');

        $implRequest = ImplementRequest::create([
            'idea_id' => $idea->id,
            'user_id' => $developer->id,
            'via'     => 'elite_tech',
            'status'  => 'pending',
        ]);

        $response = $this->actingAs($attacker)
            ->post("/dashboard/implement-requests/{$implRequest->id}/respond", [
                'action' => 'approved',
            ]);

        $response->assertStatus(403);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // 5 & 6. Avatar upload — SVG and fake files rejected (FILE-01)
    // ──────────────────────────────────────────────────────────────────────────

    public function test_svg_avatar_upload_is_rejected(): void
    {
        Storage::fake('public');

        $user = $this->makeVerifiedUser();

        $svg = UploadedFile::fake()->create('avatar.svg', 10, 'image/svg+xml');

        $response = $this->actingAs($user)->post('/settings', [
            'name'   => $user->name,
            'email'  => $user->email,
            'avatar' => $svg,
        ]);

        $response->assertSessionHasErrors('avatar');
    }

    public function test_non_image_file_avatar_upload_is_rejected(): void
    {
        Storage::fake('public');

        $user = $this->makeVerifiedUser();

        $fakeFile = UploadedFile::fake()->create('shell.php', 5, 'application/octet-stream');

        $response = $this->actingAs($user)->post('/settings', [
            'name'   => $user->name,
            'email'  => $user->email,
            'avatar' => $fakeFile,
        ]);

        $response->assertSessionHasErrors('avatar');
    }

    public function test_valid_jpg_avatar_is_accepted(): void
    {
        Storage::fake('public');

        $user = $this->makeVerifiedUser();

        $jpg = UploadedFile::fake()->image('avatar.jpg', 200, 200);

        $response = $this->actingAs($user)->post('/settings', [
            'name'   => $user->name,
            'email'  => $user->email,
            'avatar' => $jpg,
        ]);

        $response->assertRedirect();
        $this->assertFalse(
            session()->has('errors') && session('errors')->has('avatar'),
            'Valid JPG avatar should be accepted without errors'
        );
    }

    // ──────────────────────────────────────────────────────────────────────────
    // 7. Legacy OTP brute-force protection (HIGH-03)
    // ──────────────────────────────────────────────────────────────────────────

    public function test_legacy_otp_blocks_after_five_wrong_attempts(): void
    {
        $this->withoutMiddleware(ThrottleRequests::class);

        $user = $this->makeVerifiedUser(['email_verified_at' => null]);

        $this->actingAs($user)->get('/auth/verify');

        // Submit wrong code 5 times — each returns errors
        for ($i = 1; $i <= 5; $i++) {
            $response = $this->post('/auth/verify', ['code' => '999999']);
            $response->assertSessionHasErrors('code');
        }
    }

    public function test_legacy_otp_lockout_redirects_to_login_when_attempts_exceed_max(): void
    {
        $this->withoutMiddleware(ThrottleRequests::class);

        $user = $this->makeVerifiedUser(['email_verified_at' => null]);

        $this->actingAs($user)->get('/auth/verify');

        // Perform 5 failed attempts
        for ($i = 1; $i <= 5; $i++) {
            $this->post('/auth/verify', ['code' => '999999']);
        }

        // 6th attempt — attempt count is at 5, so lockout triggers immediately
        $response = $this->post('/auth/verify', ['code' => '999999']);
        $response->assertRedirect('/login');
        $this->assertGuest();
    }

    public function test_legacy_otp_correct_code_succeeds_and_verifies_email(): void
    {
        $this->withoutMiddleware(ThrottleRequests::class);

        $user = $this->makeVerifiedUser(['email_verified_at' => null]);

        $this->actingAs($user)->get('/auth/verify');

        $knownCode = '123456';
        session(['email_otp_hash' => Hash::make($knownCode)]);

        $response = $this->post('/auth/verify', ['code' => $knownCode]);

        $response->assertRedirect(route('auth.path'));
        $this->assertNotNull($user->fresh()->email_verified_at);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // 8. Role escalation — path selection cannot grant admin role
    // ──────────────────────────────────────────────────────────────────────────

    public function test_user_cannot_escalate_to_admin_via_path_selection(): void
    {
        $user = $this->makeVerifiedUser();

        $response = $this->actingAs($user)->post('/auth/path-selection', [
            'roles' => ['admin'],
        ]);

        $response->assertSessionHasErrors('roles.*');

        $this->assertDatabaseHas('users', [
            'id'   => $user->id,
            'role' => 'developer',
        ]);
    }

    public function test_user_cannot_inject_admin_alongside_valid_roles(): void
    {
        $user = $this->makeVerifiedUser();

        $response = $this->actingAs($user)->post('/auth/path-selection', [
            'roles' => ['developer', 'admin'],
        ]);

        $response->assertSessionHasErrors('roles.*');

        $this->assertDatabaseHas('users', [
            'id'   => $user->id,
            'role' => 'developer',
        ]);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // 9. Idea mass-assignment — user cannot set status/admin_notes (HIGH-04)
    // ──────────────────────────────────────────────────────────────────────────

    public function test_user_cannot_set_idea_status_to_published_via_store(): void
    {
        $owner = $this->makeVerifiedIdeaOwner();

        $response = $this->actingAs($owner)->post('/ideas', [
            'title'        => 'My Idea',
            'summary'      => 'Summary here',
            'problem'      => 'This is a problem description',
            'solution'     => 'This is a solution description',
            'category'     => 'Tech',
            'intent'       => 'draft',
            'ip_agreement' => '1',
            'status'       => 'published',
            'admin_notes'  => 'admin override',
        ]);

        $this->assertDatabaseMissing('ideas', [
            'user_id' => $owner->id,
            'status'  => 'published',
        ]);

        $this->assertDatabaseMissing('ideas', [
            'user_id'     => $owner->id,
            'admin_notes' => 'admin override',
        ]);
    }

    public function test_user_cannot_set_idea_status_to_published_via_update(): void
    {
        $owner = $this->makeVerifiedIdeaOwner();
        $idea  = $this->makeIdea($owner, 'draft');

        $response = $this->actingAs($owner)->put("/ideas/{$idea->id}", [
            'title'       => 'Updated Title',
            'summary'     => 'Updated summary',
            'problem'     => 'This is a problem description',
            'solution'    => 'This is a solution description',
            'category'    => 'Tech',
            'intent'      => 'draft',
            'status'      => 'published',
            'admin_notes' => 'injected notes',
        ]);

        $idea->refresh();

        $this->assertNotEquals('published', $idea->status);
        $this->assertNotEquals('injected notes', $idea->admin_notes);
    }
}
