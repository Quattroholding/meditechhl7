<?php

namespace App\Livewire\Doctor;

use App\Models\Appointment;
use Livewire\Attributes\On;
use Livewire\Component;

class RecentAppointmentList extends Component
{
    public $appointments;

    public $appointment_date;

    public $appointment_time;

    public $modalTitle;

    public $showModal;

    public $order;

    public $isLoading = true;
    // protected $listeners = ['refreshAppointments' => 'refreshAppointments'];

    public function mount()
    {
        // Inicializar variables para evitar errores
        $this->appointments = collect();
    }

    public function loadData()
    {
        $this->loadAppointments();
        $this->isLoading = false;
    }

    // app/Http/Livewire/AppointmentList.php
    #[On('loadAppointments')]
    public function loadAppointments()
    {
        $today = \Carbon\Carbon::today();

        $this->appointments = Appointment::whereDate('start', $today)
            ->orderBy('start')
            ->get();
    }

    public function render()
    {
        return view('livewire.doctor.recent-appointment-list');
    }

    public function editAppointment($appointmentId)
    {
        // dd('aqui');
        $this->modalTitle = 'Actualizar Cita';
        $this->dispatch('editAppointmentModal', $appointmentId);
    }

    public function updateStatus($appointmentId, $newStatus)
    {

        try {
            $appointment = Appointment::find($appointmentId);
            $current_status = $appointment->status;
            if ($appointment) {

                $appointment->update(['status' => $newStatus]);
                session()->flash('message.success', 'Estado actualizado exitosamente.');
                $this->loadAppointments();

                if ($current_status == 'proposed' && in_array($newStatus,['booked','confirm'])) {
                    $appointment->notifyPatientAboutConfirmation();
                }

                if ($newStatus == 'checked-in') {
                    $this->dispatch('showToastr'.$appointmentId,
                        type: 'success',
                        message: '¡Espere por favor en unos segundos empezara su consulta!'
                    );
                }
            }
        } catch (\Exception $e) {
            session()->flash('message.error', 'Error al actualizar el estado.');
        }
    }

    public function openModal($date = null, $time = null, $modalTitle = 'Nueva Cita')
    {
        $this->dispatch('openAppointmentModal', 'Nueva Cita');
    }
}
