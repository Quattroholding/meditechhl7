<?php

namespace App\Livewire\Doctor;
use App\Models\Appointment;
use Carbon\Carbon;
use Livewire\Attributes\On;
use Livewire\Component;

class NextAppointment extends Component
{
    public $nextAppointmentTime;
    public $timeRemainingPercentage;

    public function mount()
    {
        $this->getNextAppointment();
    }

    #[On('getNextAppointment')]
    public function getNextAppointment()
    {
        $doctorId = auth()->user()->practitioner->id;

        $nextAppointment = Appointment::where('practitioner_id', $doctorId)
            ->whereIn('status', ['booked', 'arrived'])
            ->where('start', '>=', Carbon::now())
            ->orderBy('start', 'asc')
            ->first();

        if ($nextAppointment && $nextAppointment->start) {
            $startDate = Carbon::parse($nextAppointment->start);
            $now = Carbon::now();
            $diffInMinutes = $now->diffInMinutes($startDate);

            $hours = floor($diffInMinutes / 60);
            $minutes = $diffInMinutes % 60;

            $this->nextAppointmentTime = "en $hours horas y $minutes minutos";
            $this->calculateTimeRemainingPercentage($startDate);
        } else {
            $this->nextAppointmentTime = __('No hay citas próximas');
            $this->timeRemainingPercentage = 0;
        }
    }


    public function calculateTimeRemainingPercentage($appointmentStartTime)
    {
        $now = Carbon::now();
        $startTime = Carbon::parse($appointmentStartTime);

        // Calcula la diferencia en minutos entre ahora y la próxima cita
        $diffInMinutes = $now->diffInMinutes($startTime);
        //dd($diffInMinutes);

        // Calcula el porcentaje de tiempo restante en relación con un día completo (1440 minutos)
        $this->timeRemainingPercentage = $diffInMinutes > 0 ? (1 - ($diffInMinutes / 1440)) * 100 : 0;
    }


    public function render()
    {
        return view('livewire.doctor.next-appointment');
    }
}
