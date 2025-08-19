<?php

namespace Database\Seeders;

use App\Models\MedicalSpeciality;
use App\Models\Patient;
use App\Models\Practitioner;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 3. Crear médicos (con usuarios y rol doctor)

        $especialidades = MedicalSpeciality::get();

        foreach ($especialidades as $e) {
            $doctor = Practitioner::factory()
                ->specialist($e->name, $e->id)
                ->create();

            $this->command->info($doctor);
        }

        // 4. Crear pacientes (con usuarios y rol patient)
        $patients = Patient::factory()
            ->count(50)
            ->create();

    }
}
