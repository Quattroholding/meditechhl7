<?php

namespace App\Livewire\Appointment;

use App\Enums\AppointmentCancelledReason;
use App\Events\AppointmentCheckedIn;
use App\Models\Appointment;
use Livewire\Attributes\On;
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

    public $showCancelModal = false;

    public $cancellationReason = '';

    public $customCancellationReason = '';

    public function render()
    {
        $this->appointment = Appointment::find($this->appointment_id);
        $this->status = $this->appointment->status;
        $this->colors = $this->appointment::statusColors();
        $this->color = $this->colors[$this->status];

        return view('livewire.appointment.status', [
            'cancellationReasons' => AppointmentCancelledReason::toArray(),
        ]);
    }

    public function changeStatus($newStatus)
    {
        \Log::info('ChangeStatus method called', [
            'appointment_id' => $this->appointment_id,
            'old_status' => $this->appointment->status,
            'new_status' => $newStatus,
        ]);

        // Si se intenta cancelar, mostrar modal para solicitar razón
        if ($newStatus == 'cancelled') {
            $this->showCancelModal = true;
            return;
        }

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

        if ($current_status == 'proposed' && $newStatus == 'booked') {
            $this->appointment->notifyPatientAboutConfirmation();

            $this->dispatch('showToastr'.$this->appointment_id, [
                'type' => 'success',
                'message' => '¡Cita confirmada con exito , se envio notificacion al correo del paciente!',
                'appointment_id' => $this->appointment_id,
            ]);
        }

        if ($newStatus == 'checked-in') {

            \Log::info('Broadcasting AppointmentCheckedIn event', [
                'appointment_id' => $this->appointment->id,
                'practitioner_id' => $this->appointment->practitioner_id,
                'channel' => 'doctor.'.$this->appointment->practitioner_id,
                'patient_name' => $this->appointment->patient->name ?? 'Unknown',
            ]);

            // Disparar evento de broadcast para notificar al doctor
            broadcast(new AppointmentCheckedIn($this->appointment));

            \Log::info('AppointmentCheckedIn event broadcasted successfully');

            $this->dispatch('showToastr'.$this->appointment_id, [
                'type' => 'success',
                'message' => '¡Paciente registrado, se notificó al doctor!',
                'appointment_id' => $this->appointment_id,
            ]);
            // sleep(5);
            // return $this->redirect(route('consultation.show',$this->appointment->id));
        }
    }

    public function confirmCancellation()
    {
        $this->validate([
            'cancellationReason' => 'required',
            'customCancellationReason' => 'required_if:cancellationReason,OTHER',
        ], [
            'cancellationReason.required' => 'Debe seleccionar una razón de cancelación',
            'customCancellationReason.required_if' => 'Debe especificar la razón cuando selecciona "Otra razón"',
        ]);

        $current_status = $this->appointment->status;
        $this->appointment->status = 'cancelled';
        $this->appointment->save();
        $this->status = 'cancelled';
        $this->color = $this->colors[$this->status];

        // Determinar la razón final a enviar
        $finalReason = $this->cancellationReason === 'OTHER'
            ? $this->customCancellationReason
            : AppointmentCancelledReason::{$this->cancellationReason}->value;

        // Enviar notificación con la razón
        $this->appointment->notifyPatientAboutCancellation($finalReason);

        // Emitir evento para otros usuarios
        $this->dispatch('appointmentStatusChanged',
            appointment_id: $this->appointment_id,
            new_status: 'cancelled'
        );

        $this->dispatch('showToastr'.$this->appointment_id, [
            'type' => 'warning',
            'message' => '¡Cita cancelada, se envió notificación al paciente!',
            'appointment_id' => $this->appointment_id,
        ]);

        // Cerrar modal y limpiar campos
        $this->showCancelModal = false;
        $this->cancellationReason = '';
        $this->customCancellationReason = '';
    }

    public function closeCancelModal()
    {
        $this->showCancelModal = false;
        $this->cancellationReason = '';
        $this->customCancellationReason = '';
        $this->resetValidation();
    }

    public function getCancellationReasons()
    {
        return AppointmentCancelledReason::toArray();
    }

    #[On('appointmentStatusChanged')]
    public function updateStatus($appointment_id, $new_status)
    {
        dd('aqui');
        if ($appointment_id == $this->appointment_id) {
            $this->appointment = Appointment::find($this->appointment_id);
            $this->status = $new_status;
            $this->colors = $this->appointment::statusColors();
            $this->color = $this->colors[$this->status];
        }
    }
}
