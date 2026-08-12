<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MassAssignmentProtectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_administrative_fields_are_guarded_from_mass_assignment(): void
    {
        // Simulate malicious mass-assignment attempt
        $input = [
            'name'              => 'Hacker',
            'email'             => 'hacker@example.com',
            'password'          => bcrypt('password123'),
            'role'              => 'admin',
            'roles'             => ['admin'],
            'kyc_status'        => 'approved',
            'is_suspended'      => true,
            'admin_notes'       => 'Injected Note',
            'email_verified_at' => now(),
        ];

        $user = User::create($input);

        // Standard fillable fields are populated
        $this->assertEquals('Hacker', $user->name);
        $this->assertEquals('hacker@example.com', $user->email);

        // Administrative fields MUST be ignored
        $this->assertNull($user->role);
        $this->assertNull($user->roles);
        $this->assertNull($user->kyc_status);
        $this->assertFalse((bool) $user->is_suspended);
        $this->assertNull($user->admin_notes);
        $this->assertNull($user->email_verified_at);
    }
}
