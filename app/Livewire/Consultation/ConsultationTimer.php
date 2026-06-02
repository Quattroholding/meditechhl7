<?php

namespace App\Livewire\Consultation;

use App\Models\Appointment;
use App\Models\Encounter;
use Carbon\Carbon;
use Livewire\Component;

class ConsultationTimer extends Component
{
    public $encounter;

    public $appointment;

    public $showTimer = false;

    public function mount(Encounter $encounter, Appointment $appointment): void
    {
        $this->encounter = $encounter;
        $this->appointment = $appointment;

        // Verificar si el cliente tiene habilitado el timer
        $client = auth()->user()->clients()->first();
        $this->showTimer = $client && $client->show_consultation_timer;
    }

    public function getTimerData(): array
    {
        if (! $this->showTimer) {
            return [];
        }

        // Validar que existan los datos necesarios
        if (! $this->appointment || ! $this->appointment->end || ! $this->encounter || ! $this->encounter->start) {
            return [];
        }

        $now = Carbon::now();
        $appointmentEnd = Carbon::parse($this->appointment->end);
        $encounterStart = Carbon::parse($this->encounter->start);

        // Obtener el status crudo sin el accessor HTML
        $status = $this->encounter->getAttributes()['status'] ?? null;

        if (! $status) {
            return [];
        }

        // Para consultas in-progress
        if (str_contains($status, 'in-progress')) {
            $appointmentEndTimestamp = $appointmentEnd->timestamp;

            if ($now->lte($appointmentEnd)) {
                // Dentro del tiempo programado - contador decreciente en verde
                $remainingSeconds = $now->diffInSeconds($appointmentEnd, false);

                return [
                    'status' => 'in-progress',
                    'color' => 'green',
                    'type' => 'countdown',
                    'seconds' => abs($remainingSeconds),
                    'appointmentEndTimestamp' => $appointmentEndTimestamp,
                    'message' => __('consultation.timer.remaining_time'),
                ];
            } else {
                // Fuera del tiempo programado - contador creciente en rojo
                $overtimeSeconds = $appointmentEnd->diffInSeconds($now);

                return [
                    'status' => 'in-progress',
                    'color' => 'red',
                    'type' => 'countup',
                    'seconds' => $overtimeSeconds,
                    'appointmentEndTimestamp' => $appointmentEndTimestamp,
                    'message' => __('consultation.timer.overtime'),
                ];
            }
        }

        // Para consultas completed/finished
        if (str_contains($status, 'finished')) {
            $encounterEnd = $this->encounter->end ? Carbon::parse($this->encounter->end) : $now;
            $totalDuration = $encounterStart->diffInSeconds($encounterEnd);
            $wasOnTime = $encounterEnd->lte($appointmentEnd);

            if ($wasOnTime) {
                // Completada dentro del tiempo - verde
                return [
                    'status' => 'finished',
                    'color' => 'green',
                    'type' => 'static',
                    'seconds' => $totalDuration,
                    'message' => __('consultation.timer.completed_on_time'),
                ];
            } else {
                // Completada fuera de tiempo - rojo con tiempo extra
                $overtimeSeconds = $appointmentEnd->diffInSeconds($encounterEnd);

                return [
                    'status' => 'finished',
                    'color' => 'red',
                    'type' => 'static',
                    'seconds' => $totalDuration,
                    'overtime' => $overtimeSeconds,
                    'message' => __('consultation.timer.completed_overtime'),
                ];
            }
        }

        return [];
    }

    public function render()
    {
        return view('livewire.consultation.consultation-timer', [
            'timerData' => $this->getTimerData(),
        ]);
    }
}
