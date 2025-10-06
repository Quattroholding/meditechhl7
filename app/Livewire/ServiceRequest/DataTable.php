<?php

namespace App\Livewire\ServiceRequest;

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
        // Agrupar ServiceRequests por Encounter
        $encountersQuery = \App\Models\Encounter::query()
            ->with([
                'patient',
                'practitioner',
                'diagnoses.condition',
                'serviceRequests' => function ($query) {
                    $query->with(['cpt', 'results'])
                        ->withCount('results');
                },
            ])
            ->whereHas('serviceRequests')
            ->when($this->search, function (Builder $query) {
                $query->where(function ($q) {
                    $q->orWhereHas('patient', function ($q) {
                        $q->where('name', 'like', '%'.$this->search.'%');
                    })
                        ->orWhereHas('practitioner', function ($q) {
                            $q->where('name', 'like', '%'.$this->search.'%');
                        })
                        ->orWhereRaw("DATE_FORMAT(encounters.start, '%d/%m/%Y') LIKE ?", ['%'.$this->search.'%']);
                        /*
                        ->orWhereHas('diagnoses.condition', function ($q) {
                            $q->where('onset_info', 'like', '%'.$this->search.'%');
                        })
                        ->orWhereHas('serviceRequests', function ($q) {
                            $q->where('code', 'like', '%'.$this->search.'%')
                                ->orWhere('service_type', 'like', '%'.$this->search.'%')
                                ->orWhereHas('cpt', function ($cptQuery) {
                                    $cptQuery->where('description_es', 'like', '%'.$this->search.'%')
                                        ->orWhere('description', 'like', '%'.$this->search.'%');
                                });
                        });*/
                });
            })
            ->when(auth()->user()->hasRole('doctor'), function (Builder $query) {
                $query->where('practitioner_id', auth()->user()->practitioner->id);
            })
            ->orderBy('start', 'desc');

        $data = $encountersQuery->paginate($this->pagination);

        return view('livewire.service-request.data-table', ['data' => $data]);
    }

    public function openModal($id = null)
    {
        if ($id) {
            $this->dispatch('editServiceRequestModal', $id);
        } else {
            $this->dispatch('openServiceRequestModal', 'Crear Solicitud de Servicio');
        }
    }

    #[On('refreshServiceRequests')]
    public function refreshServiceRequests()
    {
        $this->resetPage();
    }

    public function openUploadModal($serviceRequestId)
    {
        $this->dispatch('openUploadResultModal', $serviceRequestId);
    }

    public function openViewResultsModal($serviceRequestId)
    {
        $this->dispatch('openViewResultsModal', $serviceRequestId);
    }

    public function openStatusModal($serviceRequestId)
    {
        $this->dispatch('openStatusManagerModal', $serviceRequestId);
    }
}
