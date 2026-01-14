<?php

namespace App\Livewire\User;

use App\Models\Practitioner;
use App\Models\Recepy\RecepyDoctorProfile;
use App\Models\User;
use App\Notifications\AccountValidationNotification;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class PendingValidations extends Component
{
    use WithPagination;

    public $search = '';

    public $selectedUser = null;

    public $showModal = false;

    public $rejectionReason = '';

    public $documents = [];

    public $sortField = 'id'; // Ordenación por defecto

    public $sortDirection = 'desc'; // Dirección de orden

    public $pagination = 10;

    public function mount()
    {
        if (! auth()->user()->can('users.validate')) {
            abort(403, 'No tienes permiso para acceder a esta página');
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function viewDocuments($userId)
    {
        $this->selectedUser = User::with(['practitioner', 'files' => function ($query) {
            $query->whereIn('type', ['cedula', 'idoneidad']);
        }])->findOrFail($userId);

        $this->documents = $this->selectedUser->files;
        $this->rejectionReason = '';
        $this->showModal = true;
    }

    public function approve()
    {
        if (! $this->selectedUser) {
            return;
        }

        DB::beginTransaction();

        try {
            // Actualizar usuario
            $this->selectedUser->update([
                'active' => true,
                'validation_status' => 'approved',
                'validated_at' => now(),
                'validated_by' => auth()->id(),
            ]);

            // Actualizar practitioner
            if ($practitioner = $this->selectedUser->practitioner) {
                $practitioner->update(['active' => true]);
            }

            // Actualizar doctor profile
            $doctorProfile = RecepyDoctorProfile::where('user_id', $this->selectedUser->id)->first();
            if ($doctorProfile) {
                $doctorProfile->update(['is_active' => true]);
            }

            // Enviar notificación de aprobación
            $this->selectedUser->notify(new AccountValidationNotification(
                approved: true
            ));

            DB::commit();

            $this->dispatch('user-approved');
            session()->flash('message', 'Usuario aprobado exitosamente');

            $this->closeModal();
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error al aprobar usuario: '.$e->getMessage());
        }
    }

    public function reject()
    {
        $this->validate([
            'rejectionReason' => 'required|string|min:10|max:500',
        ], [
            'rejectionReason.required' => 'Debes proporcionar un motivo de rechazo',
            'rejectionReason.min' => 'El motivo debe tener al menos 10 caracteres',
            'rejectionReason.max' => 'El motivo no debe exceder 500 caracteres',
        ]);

        if (! $this->selectedUser) {
            return;
        }

        DB::beginTransaction();

        try {
            // Actualizar usuario con rechazo
            $this->selectedUser->update([
                'active' => false,
                'validation_status' => 'rejected',
                'validated_at' => now(),
                'validated_by' => auth()->id(),
                'rejection_reason' => $this->rejectionReason,
            ]);

            // Enviar notificación de rechazo
            $this->selectedUser->notify(new AccountValidationNotification(
                approved: false,
                rejectionReason: $this->rejectionReason
            ));

            DB::commit();

            $this->dispatch('user-rejected');
            session()->flash('message', 'Usuario rechazado. Se ha enviado notificación.');

            $this->closeModal();
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error al rechazar usuario: '.$e->getMessage());
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->selectedUser = null;
        $this->documents = [];
        $this->rejectionReason = '';
    }

    #[On('user-approved')]
    #[On('user-rejected')]
    public function refreshList()
    {
        // La lista se refrescará automáticamente por el render
    }

    public function render()
    {
        $pendingUsers = User::pendingValidation()
            ->with(['practitioner', 'files' => function ($query) {
                $query->whereIn('type', ['cedula', 'idoneidad']);
            }])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('first_name', 'like', '%'.$this->search.'%')
                        ->orWhere('last_name', 'like', '%'.$this->search.'%')
                        ->orWhere('email', 'like', '%'.$this->search.'%');
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.user.pending-validations', [
            'pendingUsers' => $pendingUsers,
        ]);
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }

        $this->sortField = $field;
        $this->resetPage();
    }
}
