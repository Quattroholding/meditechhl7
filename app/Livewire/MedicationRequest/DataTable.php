<?php

namespace App\Livewire\MedicationRequest;

use App\Models\Encounter;
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
        // Agrupar MedicationRequests por Encounter
        $encountersQuery = Encounter::query()
            ->with([
                'patient',
                'practitioner',
                'appointment.client',
                'diagnoses.condition.icd10Code',
                'medicationRequests' => function ($query) {
                    $query->with(['medicine', 'medication2.ingredients'])
                        ->orderBy('created_at', 'desc');
                },
            ])
            ->whereHas('medicationRequests')
            ->when($this->search, function (Builder $query) {
                $query->where(function ($q) {
                    $q->orWhereHas('patient', function ($q) {
                        $q->where('name', 'like', '%'.$this->search.'%');
                    })
                        ->orWhereHas('practitioner', function ($q) {
                            $q->where('name', 'like', '%'.$this->search.'%');
                        })
                        ->orWhereRaw("DATE_FORMAT(encounters.start, '%d/%m/%Y') LIKE ?", ['%'.$this->search.'%']);
                });
            })
            ->when(auth()->user()->hasRole('doctor'), function (Builder $query) {
                $query->where('practitioner_id', auth()->user()->practitioner->id);
            })
            ->orderBy('start', 'desc');

        $data = $encountersQuery->paginate($this->pagination);

        return view('livewire.medication-request.data-table', ['data' => $data]);
    }

    #[On('refreshMedicationRequests')]
    public function refreshMedicationRequests()
    {
        $this->resetPage();
    }
}
