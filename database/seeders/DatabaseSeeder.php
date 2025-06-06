<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RolesSeeder::class);
        $this->call(UserAdminSeeder::class);
        $this->call(CptAreaSeeder::class);
        $this->call(CptSeeder::class);
        $this->call(MedicalSpecialitySeeder::class);
        $this->call(PresentIllnesTypesSeeder::class);
        $this->call(ClinicalObservationTypeSeeder::class);
        $this->call(MedicineSeeder::class);
        $this->call(DiagnosticSeeder::class);
        $this->call(EncounterSecctionSeeder::class);
        //$this->call(UserSeeder::class);
    }
}
