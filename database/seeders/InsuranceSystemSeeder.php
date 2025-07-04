<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\InsuranceCompany;
use App\Models\Patient;
use App\Models\PatientInsurancePolicy;
use App\Models\Invoice;
use App\Models\InsuranceClaim;
use App\Models\InvoicePayment;
use App\Models\Encounter;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InsuranceSystemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Seeding Insurance System...');

        // Get existing clients
        $clients = Client::all();
        
        if ($clients->isEmpty()) {
            $this->command->warn('No clients found. Creating a test client...');
            $client = Client::factory()->create([
                'name' => 'Centro Médico Demo',
                'code' => 'CMD-001'
            ]);
            $clients = collect([$client]);
        }

        foreach ($clients as $client) {
            $this->command->info("Creating insurance data for client: {$client->name}");
            
            // Create insurance companies for this client
            $insuranceCompanies = InsuranceCompany::factory()
                ->count(5)
                ->active()
                ->create(['client_id' => $client->id]);

            // Get patients for this client
            $patients = Patient::whereHas('clients', function($query) use ($client) {
                $query->where('client_id', $client->id);
            })->limit(20)->get();

            if ($patients->isEmpty()) {
                $this->command->warn("No patients found for client {$client->name}. Creating test patients...");
                
                // Create patients manually to avoid factory issues
                $patients = collect();
                for ($i = 0; $i < 10; $i++) {
                    $patient = Patient::factory()->make();
                    $patient->save();
                    $patient->clients()->attach($client->id);
                    $patients->push($patient);
                }
            }

            foreach ($patients as $patient) {
                // Give 80% of patients insurance
                if (rand(1, 100) <= 80) {
                    // Primary insurance for most patients
                    $primaryInsurance = PatientInsurancePolicy::factory()
                        ->primary()
                        ->active()
                        ->forSelf()
                        ->create([
                            'patient_id' => $patient->id,
                            'insurance_company_id' => $insuranceCompanies->random()->id,
                        ]);

                    // 30% chance of secondary insurance
                    if (rand(1, 100) <= 30) {
                        PatientInsurancePolicy::factory()
                            ->secondary()
                            ->active()
                            ->create([
                                'patient_id' => $patient->id,
                                'insurance_company_id' => $insuranceCompanies->random()->id,
                            ]);
                    }

                    // Create encounters and invoices for patients with insurance
                    $encounters = Encounter::where('patient_id', $patient->id)->limit(3)->get();
                    
                    if ($encounters->isEmpty()) {
                        $encounters = collect();
                        for ($j = 0; $j < 2; $j++) {
                            $encounter = Encounter::factory()->make([
                                'patient_id' => $patient->id,
                                'client_id' => $client->id,
                            ]);
                            $encounter->save();
                            $encounters->push($encounter);
                        }
                    }

                    foreach ($encounters as $encounter) {
                        // Create invoice for encounter
                        $invoice = Invoice::factory()->create([
                            'patient_id' => $patient->id,
                            'encounter_id' => $encounter->id,
                            'client_id' => $client->id,
                            'primary_insurance_id' => $primaryInsurance->id,
                            'has_insurance' => true,
                            'insurance_status' => 'submitted',
                            'insurance_billed_amount' => rand(10000, 80000),
                            'patient_copay_amount' => rand(1000, 3000),
                            'patient_deductible_amount' => rand(0, 5000),
                        ]);

                        // Create insurance claim
                        $claim = InsuranceClaim::factory()
                            ->create([
                                'patient_insurance_policy_id' => $primaryInsurance->id,
                                'invoice_id' => $invoice->id,
                                'encounter_id' => $encounter->id,
                            ]);

                        // Create payments based on claim status
                        if ($claim->status === 'paid') {
                            // Insurance payment
                            InvoicePayment::factory()
                                ->insurancePayment()
                                ->processed()
                                ->create([
                                    'invoice_id' => $invoice->id,
                                    'patient_id' => $patient->id,
                                    'insurance_claim_id' => $claim->id,
                                    'amount' => $claim->paid_amount,
                                ]);

                            // Patient copay
                            InvoicePayment::factory()
                                ->copayment()
                                ->processed()
                                ->create([
                                    'invoice_id' => $invoice->id,
                                    'patient_id' => $patient->id,
                                    'amount' => $invoice->patient_copay_amount,
                                ]);
                        } elseif ($claim->status === 'approved') {
                            // Only patient copay paid so far
                            InvoicePayment::factory()
                                ->copayment()
                                ->processed()
                                ->create([
                                    'invoice_id' => $invoice->id,
                                    'patient_id' => $patient->id,
                                    'amount' => $invoice->patient_copay_amount,
                                ]);
                        }

                        // Update invoice amounts
                        $invoice->updateInsuranceAmounts();
                    }
                }
            }
        }

        $this->command->info('Insurance System seeding completed!');
        $this->displayStatistics();
    }

    private function displayStatistics(): void
    {
        $stats = [
            'Insurance Companies' => InsuranceCompany::count(),
            'Patient Insurance Policies' => PatientInsurancePolicy::count(),
            'Active Policies' => PatientInsurancePolicy::active()->count(),
            'Primary Insurance Policies' => PatientInsurancePolicy::primary()->count(),
            'Insurance Claims' => InsuranceClaim::count(),
            'Paid Claims' => InsuranceClaim::paid()->count(),
            'Invoice Payments' => InvoicePayment::count(),
            'Insurance Payments' => InvoicePayment::insurancePayments()->count(),
            'Patient Payments' => InvoicePayment::patientPayments()->count(),
        ];

        $this->command->table(['Item', 'Count'], 
            collect($stats)->map(fn($count, $item) => [$item, $count])->toArray()
        );
    }
}
