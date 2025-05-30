<?php

namespace Database\Factories;

use App\Models\Branch;
use App\Models\Client;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Client>
 */
class ClientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'ruc' => fake()->randomNumber(7),
            'dv' => fake()->numberBetween(10,99),
            'long_name' =>fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'whatsapp' => fake()->phoneNumber,
            'logo'=>fake()->imageUrl(),
        ];
    }

    /**
     * Configure the model factory.
     */
    public function configure(): static
    {
        return $this->afterCreating(function (Client $c) {

            $gender = $this->faker->randomElement(['male', 'female']);
            $givenName = $gender === 'male' ? $this->faker->firstNameMale : $this->faker->firstNameFemale;
            $last_name = $this->faker->lastName;

            $user = User::factory()
                ->asAdminClient()
                ->create([
                    'first_name' =>$givenName,
                    'last_name' => $last_name,
                    'email' => substr($givenName,0,1).$last_name.'@clinica.com'
                ]);

            Branch::factory()->count(1)->create(['client_id'=>$c->id]);
        });
    }
}
