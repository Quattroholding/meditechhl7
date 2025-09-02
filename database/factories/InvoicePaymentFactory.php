<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\InvoicePayment>
 */
class InvoicePaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $paymentDate = $this->faker->dateTimeBetween('-3 months', 'now');
        $postingDate = $this->faker->dateTimeBetween($paymentDate, 'now');
        $paymentSource = $this->faker->randomElement(['patient', 'insurance', 'copay', 'deductible', 'coinsurance', 'adjustment', 'refund']);
        $amount = $this->faker->randomFloat(2, 100, 25000);

        return [
            'invoice_id' => \App\Models\Invoice::factory(),
            'patient_id' => \App\Models\Patient::factory(),
            'insurance_claim_id' => $paymentSource === 'insurance'
                ? \App\Models\InsuranceClaim::factory()
                : null,
            'payment_reference' => 'PAY-'.$this->faker->unique()->regexify('[0-9]{8}'),
            'amount' => $amount,
            'payment_source' => $paymentSource,
            'payment_method' => $this->getPaymentMethodBySource($paymentSource),
            'payment_details' => $this->getPaymentDetails($paymentSource),
            'payment_date' => $paymentDate,
            'posting_date' => $postingDate,
            'transaction_id' => $this->faker->optional()->regexify('[A-Z0-9]{12}'),
            'check_number' => $this->faker->optional()->regexify('[0-9]{6}'),
            'authorization_code' => $this->faker->optional()->regexify('[A-Z0-9]{8}'),
            'status' => $this->faker->randomElement(['pending', 'processed', 'cleared', 'failed', 'refunded', 'voided']),
            'notes' => $this->faker->optional()->sentence(),
            'created_by' => \App\Models\User::factory(),
        ];
    }

    private function getPaymentMethodBySource(string $source): string
    {
        return match ($source) {
            'insurance' => 'insurance_payment',
            'patient', 'copay', 'deductible', 'coinsurance' => $this->faker->randomElement([
                'cash', 'credit_card', 'debit_card', 'check', 'bank_transfer',
            ]),
            'adjustment', 'refund' => 'adjustment',
            default => 'other'
        };
    }

    private function getPaymentDetails(string $source): ?string
    {
        return match ($source) {
            'insurance' => 'Insurance payment via electronic transfer',
            'patient' => $this->faker->randomElement([
                'Cash payment',
                'Card ending in '.$this->faker->numberBetween(1000, 9999),
                'Check #'.$this->faker->numberBetween(100000, 999999),
                'Bank transfer',
            ]),
            'copay' => 'Patient copayment - '.$this->faker->randomElement(['Cash', 'Card']),
            'deductible' => 'Deductible payment',
            'coinsurance' => 'Coinsurance payment',
            'adjustment' => $this->faker->randomElement([
                'Provider discount',
                'Insurance adjustment',
                'Billing correction',
                'Charity care adjustment',
            ]),
            'refund' => 'Refund processed',
            default => null
        };
    }

    public function patientPayment(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_source' => 'patient',
            'payment_method' => $this->faker->randomElement(['cash', 'credit_card', 'debit_card', 'check']),
            'insurance_claim_id' => null,
        ]);
    }

    public function insurancePayment(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_source' => 'insurance',
            'payment_method' => 'insurance_payment',
            'payment_details' => 'Insurance payment via electronic transfer',
        ]);
    }

    public function copayment(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_source' => 'copay',
            'payment_method' => $this->faker->randomElement(['cash', 'credit_card', 'debit_card']),
            'amount' => $this->faker->randomFloat(2, 500, 3000),
            'insurance_claim_id' => null,
        ]);
    }

    public function processed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'processed',
        ]);
    }

    public function cleared(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'cleared',
        ]);
    }

    public function cashPayment(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_method' => 'cash',
            'payment_details' => 'Cash payment',
            'transaction_id' => null,
            'authorization_code' => null,
        ]);
    }

    public function creditCardPayment(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_method' => 'credit_card',
            'payment_details' => 'Card ending in '.$this->faker->numberBetween(1000, 9999),
            'authorization_code' => $this->faker->regexify('[A-Z0-9]{8}'),
        ]);
    }
}
