<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Treasury\PaymentMethod;
use App\Models\User;
use Illuminate\Database\Seeder;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get admin user
        $adminUser = User::where('email', 'rgasperi@smartcarebilling.com')->first()
            ?? User::where('email', 'atenorio@smartcarebilling.com')->first()
            ?? User::whereHas('roles', fn ($q) => $q->where('name', 'admin'))->first()
            ?? User::first();

        if (! $adminUser) {
            $this->command->warn('No admin user found. Skipping PaymentMethodSeeder.');

            return;
        }

        $paymentMethods = [
            [
                'name' => 'Efectivo',
                'destination_type' => 'cash',
            ],
            [
                'name' => 'Tarjeta Crédito',
                'destination_type' => 'bank',
            ],
            [
                'name' => 'Tarjeta Débito',
                'destination_type' => 'bank',
            ],
            [
                'name' => 'ACH',
                'destination_type' => 'bank',
            ],
            [
                'name' => 'Transferencia',
                'destination_type' => 'bank',
            ],
            [
                'name' => 'Yappy',
                'destination_type' => 'bank',
            ],
            [
                'name' => 'Cheque',
                'destination_type' => 'bank',
            ],
        ];

        foreach (Client::all() as $client) {
            $createdCount = 0;

            foreach ($paymentMethods as $data) {
                // Check if payment method already exists
                if (PaymentMethod::where('client_id', $client->id)
                    ->where('name', $data['name'])
                    ->exists()) {
                    continue;
                }

                PaymentMethod::create([
                    'client_id' => $client->id,
                    'name' => $data['name'],
                    'destination_type' => $data['destination_type'],
                    'status' => 'active',
                    'created_by' => $adminUser->id,
                    'updated_by' => $adminUser->id,
                ]);

                $createdCount++;
            }

            if ($createdCount > 0) {
                $this->command->info("Created {$createdCount} payment methods for client: {$client->name}");
            }
        }

        $this->command->info('PaymentMethodSeeder completed successfully.');
    }
}
