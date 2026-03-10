<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'dni' => fake()->unique()->numerify('########'),
            'role' => 'Auxiliar',
            'is_active' => true,
        ];
    }

    /**
     * Configure the model factory.
     */
    public function configure(): static
    {
        return $this->afterCreating(function (\App\Models\User $user) {
            if ($user->role && \Spatie\Permission\Models\Role::where('name', $user->role)->exists()) {
                $user->syncRoles([$user->role]);
            }
        });
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn(array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function superAdmin(): static
    {
        return $this->state(fn() => ['role' => 'SuperAdmin']);
    }

    public function administrador(): static
    {
        return $this->state(fn() => ['role' => 'Administrador']);
    }

    public function director(): static
    {
        return $this->state(fn() => ['role' => 'Director']);
    }

    public function docente(): static
    {
        return $this->state(fn() => ['role' => 'Docente']);
    }

    public function inactive(): static
    {
        return $this->state(fn() => ['is_active' => false]);
    }
}
