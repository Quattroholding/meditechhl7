<?php

namespace App\Livewire\Patient\Dashboard;

use App\Models\Appointment;
use Livewire\Component;

class UpcomingAppointments extends Component
{
    public $patient;
    public $limit = 5;

    public function mount($limit = 5)
    {
        $this->patient = auth()->user()->patient;
        $this->limit = $limit;
    }

    public function getUpcomingAppointmentsProperty()
    {
        if (!$this->patient) {
            return collect();
        }

        return Appointment::where('patient_id', $this->patient->id)
            ->where('start', '>=', now())
            ->where('status', '!=', 'cancelled')
            ->with(['practitioner', 'consultingRoom.branch'])
            ->orderBy('start')
            ->limit($this->limit)
            ->get();
    }

    public function cancelAppointment($appointmentId)
    {
        $appointment = Appointment::where('id', $appointmentId)
            ->where('patient_id', $this->patient->id)
            ->first();

        if ($appointment && $appointment->start > now()->addHours(24)) {
            $appointment->update([
                'status' => 'cancelled',
                'cancellation_reason' => 'Cancelado por el paciente'
            ]);

            $this->dispatch('showToastr', [
                'type' => 'success',
                'message' => 'Cita cancelada exitosamente.'
            ]);
        } else {
            $this->dispatch('showToastr', [
                'type' => 'error',
                'message' => 'No se puede cancelar esta cita. Debe hacerlo con al menos 24 horas de anticipación.'
            ]);
        }
    }

    public function render()
    {
        return view('livewire.patient.dashboard.upcoming-appointments');
    }
}
