<?php

namespace Database\Factories;

use App\Enums\SubscriptionStatus;
use App\Models\Client;
use App\Models\ClientSubscription;
use App\Models\Package;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ClientSubscription>
 */
class ClientSubscriptionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $periodStart = now();
        $periodEnd = $periodStart->copy()->addDays(30);

        return [
            'client_id' => Client::factory(),
            'package_id' => Package::factory(),
            'status' => SubscriptionStatus::ACTIVE,
            'trial_ends_at' => null,
            'current_period_starts_at' => $periodStart,
            'current_period_ends_at' => $periodEnd,
            'next_billing_date' => $periodEnd,
            'cancelled_at' => null,
            'cancellation_reason' => null,
            'paused_at' => null,
            'extra_doctors_count' => 0,
            'metadata' => [],
        ];
    }

    /**
     * Indicate that the subscription is in trial period.
     */
    public function trial(int $days = 14): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => SubscriptionStatus::TRIAL,
            'trial_ends_at' => now()->addDays($days),
            'next_billing_date' => now()->addDays($days),
        ]);
    }

    /**
     * Indicate that the subscription is past due.
     */
    public function pastDue(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => SubscriptionStatus::PAST_DUE,
            'next_billing_date' => now()->subDays(1),
        ]);
    }

    /**
     * Indicate that the subscription is suspended.
     */
    public function suspended(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => SubscriptionStatus::SUSPENDED,
        ]);
    }

    /**
     * Indicate that the subscription is cancelled.
     */
    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => SubscriptionStatus::CANCELLED,
            'cancelled_at' => now(),
            'cancellation_reason' => 'Test cancellation',
        ]);
    }

    /**
     * Indicate that the subscription needs billing.
     */
    public function needsBilling(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => SubscriptionStatus::ACTIVE,
            'next_billing_date' => now()->subDay(),
        ]);
    }
}
