<?php

namespace App\Livewire\Appointment;

use App\Models\AppointmentFreedSlot;
use App\Models\AppointmentWaitlistEntry;
use App\Services\WaitlistService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Attributes\On;
use Livewire\Component;

class ManualSlotAssignment extends Component
{
    public ?AppointmentFreedSlot $freedSlot = null;

    public array $suggestedCandidates = [];

    public ?int $selectedEntryId = null;

    public bool $showModal = false;

    public string $assignDate = '';

    public string $assignTime = '';

    public int $assignDuration = 30;

    public ?int $assignRoomId = null;

    protected WaitlistService $service;

    public function mount(): void
    {
        $this->service = app(WaitlistService::class);
    }

    #[On('show-manual-assignment')]
    public function show(AppointmentFreedSlot $slot): void
    {
        $this->freedSlot = $slot;
        $this->assignDate = $slot->slot_date;
        $this->assignTime = substr($slot->slot_start_time, 0, 5);
        $this->assignDuration = $slot->duration_minutes;
        $this->assignRoomId = $slot->consulting_room_id;

        // Obtener candidatos sugeridos por score
        $this->suggestedCandidates = $this->service->getSuggestedCandidates($slot, 10)
            ->map(fn($candidate) => [
                'entry_id' => $candidate['entry']->id,
                'patient_name' => $candidate['patient_name'],
                'patient_id' => $candidate['patient_id'],
                'urgency' => $candidate['urgency'],
                'days_waiting' => $candidate['days_waiting'],
                'priority_score' => $candidate['priority_score'],
                'match_score' => $candidate['score'],
            ])
            ->toArray();

        $this->showModal = true;
    }

    public function assignSlot(): void
    {
        if (!$this->selectedEntryId || !$this->freedSlot) {
            $this->dispatch('error', 'Selecciona un paciente para asignar el espacio');
            return;
        }

        try {
            $entry = AppointmentWaitlistEntry::findOrFail($this->selectedEntryId);

            // Crear DateTime para la asignación
            $start = \Carbon\Carbon::createFromFormat(
                'Y-m-d H:i',
                "{$this->assignDate} {$this->assignTime}"
            );

            // Asignar desde la lista de espera
            $this->service->assignFromWaitlist(
                $entry,
                $start,
                $this->assignDuration,
                $this->assignRoomId,
                auth()->user()
            );

            // Marcar slot como manualmente llenado
            $this->freedSlot->markAsManuallyFilled();

            $this->dispatch('success', "Paciente {$entry->patient->full_name} asignado correctamente");
            $this->closeModal();
            $this->dispatch('appointment-assigned');
        } catch (\Exception $e) {
            $this->dispatch('error', "Error al asignar: {$e->getMessage()}");
        }
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->selectedEntryId = null;
        $this->suggestedCandidates = [];
        $this->freedSlot = null;
    }

    public function render()
    {
        return view('livewire.appointment.manual-slot-assignment');
    }
}
