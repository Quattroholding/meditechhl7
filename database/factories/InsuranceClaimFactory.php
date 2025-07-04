<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\InsuranceClaim>
 */
class InsuranceClaimFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $claimDate = $this->faker->dateTimeBetween('-6 months', 'now');
        $serviceDate = $this->faker->dateTimeBetween('-1 year', $claimDate);
        $billedAmount = $this->faker->randomFloat(2, 5000, 150000);
        $approvedAmount = $this->faker->randomFloat(2, $billedAmount * 0.7, $billedAmount);
        $paidAmount = $this->faker->randomFloat(2, 0, $approvedAmount);
        
        $status = $this->faker->randomElement(['pending', 'submitted', 'processing', 'approved', 'partially_paid', 'paid', 'denied', 'rejected']);
        
        return [
            'patient_insurance_policy_id' => \App\Models\PatientInsurancePolicy::factory(),
            'invoice_id' => \App\Models\Invoice::factory(),
            'encounter_id' => \App\Models\Encounter::factory(),
            'claim_number' => 'CLM-' . $this->faker->unique()->regexify('[0-9]{8}'),
            'claim_date' => $claimDate,
            'service_date' => $serviceDate,
            'billed_amount' => $billedAmount,
            'approved_amount' => $status === 'denied' ? null : $approvedAmount,
            'paid_amount' => in_array($status, ['paid', 'partially_paid']) ? $paidAmount : 0,
            'patient_responsibility' => $this->faker->randomFloat(2, 1000, 5000),
            'copay_amount' => $this->faker->randomFloat(2, 500, 2000),
            'deductible_amount' => $this->faker->randomFloat(2, 0, 3000),
            'coinsurance_amount' => $this->faker->randomFloat(2, 0, 2000),
            'status' => $status,
            'rejection_reason' => $status === 'denied' ? $this->faker->randomElement([
                'Service not covered',
                'Prior authorization required',
                'Out of network provider',
                'Duplicate claim',
                'Missing documentation',
                'Exceeded annual limit'
            ]) : null,
            'rejection_details' => $status === 'denied' ? $this->faker->optional()->paragraph() : null,
            'submitted_date' => in_array($status, ['submitted', 'processing', 'approved', 'paid', 'denied']) 
                ? $this->faker->dateTimeBetween($claimDate, 'now') : null,
            'processed_date' => in_array($status, ['approved', 'paid', 'denied']) 
                ? $this->faker->dateTimeBetween($claimDate, 'now') : null,
            'payment_date' => $status === 'paid' 
                ? $this->faker->dateTimeBetween($claimDate, 'now') : null,
            'authorization_number' => $this->faker->optional()->regexify('[A-Z]{2}[0-9]{6}'),
            'diagnosis_codes' => $this->generateDiagnosisCodes(),
            'procedure_codes' => $this->generateProcedureCodes(),
            'notes' => $this->faker->optional()->paragraph(),
        ];
    }

    private function generateDiagnosisCodes(): array
    {
        $commonCodes = [
            'Z00.00' => 'Encounter for general adult medical examination without abnormal findings',
            'I10' => 'Essential hypertension',
            'E11.9' => 'Type 2 diabetes mellitus without complications',
            'Z51.11' => 'Encounter for antineoplastic chemotherapy',
            'M25.50' => 'Pain in unspecified joint',
            'R06.02' => 'Shortness of breath',
            'K21.9' => 'Gastro-esophageal reflux disease without esophagitis',
            'F32.9' => 'Major depressive disorder, single episode, unspecified',
            'M54.5' => 'Low back pain',
            'R50.9' => 'Fever, unspecified'
        ];

        $selectedCodes = $this->faker->randomElements($commonCodes, $this->faker->numberBetween(1, 3), false);
        
        return array_map(function($code, $description) {
            return ['code' => $code, 'description' => $description];
        }, array_keys($selectedCodes), $selectedCodes);
    }

    private function generateProcedureCodes(): array
    {
        $commonCodes = [
            '99213' => 'Office visit, established patient, level 3',
            '99214' => 'Office visit, established patient, level 4',
            '99203' => 'Office visit, new patient, level 3',
            '80053' => 'Comprehensive metabolic panel',
            '85025' => 'Complete blood count',
            '93000' => 'Electrocardiogram',
            '71020' => 'Chest X-ray',
            '36415' => 'Venipuncture',
            '12001' => 'Simple repair of superficial wounds',
            '90471' => 'Immunization administration'
        ];

        $selectedCodes = $this->faker->randomElements($commonCodes, $this->faker->numberBetween(1, 2), false);
        
        return array_map(function($code, $description) {
            return ['code' => $code, 'description' => $description];
        }, array_keys($selectedCodes), $selectedCodes);
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
            'submitted_date' => null,
            'processed_date' => null,
            'payment_date' => null,
        ]);
    }

    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'approved',
            'submitted_date' => $this->faker->dateTimeBetween('-1 month', '-1 week'),
            'processed_date' => $this->faker->dateTimeBetween('-1 week', 'now'),
        ]);
    }

    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'paid',
            'submitted_date' => $this->faker->dateTimeBetween('-2 months', '-1 month'),
            'processed_date' => $this->faker->dateTimeBetween('-1 month', '-1 week'),
            'payment_date' => $this->faker->dateTimeBetween('-1 week', 'now'),
            'paid_amount' => $attributes['approved_amount'] ?? $attributes['billed_amount'],
        ]);
    }

    public function denied(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'denied',
            'approved_amount' => null,
            'paid_amount' => 0,
            'rejection_reason' => 'Service not covered by policy',
            'rejection_details' => 'The requested service is not included in the current coverage plan.',
        ]);
    }
}
