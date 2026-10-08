<?php

namespace Database\Factories;

use App\Models\Invoice;
use App\Models\Patient;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'invoice_id' => Invoice::factory(),
            'patient_id' => Patient::factory(),
            'payment_number' => 'PAY-'.date('Y-').str_pad(rand(1, 999999), 6, '0', STR_PAD_LEFT),
            'amount' => $this->faker->numberBetween(100, 10000),
            'payment_date' => $this->faker->dateTime(),
            'payment_method' => $this->faker->randomElement(['cash', 'credit_card', 'debit_card', 'bank_transfer', 'check', 'online', 'insurance', 'other']),
            'reference_number' => $this->faker->uuid(),
            'transaction_id' => $this->faker->uuid(),
            'status' => $this->faker->randomElement(['pending', 'completed', 'failed', 'cancelled', 'refunded']),
            'notes' => $this->faker->sentence(),
            'metadata' => [],
        ];
    }
}
