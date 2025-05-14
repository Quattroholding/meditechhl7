<?php

namespace Database\Seeders;

use App\Models\MedicalSpeciality;
use App\Models\Practitioner;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PractitionerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $especialidades = MedicalSpeciality::get();

        foreach ($especialidades as $e){
           $doctor = \App\Models\Practitioner::factory()
                ->specialist($e->name)
                ->create();

            $this->command->info($doctor);
        }
    }
}
