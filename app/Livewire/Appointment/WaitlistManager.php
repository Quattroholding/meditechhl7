<?php

namespace App\Livewire\Appointment;

use App\Enums\WaitlistUrgencyLevel;
use App\Models\AppointmentWaitlistEntry;
use App\Models\MedicalSpeciality;
use App\Models\Practitioner;
use App\Services\WaitlistService;
use Carbon\Carbon;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class WaitlistManager extends Component
{
    use WithPagination;

    public int $clientId;

    public ?int $filterPractitionerId = null;

    public ?int $filterSpecialityId = null;

    public ?string $filterUrgency = null;

    public ?string $searchQuery = null;

    public bool $showAssignModal = false;

    public ?AppointmentWaitlistEntry $selectedEntry = null;

    public ?string $assignDate = null;

    public ?string $assignTime = null;

    public ?int $assignDuration = 30;

    public ?int $assignRoomId = null;

    public function mount(): void
    {
        $this->clientId = auth()->user()->default_client_id ?? 1;
    }

    #[Computed]
    public function waitlistEntries()
    {
        $query = AppointmentWaitlistEntry::query()
            ->where('client_id', $this->clientId)
            ->active()
            ->notExpired();

        if ($this->filterPractitionerId) {
            $query->where('practitioner_id', $this->filterPractitionerId);
        }

        if ($this->filterSpecialityId) {
            $query->where('medical_speciality_id', $this->filterSpecialityId);
        }

        if ($this->filterUrgency) {
            $query->where('urgency_level', $this->filterUrgency);
        }

        if ($this->searchQuery) {
            $query->whereHas('patient', function ($q) {
                $q->where('name', 'like', "%{$this->searchQuery}%")
                    ->orWhere('identifier', 'like', "%{$this->searchQuery}%");
            });
        }

        return $query->orderedByPriority()
            ->paginate(20);
    }

    #[Computed]
    public function statistics()
    {
        $waitlistService = app(WaitlistService::class);

        return $waitlistService->getWaitlistStats($this->clientId);
    }

    #[Computed]
    public function practitioners()
    {
        return Practitioner::where('active', true)
            ->orderBy('name')
            ->get();
    }

    #[Computed]
    public function specialities()
    {
        return MedicalSpeciality::orderBy('name')->get();
    }

    public function updatedFilterPractitionerId(): void
    {
        $this->resetPage();
    }

    public function updatedFilterSpecialityId(): void
    {
        $this->resetPage();
    }

    public function updatedFilterUrgency(): void
    {
        $this->resetPage();
    }

    public function updatedSearchQuery(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->filterPractitionerId = null;
        $this->filterSpecialityId = null;
        $this->filterUrgency = null;
        $this->searchQuery = null;
        $this->resetPage();
    }

    public function openAssignModal(AppointmentWaitlistEntry $entry): void
    {
        $this->selectedEntry = $entry;

        // Pre-llenar con preferencias del paciente
        if ($entry->preferred_date) {
            $this->assignDate = $entry->preferred_date->toDateString();
        } else {
            $this->assignDate = Carbon::now()->addDays(1)->toDateString();
        }

        if ($entry->preferred_time) {
            $this->assignTime = $entry->preferred_time->format('H:i');
        } else {
            $this->assignTime = '10:00';
        }

        $this->assignDuration = $entry->appointment->minutes_duration ?? 30;
        $this->assignRoomId = $entry->consulting_room_id;

        $this->showAssignModal = true;
    }

    public function closeAssignModal(): void
    {
        $this->showAssignModal = false;
        $this->selectedEntry = null;
        $this->resetForm();
    }

    public function assignAppointment(): void
    {
        $this->validate([
            'assignDate' => 'required|date',
            'assignTime' => 'required|date_format:H:i',
            'assignDuration' => 'required|integer|min:15|max:480',
        ], [
            'assignDate.required' => 'La fecha es requerida',
            'assignDate.date' => 'La fecha debe ser válida',
            'assignTime.required' => 'La hora es requerida',
            'assignTime.date_format' => 'La hora debe estar en formato HH:mm',
            'assignDuration.required' => 'La duración es requerida',
            'assignDuration.integer' => 'La duración debe ser un número entero',
            'assignDuration.min' => 'La duración mínima es 15 minutos',
            'assignDuration.max' => 'La duración máxima es 480 minutos',
        ]);

        try {
            $waitlistService = app(WaitlistService::class);

            // Combinar fecha y hora
            $dateTime = Carbon::createFromFormat(
                'Y-m-d H:i',
                $this->assignDate.' '.$this->assignTime
            );

            // Asignar cita
            $waitlistService->assignFromWaitlist(
                $this->selectedEntry,
                $dateTime,
                $this->assignDuration,
                $this->assignRoomId,
                auth()->user()
            );

            // Cerrar modal PRIMERO
            $this->showAssignModal = false;
            $this->selectedEntry = null;
            $this->resetForm();

            // Luego hacer el resto
            $this->dispatch('appointment-assigned');
            session()->flash('success', 'Cita asignada exitosamente');
        } catch (\Exception $e) {
            $this->closeAssignModal();
            session()->flash('error', 'Error al asignar cita: '.$e->getMessage());
        }
    }

    public function cancelEntry(AppointmentWaitlistEntry $entry): void
    {
        try {
            $waitlistService = app(WaitlistService::class);
            $waitlistService->cancelEntry(
                $entry,
                auth()->user(),
                'Cancelado por recepcionista'
            );

            session()->flash('success', 'Entrada cancelada exitosamente');
            $this->dispatch('entry-cancelled');
        } catch (\Exception $e) {
            session()->flash('error', 'Error al cancelar: '.$e->getMessage());
        }
    }

    private function resetForm(): void
    {
        $this->assignDate = null;
        $this->assignTime = null;
        $this->assignDuration = 30;
        $this->assignRoomId = null;
    }

    public function render()
    {
        return view('livewire.appointment.waitlist-manager', [
            'waitlistEntries' => $this->waitlistEntries,
            'statistics' => $this->statistics,
            'practitioners' => $this->practitioners,
            'specialities' => $this->specialities,
            'urgencyLevels' => WaitlistUrgencyLevel::options(),
        ]);
    }
}
