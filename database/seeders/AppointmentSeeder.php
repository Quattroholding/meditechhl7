<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Patient;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AppointmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear 20 citas normales
        /*Appointment::factory()
            ->count(20)
            ->create();

        // Crear 5 citas canceladas
        Appointment::factory()
            ->count(5)
            ->cancelled()
            ->create();*/

        // Crear 15 citas booked
        /*Appointment::factory()
            ->count(15)
            ->booked()
            ->create();*/

        // Crear 5 citas fullfilled
        Appointment::factory()
            ->count(8)
            ->booked()
           // ->withEncounter()
            ->create();

        // Crear citas para un paciente específico
        /*$patient =Patient::first();
        Appointment::factory()
            ->count(3)
            ->for($patient)
            ->create();*/

        // Crear citas con especialistas
       /* Appointment::factory()
            ->withSpecialist('Cardiología')
            ->count(2)
            ->booked()
            ->create();

        Appointment::factory()
            ->withSpecialist('Pediatría')
            ->count(2)
            ->booked()
            ->create();*/
    }
}
