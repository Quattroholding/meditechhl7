<?php

namespace App\Livewire\ServiceType;

use App\Models\ServiceType;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Livewire\WithPagination;

class DataTable extends Component
{
    use WithPagination;

    protected $listeners = ['refreshServiceTypes' => '$refresh'];

    public $search = '';

    public $sortField = 'id';

    public $sortDirection = 'desc';

    public $pagination = 10;

    public $statusFilter = 'all';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    public function sortBy($field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }

        $this->sortField = $field;
        $this->resetPage();
    }

    public function openCreateModal(): void
    {
        $this->dispatch('openServiceTypeModal', title: 'Crear Tipo de Servicio');
    }

    public function openEditModal($serviceTypeId): void
    {
        $this->dispatch('editServiceTypeModal', service_type_id: $serviceTypeId);
    }

    public function deleteServiceType($id): void
    {
        $serviceType = ServiceType::find($id);

        if ($serviceType) {
            $serviceType->delete();
            $this->dispatch('showToastr',
                type: 'success',
                message: 'Tipo de servicio eliminado exitosamente'
            );
            $this->dispatch('refreshServiceTypes');
        }
    }

    public function render()
    {
        $data = ServiceType::query()
            ->when($this->search, function (Builder $query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('description', 'like', '%'.$this->search.'%');
                });
            })
            ->when($this->statusFilter !== 'all', function (Builder $query) {
                $query->where('status', $this->statusFilter);
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->pagination);

        return view('livewire.service-type.data-table', ['data' => $data]);
    }
}
