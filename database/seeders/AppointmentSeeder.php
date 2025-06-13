<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Patient;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Nette\Utils\DateTime;

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
        /*Appointment::factory()
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
        date_default_timezone_set('America/Panama');
        $horaInicio = new DateTime('08:00');
        $horaFin = new DateTime('17:00'); // Puedes definir un límite si quieres

        $duraciones = [15, 30, 45, 60]; // Duraciones posibles en minutos

        while ($horaInicio < $horaFin) {
            // Elegir duración aleatoria
            $duracion = $duraciones[array_rand($duraciones)];

            // Clonar la hora de inicio actual para mostrarla
            $inicioActual = clone $horaInicio;

            // Calcular la nueva hora de fin
            $horaInicio->modify("+$duracion minutes");

            // Si la nueva hora de fin excede el límite, rompemos el ciclo
            if ($horaInicio > $horaFin) {
                break;
            }
            Appointment::factory()->create([
                'practitioner_id' =>49,
                'start'  =>$inicioActual->format('Y-m-d H:i'),
                'end' =>  $horaInicio->format('Y-m-d H:i'),
                'minutes_duration'=>$duracion,
                'status'=>'booked',
            ]);
            // Mostrar intervalo
            echo "Inicio: " . $inicioActual->format('H:i') . " - Fin: " . $horaInicio->format('H:i') . " (Duración: $duracion minutos)" . PHP_EOL;
        }

    }
}
