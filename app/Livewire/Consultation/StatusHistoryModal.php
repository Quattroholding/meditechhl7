<?php

namespace App\Livewire\Consultation;

use App\Models\Encounter;
use Livewire\Attributes\Modelable;
use Livewire\Attributes\On;
use Livewire\Component;

class StatusHistoryModal extends Component
{
    #[Modelable]
    public $showModal = false;

    public $encounterId;

    public $statusHistory = [];

    #[On('showStatusHistory')]
    public function showStatusHistory($encounterId): void
    {
        $this->encounterId = $encounterId;
        $this->loadStatusHistory();
        $this->showModal = true;
    }

    public function loadStatusHistory(): void
    {
        $encounter = Encounter::with(['statusHistory.user'])->find($this->encounterId);

        if ($encounter) {
            $this->statusHistory = $encounter->statusHistory->map(function ($status) {
                return [
                    'id' => $status->id,
                    'status' => $status->status,
                    'status_label' => __('encounter.status.'.$status->status),
                    'previous_status' => $status->previous_status,
                    'previous_status_label' => $status->previous_status ? __('encounter.status.'.$status->previous_status) : null,
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
        $this->reset(['encounterId', 'statusHistory']);
    }

    public function render()
    {
        return view('livewire.consultation.status-history-modal');
    }
}
