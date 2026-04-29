<?php

namespace App\Livewire\Medicine;

use App\Models\Medication;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class DataTable extends Component
{
    use WithPagination;

    public $search;

    public $sortField = 'id';

    public $sortDirection = 'desc';

    public $pagination = 10;

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

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $data = Medication::query()
            ->with(['ingredients', 'medicationRequests'])
            ->when($this->search, function (Builder $query) {
                $query->where(function ($q) {
                    $q->orWhere('generic_name', 'like', '%'.$this->search.'%')
                        ->orWhere('home_name', 'like', '%'.$this->search.'%')
                        ->orWhere('display', 'like', '%'.$this->search.'%')
                        ->orWhere('code', 'like', '%'.$this->search.'%')
                        ->orWhere('form', 'like', '%'.$this->search.'%')
                        ->orWhere('manufacturer', 'like', '%'.$this->search.'%');
                });
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->pagination);

        return view('livewire.medicine.data-table', ['data' => $data]);
    }

    public function openModal($id = null)
    {
        if ($id) {
            $this->dispatch('editMedicineModal', $id);
        } else {
            $this->dispatch('openMedicineModal', 'Crear Medicamento');
        }
    }

    #[On('refreshMedicines')]
    public function refreshMedicines()
    {
        $this->resetPage();
    }

    #[On('deleteMedication')]
    public function deleteMedication($id)
    {
        $medication = Medication::find($id);

        if (! $medication) {
            $this->dispatch('showToastrDeleteMEdication',
                type: 'error',
                message: 'Medicamento no encontrado.',
            );
            session()->flash('error', 'Medicamento no encontrado.');

            return;
        }

        // Authorize using the policy and get the response message
        $response = Gate::inspect('delete', $medication);

        if ($response->denied()) {
            $this->dispatch('showToastrDeleteMEdication',
                type: 'error',
                message: $response->message(),
            );
            session()->flash('error', $response->message());

            return;
        }

        try {
            $medication->delete();
            $this->dispatch('showToastrDeleteMEdication',
                type: 'success',
                message: 'Medicamento eliminado correctamente.',
            );
            session()->flash('success', 'Medicamento eliminado correctamente.');
            $this->resetPage();
        } catch (\Exception $e) {
            $this->dispatch('showToastrDeleteMEdication',
                type: 'error',
                message: 'Error al eliminar el medicamento: '.$e->getMessage(),
            );
            session()->flash('error', 'Error al eliminar el medicamento: '.$e->getMessage());
        }
    }
}
