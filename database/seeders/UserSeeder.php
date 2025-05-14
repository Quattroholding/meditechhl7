<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Patient;
use App\Models\Practitioner;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 3. Crear médicos (con usuarios y rol doctor)
        $doctors = Practitioner::factory()
            ->count(5)
            ->create();

        // 4. Crear pacientes (con usuarios y rol patient)
        $patients = Patient::factory()
            ->count(20)
            ->create();
    }
}
