<?php

namespace App\Livewire\Patient\Dashboard;

use App\Models\Appointment;
use Livewire\Component;

class UpcomingAppointments extends Component
{
    public $patient;

    public $limit = 5;

    public $appointment_date;

    public $appointment_time;

    public $modalTitle;

    public $showModal;

    public $order;

    public $isLoading = true;

    public $upcomingAppointments = [];

    protected $listeners = ['loadData'];

    public function mount($limit = 5, $order = null)
    {
        $this->patient = auth()->user()->patient;
        $this->limit = $limit;
        $this->order = $order;
        // Initialize empty data to avoid errors during loading
        $this->upcomingAppointments = collect();
    }

    public function loadData()
    {
        $this->loadUpcomingAppointments();
        $this->isLoading = false;
    }

    public function loadUpcomingAppointments()
    {
        if (! $this->patient) {
            $this->upcomingAppointments = collect();

            return;
        }

        $this->upcomingAppointments = Appointment::where('patient_id', $this->patient->id)
            ->whereDate('start', '>=', now())
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
                'cancellation_reason' => 'Cancelado por el paciente',
            ]);

            $this->dispatch('showToastr', [
                'type' => 'success',
                'message' => 'Cita cancelada exitosamente.',
            ]);
        } else {
            $this->dispatch('showToastr', [
                'type' => 'error',
                'message' => 'No se puede cancelar esta cita. Debe hacerlo con al menos 24 horas de anticipación.',
            ]);
        }
    }

    public function openModal($date = null, $time = null, $modalTitle = 'Nueva Cita')
    {
        $this->dispatch('openAppointmentModal', 'Nueva Cita');
    }

    public function render()
    {
        return view('livewire.patient.dashboard.upcoming-appointments');
    }
}
