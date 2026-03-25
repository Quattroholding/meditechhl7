<?php

namespace Database\Seeders;

use App\Models\MedicationType;
use Illuminate\Database\Seeder;

class MedicationTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        MedicationType::create(['name' => 'Botella']);
        MedicationType::create(['name' => 'Cajas']);
        MedicationType::create(['name' => 'Crema']);
        MedicationType::create(['name' => 'Gel']);
        MedicationType::create(['name' => 'Gotas']);
        MedicationType::create(['name' => 'Inhalador']);
        MedicationType::create(['name' => 'Ovulos']);
        MedicationType::create(['name' => 'Parches']);
        MedicationType::create(['name' => 'Sobre']);
        MedicationType::create(['name' => 'S/P']);
        MedicationType::create(['name' => 'Spray']);
        MedicationType::create(['name' => 'Suspensión']);
        MedicationType::create(['name' => 'Tabletas']);
        MedicationType::create(['name' => 'Tubo']);
        MedicationType::create(['name' => 'Unguento']);
        MedicationType::create(['name' => 'Unidades']);
        MedicationType::create(['name' => 'Vial']);
        MedicationType::create(['name' => 'Supositorio']);
        MedicationType::create(['name' => 'Pluma precargada']);
        MedicationType::create(['name' => 'Masticables']);
    }
}
