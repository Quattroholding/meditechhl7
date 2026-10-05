<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\CostCenter;
use Illuminate\Database\Seeder;

class CostCenterSeeder extends Seeder
{
    public function run(): void
    {
        $client = Client::first();
        if (! $client) {
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

        foreach ($costCenters as $data) {
            CostCenter::firstOrCreate(
                ['client_id' => $client->id, 'code' => $data['code']],
                [
                    'name' => $data['name'],
                    'description' => $data['description'],
                    'status' => 'active',
                    'created_by' => 1,
                ]
            );
        }
    }
}
