<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\CostCenter;
use App\Models\User;
use Illuminate\Database\Seeder;

class CostCenterSeeder extends Seeder
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
            $this->command->warn('No admin user found. Skipping CostCenterSeeder.');

            return;
        }

        $costCenters = [
            ['code' => '100', 'name' => 'Medicina General', 'description' => 'Servicios de medicina general'],
            ['code' => '200', 'name' => 'Pediatría', 'description' => 'Servicios pediátricos'],
            ['code' => '300', 'name' => 'Dermatología', 'description' => 'Servicios dermatológicos'],
            ['code' => '400', 'name' => 'Estética', 'description' => 'Servicios estéticos'],
            ['code' => '500', 'name' => 'Laboratorio', 'description' => 'Servicios de laboratorio'],
            ['code' => '600', 'name' => 'Odontología', 'description' => 'Servicios odontológicos'],
            ['code' => '700', 'name' => 'Radiología', 'description' => 'Servicios de radiología'],
            ['code' => '800', 'name' => 'Administración', 'description' => 'Costos administrativos'],
            ['code' => '900', 'name' => 'Farmacia', 'description' => 'Servicios de farmacia'],
        ];

        foreach (Client::all() as $client) {
            $createdCount = 0;

            foreach ($costCenters as $data) {
                // Check if cost center already exists
                if (CostCenter::where('client_id', $client->id)
                    ->where('code', $data['code'])
                    ->exists()) {
                    continue;
                }

                CostCenter::create([
                    'client_id' => $client->id,
                    'code' => $data['code'],
                    'name' => $data['name'],
                    'description' => $data['description'],
                    'status' => 'active',
                    'created_by' => $adminUser->id,
                ]);

                $createdCount++;
            }

            if ($createdCount > 0) {
                $this->command->info("Created {$createdCount} cost centers for client: {$client->name}");
            }
        }

        $this->command->info('CostCenterSeeder completed successfully.');
    }
}
