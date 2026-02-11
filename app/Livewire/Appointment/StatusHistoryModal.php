<?php

namespace App\Livewire\Appointment;

use App\Enums\AppointmentStatusEnum;
use App\Models\Appointment;
use Livewire\Attributes\Modelable;
use Livewire\Attributes\On;
use Livewire\Component;

class StatusHistoryModal extends Component
{
    #[Modelable]
    public $showModal = false;

    public $appointmentId;

    public $statusHistory = [];

    #[On('showStatusHistory')]
    public function showStatusHistory($appointmentId): void
    {
        $this->appointmentId = $appointmentId;
        $this->loadStatusHistory();
        $this->showModal = true;
    }

    public function loadStatusHistory(): void
    {
        $appointment = Appointment::with(['statusHistory.user'])->find($this->appointmentId);

        if ($appointment) {
            $this->statusHistory = $appointment->statusHistory->map(function ($status) {
                return [
                    'id' => $status->id,
                    'status' => $status->status,
                    'status_label' => AppointmentStatusEnum::getLabelFromString($status->status),
                    'status_badge_class' => AppointmentStatusEnum::getBadgeClassFromString($status->status),
                    'previous_status' => $status->previous_status,
                    'previous_status_label' => AppointmentStatusEnum::getLabelFromString($status->previous_status),
                    'previous_status_badge_class' => AppointmentStatusEnum::getBadgeClassFromString($status->previous_status),
                    'observation' => $status->observation,
                    'user_name' => $status->user->full_name ?? 'Sistema',
                    'created_at' => $status->getRawOriginal('created_at'),
                ];
            })->toArray();
        }
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->reset(['appointmentId', 'statusHistory']);
    }


    public function render()
    {
        return view('livewire.appointment.status-history-modal');
    }
}
