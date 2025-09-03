<?php

namespace App\Livewire\Medicine;

use App\Models\Medicine;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class DataTable extends Component
{
    use WithPagination;

    public $search;

    public $sortField = 'id';

    public $sortDirection = 'asc';

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
        $data = Medicine::query()
            ->when($this->search, function (Builder $query) {
                $query->where(function ($q) {
                    $q->orWhere('generic_name', 'like', '%'.$this->search.'%')
                        ->orWhere('home_name', 'like', '%'.$this->search.'%')
                        ->orWhere('ndc_code', 'like', '%'.$this->search.'%')
                        ->orWhere('type', 'like', '%'.$this->search.'%')
                        ->orWhere('mgs', 'like', '%'.$this->search.'%')
                        ->orWhere('mgs_type', 'like', '%'.$this->search.'%');
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
}
