<?php

namespace App\Livewire\Appointment;

use App\Models\AppointmentFreedSlot;
use App\Models\AppointmentWaitlistEntry;
use App\Services\WaitlistService;
use Carbon\Carbon;
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

    #[On('show-manual-assignment')]
    public function show(int $slotId): void
    {
        $this->freedSlot = AppointmentFreedSlot::findOrFail($slotId);
        $this->assignDate = $this->freedSlot->slot_date->format('Y-m-d');
        // Extraer hora en formato H:i de slot_start_time (que es tipo time: HH:MM:SS)
        $this->assignTime = substr($this->freedSlot->slot_start_time, 0, 5);
        $this->assignDuration = $this->freedSlot->duration_minutes;
        $this->assignRoomId = $this->freedSlot->consulting_room_id;

        // Obtener candidatos sugeridos por score
        $waitlistService = app(WaitlistService::class);
        $this->suggestedCandidates = $waitlistService->getSuggestedCandidates($this->freedSlot, 10)
            ->map(fn ($candidate) => [
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
        \Log::info('assignSlot() iniciado', [
            'selectedEntryId' => $this->selectedEntryId,
            'freedSlot' => $this->freedSlot?->id,
            'assignDate' => $this->assignDate,
            'assignTime' => $this->assignTime,
        ]);

        if (! $this->selectedEntryId || ! $this->freedSlot) {
            \Log::warning('assignSlot() - Validación fallida', [
                'selectedEntryId' => $this->selectedEntryId,
                'freedSlot' => $this->freedSlot?->id,
            ]);
            $this->dispatch('showToastrManualSlotassigment',
                type: 'error',
                message: 'Selecciona un paciente para asignar el espacio'
            );

            return;
        }

        try {
            $entry = AppointmentWaitlistEntry::findOrFail($this->selectedEntryId);

            \Log::info('assignSlot() - Entry encontrado', [
                'entry_id' => $entry->id,
                'patient_id' => $entry->patient_id,
            ]);

            // Crear DateTime para la asignación
            $start = Carbon::createFromFormat(
                'Y-m-d H:i',
                "{$this->assignDate} {$this->assignTime}"
            );

            \Log::info('assignSlot() - DateTime creado', [
                'start' => $start->format('Y-m-d H:i'),
            ]);

            // Asignar desde la lista de espera
            $waitlistService = app(WaitlistService::class);
            $waitlistService->assignFromWaitlist(
                $entry,
                $start,
                $this->assignDuration,
                $this->assignRoomId,
                auth()->user()
            );

            \Log::info('assignSlot() - assignFromWaitlist completado', [
                'entry_id' => $entry->id,
            ]);

            // Marcar slot como manualmente llenado
            $this->freedSlot->markAsManuallyFilled();

            \Log::info('assignSlot() - Slot marcado como manualmente llenado', [
                'slot_id' => $this->freedSlot->id,
            ]);

            $patientName = $entry->patient->name ?? 'Paciente';
            $this->dispatch('showToastrManualSlotassigment',
                type: 'success',
                message: "Paciente {$patientName} asignado correctamente"
            );

            $this->closeModal();
            $this->dispatch('appointment-assigned');

            \Log::info('assignSlot() - Completado exitosamente', [
                'entry_id' => $entry->id,
            ]);
        } catch (\Exception $e) {
            \Log::error('assignSlot() - Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            $this->dispatch('showToastrManualSlotassigment',
                type: 'error',
                message: 'Error al asignar: '.$e->getMessage()
            );
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
