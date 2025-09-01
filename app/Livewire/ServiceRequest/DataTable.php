<?php

namespace App\Livewire\ServiceRequest;

use App\Models\ServiceRequest;
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
        $data = ServiceRequest::query()
            ->with(['patient', 'practitioner', 'encounter', 'cpt'])
            ->withCount('results')
            ->when($this->search, function (Builder $query) {
                $query->where(function ($q) {
                    $q->orWhere('id', 'like', '%' . $this->search . '%')
                      ->orWhere('code', 'like', '%' . $this->search . '%')
                      ->orWhere('code_display', 'like', '%' . $this->search . '%')
                      ->orWhere('service_type', 'like', '%' . $this->search . '%')
                      ->orWhere('status', 'like', '%' . $this->search . '%')
                      ->orWhere('intent', 'like', '%' . $this->search . '%')
                      ->orWhere('priority', 'like', '%' . $this->search . '%')
                      ->orWhere('service_type', 'like', '%' . $this->search . '%')
                      ->orWhereHas('patient', function ($q) {
                          $q->where('name', 'like', '%' . $this->search . '%');
                      })
                      ->orWhereHas('practitioner', function ($q) {
                          $q->where('name', 'like', '%' . $this->search . '%');
                      });
                });
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->pagination);

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
