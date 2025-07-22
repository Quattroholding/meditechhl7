<?php

namespace App\Livewire\Appointment;

use App\Models\Appointment;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;

class Status extends Component
{
    use WithPagination;


    public $appointment_id;
    public $appointment;
    public $status;
    public $colors;
    public $color;

    public function render()
    {
        $this->appointment = Appointment::find($this->appointment_id);
        $this->status = $this->appointment->status;
        $this->colors = $this->appointment::statusColors();
        $this->color = $this->colors[$this->status];

        return view('livewire.appointment.status');
    }

    public function changeStatus($newStatus){
        $current_status = $this->appointment->status;
        $this->appointment->status = $newStatus;
        $this->appointment->save();
        $this->status = $newStatus;
        $this->color = $this->colors[$this->status];

        // Emitir evento para otros usuarios
        $this->dispatch('appointmentStatusChanged',
            appointment_id: $this->appointment_id,
            new_status: $newStatus
        );

        if($current_status=='proposed' && $newStatus=='booked'){
            $this->appointment->notifyPatientAboutConfirmation();

            $this->dispatch('showToastr' . $this->appointment_id, [
                'type' => 'success',
                'message' => '¡Cita confirmada con exito , se envio notificacion al correo del paciente!',
                'appointment_id' => $this->appointment_id
            ]);
        }

        if($newStatus=='checked-in'){
            $this->dispatch('showToastr'.$this->appointment_id, [
                'type' => 'success',
                'message' => '¡Espere por favor en unos segundos empezara su consulta!',
                'appointment_id' => $this->appointment_id
            ]);
            //sleep(5);
            //return $this->redirect(route('consultation.show',$this->appointment->id));
        }
    }

    #[On('appointmentStatusChanged')]
    public function updateStatus($appointment_id, $new_status)
    {
        if ($appointment_id == $this->appointment_id) {
            $this->appointment = Appointment::find($this->appointment_id);
            $this->status = $new_status;
            $this->colors = $this->appointment::statusColors();
            $this->color = $this->colors[$this->status];
        }
    }

}
