<?php

namespace App\Livewire\Dermatology;

use App\Models\Patient;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Livewire\WithPagination;

class PatientSelector extends Component
{
    use WithPagination;

    public $search = '';

    public $sortField = 'id';

    public $sortDirection = 'desc';

    public $pagination = 10;

    public function updatedSearch()
    {
        $this->resetPage();
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

    public function render()
    {
        $data = Patient::query()
            ->when($this->search, function (Builder $query) {
                $query->where(function ($q) {
                    $q->orWhere('birth_date', 'like', '%'.$this->search.'%');
                    $q->orWhere('identifier', 'like', '%'.$this->search.'%');
                    $q->orWhere('email', 'like', '%'.$this->search.'%');
                    $q->orWhere('name', 'like', '%'.$this->search.'%');
                });
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->pagination, ['*'], 'page');

        return view('livewire.dermatology.patient-selector', ['data' => $data]);
    }
}
