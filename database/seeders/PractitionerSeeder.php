<?php

namespace Database\Seeders;

use App\Models\MedicalSpeciality;
use Illuminate\Database\Seeder;

class PractitionerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $especialidades = MedicalSpeciality::get();

        foreach ($especialidades as $e) {
            $doctor = \App\Models\Practitioner::factory()
                ->specialist($e->name, $e->id)
                ->create();

            $this->command->info($doctor);
        }
    }
}
