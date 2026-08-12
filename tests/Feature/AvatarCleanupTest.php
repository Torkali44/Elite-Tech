<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AvatarCleanupTest extends TestCase
{
    use RefreshDatabase;

    private function makeUser(): User
    {
        return User::forceCreate([
            'name'              => 'Avatar Test User',
            'email'             => 'avatar_' . uniqid() . '@example.com',
            'password'          => bcrypt('password123'),
            'role'              => 'developer',
            'email_verified_at' => now(),
        ]);
    }

    public function test_old_avatar_is_deleted_when_updating_via_settings(): void
    {
        Storage::fake('public');

        $user = $this->makeUser();

        // 1. Upload first avatar
        $firstAvatar = UploadedFile::fake()->image('first.jpg', 200, 200);
        $this->actingAs($user)->post('/settings', [
            'name'   => $user->name,
            'email'  => $user->email,
            'avatar' => $firstAvatar,
        ]);

        $firstPath = $user->fresh()->avatar;
        $this->assertNotNull($firstPath);
        Storage::disk('public')->assertExists($firstPath);

        // 2. Upload second avatar
        $secondAvatar = UploadedFile::fake()->image('second.jpg', 200, 200);
        $this->actingAs($user)->post('/settings', [
            'name'   => $user->name,
            'email'  => $user->email,
            'avatar' => $secondAvatar,
        ]);

        $secondPath = $user->fresh()->avatar;
        $this->assertNotNull($secondPath);
        $this->assertNotEquals($firstPath, $secondPath);

        // Old avatar MUST be deleted, new avatar MUST exist
        Storage::disk('public')->assertMissing($firstPath);
        Storage::disk('public')->assertExists($secondPath);
    }

    public function test_old_avatar_is_deleted_when_updating_via_cv_builder(): void
    {
        Storage::fake('public');

        $user = $this->makeUser();

        // 1. Upload first avatar via CV builder
        $firstAvatar = UploadedFile::fake()->image('cv_first.jpg', 200, 200);
        $this->actingAs($user)->post('/profile/cv-builder', [
            'title'  => 'Developer',
            'avatar' => $firstAvatar,
        ]);

        $firstPath = $user->fresh()->avatar;
        $this->assertNotNull($firstPath);
        Storage::disk('public')->assertExists($firstPath);

        // 2. Upload second avatar via CV builder
        $secondAvatar = UploadedFile::fake()->image('cv_second.jpg', 200, 200);
        $this->actingAs($user)->post('/profile/cv-builder', [
            'title'  => 'Developer',
            'avatar' => $secondAvatar,
        ]);

        $secondPath = $user->fresh()->avatar;
        $this->assertNotNull($secondPath);
        $this->assertNotEquals($firstPath, $secondPath);

        // Old avatar MUST be deleted, new avatar MUST exist
        Storage::disk('public')->assertMissing($firstPath);
        Storage::disk('public')->assertExists($secondPath);
    }
}
