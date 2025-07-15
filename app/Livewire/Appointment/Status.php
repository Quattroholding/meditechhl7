<?php

namespace App\Livewire\Appointment;

use App\Models\Appointment;
use Livewire\Component;
use Livewire\WithPagination;

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

        if($current_status=='proposed' && $newStatus=='booked'){
            $this->appointment->notifyPatientAboutConfirmation();

            $this->dispatch('showToastrStatus'.$this->appointment_id,
                type: 'success',
                message: '¡Cita confirmada con exito , se envio notificacion al correo del paciente!'
            );
        }

        if($newStatus=='checked-in'){

            $this->dispatch('showToastrStatus'.$this->appointment_id,
                type: 'success',
                message: '¡Espere por favor en unos segundos empezara su consulta!'
            );
            //sleep(5);
            //return $this->redirect(route('consultation.show',$this->appointment->id));
        }
    }

}
