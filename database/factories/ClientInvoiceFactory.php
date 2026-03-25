<?php

namespace Database\Factories;

use App\Enums\InvoiceStatus;
use App\Models\Client;
use App\Models\ClientInvoice;
use App\Models\ClientSubscription;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ClientInvoice>
 */
class ClientInvoiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $subtotal = fake()->randomFloat(2, 50, 500);
        $taxRate = 0.07;
        $taxAmount = $subtotal * $taxRate;
        $total = $subtotal + $taxAmount;

        return [
            'client_id' => Client::factory(),
            'subscription_id' => ClientSubscription::factory(),
            'subtotal' => $subtotal,
            'discount_amount' => 0,
            'discount_percentage' => 0,
            'discount_reason' => null,
            'tax_rate' => $taxRate,
            'tax_amount' => $taxAmount,
            'total' => $total,
            'status' => InvoiceStatus::PENDING,
            'due_date' => now()->addDays(7),
            'paid_at' => null,
            'period_start' => now(),
            'period_end' => now()->addDays(30),
            'notes' => null,
            'metadata' => [],
        ];
    }

    /**
     * Indicate that the invoice is overdue.
     */
    public function overdue(int $days = 8): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => InvoiceStatus::OVERDUE,
            'due_date' => now()->subDays($days),
        ]);
    }

    /**
     * Indicate that the invoice is paid.
     */
    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => InvoiceStatus::PAID,
            'paid_at' => now(),
        ]);
    }
}
