<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'name'              => $this->faker->name(),
            'email'             => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password'          => 'password',   // hashed by the model cast
            'role'              => 'developer',
            'roles'             => ['developer'],
            'kyc_status'        => 'none',
            'is_suspended'      => false,
            'remember_token'    => Str::random(10),
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function suspended(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_suspended' => true,
            'kyc_status'   => 'suspended',
        ]);
    }

    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role'  => 'admin',
            'roles' => ['admin'],
        ]);
    }
}
