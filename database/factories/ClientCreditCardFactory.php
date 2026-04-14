<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\ClientCreditCard;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ClientCreditCard>
 */
class ClientCreditCardFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $lastFour = str_pad(fake()->numberBetween(0, 9999), 4, '0', STR_PAD_LEFT);

        return [
            'client_id' => Client::factory(),
            'neopayments_customer_id' => 'cust_'.fake()->uuid(),
            'neopayments_card_id' => 'card_'.fake()->uuid(),
            'card_token' => 'tok_'.fake()->uuid(),
            'card_holder' => fake()->name(),
            'card_last_four' => $lastFour,
            'card_brand' => fake()->randomElement(['Visa', 'Mastercard', 'American Express']),
            'exp_month' => str_pad(fake()->numberBetween(1, 12), 2, '0', STR_PAD_LEFT),
            'exp_year' => str_pad(fake()->numberBetween(26, 35), 2, '0', STR_PAD_LEFT),
            'is_default' => false,
            'is_active' => true,
            'alias' => null,
            'metadata' => [
                'account_type' => fake()->randomElement(['Visa', 'Mastercard']),
                'card_id' => 'card_'.fake()->uuid(),
            ],
        ];
    }

    /**
     * Indicate that the card is the default card.
     */
    public function default(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_default' => true,
        ]);
    }

    /**
     * Indicate that the card is expired.
     */
    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'exp_month' => '01',
            'exp_year' => '20',
        ]);
    }

    /**
     * Indicate that the card is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
