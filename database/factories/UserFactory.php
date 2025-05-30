<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\User;
use App\Models\UserClient;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;


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
            'first_name' => fake()->name(),
            'last_name' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (User $user) {
            // Asignar rol de paciente por defecto si no tiene ningún rol
            if (!$user->hasAnyRole(Role::all())) {
                $user->assignRole('doctor');
            }
        });
    }

    // States para diferentes roles
    public function asAdmin()
    {
        return $this->afterCreating(function (User $user) {
            $user->assignRole('admin');
        });
    }

    public function asAdminClient()
    {
        return $this->afterCreating(function (User $user) {
            $user->assignRole('admin client');
        });
    }

    public function asDoctor()
    {
        return $this->afterCreating(function (User $user) {
            $user->assignRole('doctor');
        });
    }

    public function asAsistente()
    {
        return $this->afterCreating(function (User $user) {
            $user->assignRole('asistente');
        });
    }

    public function asPatient()
    {
        return $this->afterCreating(function (User $user) {
            $user->assignRole('paciente');
        });
    }

    public function withRole(string $role)
    {
        return $this->afterCreating(function (User $user) use ($role) {
            $user->assignRole($role);
        });
    }

    public function unverified()
    {
        return $this->state(function (array $attributes) {
            return [
                'email_verified_at' => null,
            ];
        });
    }
}
