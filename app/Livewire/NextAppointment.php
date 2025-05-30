<?php

namespace App\Livewire;
use App\Models\Appointment;
use Carbon\Carbon;
use Livewire\Component;

class NextAppointment extends Component
{
    public $nextAppointmentTime;
    public $timeRemainingPercentage;

    public function mount()
    {
        $this->getNextAppointment();
    }

    public function getNextAppointment()
    {
        $doctorId = auth()->user()->id; 

        $nextAppointment = Appointment::selectRaw('DATE(start) as start_date, start')
            ->where('practitioner_id', $doctorId)
            ->whereIn('status', ['booked', 'arrived']) 
            ->where('start', '>=', now())
            ->orderBy('start', 'asc')
            ->first();
       //dd($nextAppointment, now(), today());

        if ($nextAppointment && $nextAppointment->start) {
            //$startDate = Carbon::parse($nextAppointment->start);
            
            $startDate = Carbon::parse($nextAppointment->start);
            $this->nextAppointmentTime = $startDate->diffForHumans();
            //dd($nextAppointment->start->diffForHumans());
            $this->calculateTimeRemainingPercentage($startDate);
        } else {
            $this->nextAppointmentTime = 'No upcoming appointments';
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
        return view('livewire.next-appointment');
    }
}
