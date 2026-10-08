<?php

namespace Database\Factories;

use App\Models\Invoice;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Invoice>
 */
class InvoiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $subtotal = $this->faker->numberBetween(1000, 10000);
        $tax = $subtotal * 0.07;
        $total = $subtotal + $tax;

        return [
            'patient_id' => Patient::factory(),
            'invoice_number' => 'INV-'.date('Y-').str_pad(rand(1, 999999), 6, '0', STR_PAD_LEFT),
            'status' => $this->faker->randomElement(['draft', 'issued', 'balanced', 'cancelled']),
            'date' => $this->faker->dateTime(),
            'issue_date' => $this->faker->dateTime(),
            'due_date' => $this->faker->dateTimeBetween('now', '+30 days'),
            'currency' => 'USD',
            'subtotal_amount' => $subtotal,
            'tax_amount' => $tax,
            'total_amount' => $total,
            'total_preTax' => $subtotal,
            'total_tax' => $tax,
            'total_gross' => $total,
            'total_net' => $total,
            'payment_status' => $this->faker->randomElement(['paid', 'partial', 'pending', 'overdue', 'cancelled']),
            'amount_paid' => 0,
            'amount_due' => $total,
            'type' => 'invoice',
        ];
    }
}
