<?php

namespace App\Livewire\Doctor;

use Livewire\Component;
use App\Models\Appointment;

class RecentAppointmentList extends Component
{
    public $appointments;
    public $appointment_date;
    public $appointment_time;
    public $modalTitle;
    public $showModal;
    protected $listeners = ['refreshAppointments' => 'refreshAppointments'];
    public function mount()
    {
       $this->refreshAppointments();
    }

    // app/Http/Livewire/AppointmentList.php
    public function refreshAppointments()
    {
        $today =  \Carbon\Carbon::today();
        $doctor_id = auth()->user()->practitioner->id;
        $this->appointments = Appointment::whereDate('start', $today)
                                        ->wherePractitionerId($doctor_id)
                                        ->orderBy('start')
                                        ->get();
    }

    public function render()
    {
        return view('livewire.doctor.recent-appointment-list');
    }

    public function editAppointment($appointmentId){
        //dd('aqui');
        $this->modalTitle = 'Actualizar Cita';
        $this->dispatch('editAppointmentModal',$appointmentId);
    }

    public function updateStatus($appointmentId, $newStatus)
    {

        try {
            $appointment = Appointment::find($appointmentId);
            $current_status = $appointment->status;
            if ($appointment) {

                $appointment->update(['status' => $newStatus]);
                session()->flash('message.success', 'Estado actualizado exitosamente.');
                $this->refreshAppointments();

                if($current_status=='proposed' && $newStatus=='booked'){
                    $appointment->notifyPatientAboutConfirmation();
                }

                if($newStatus=='checked-in'){
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
}
