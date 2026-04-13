<?php

namespace App\Livewire\Quotation;

use App\Models\Quotation;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Livewire\WithPagination;

class DataTable extends Component
{
    use WithPagination;

    protected $listeners = ['refreshQuotations' => '$refresh'];

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
        $this->dispatch('openQuotationModal', title: 'Crear Cotización');
    }

    public function openEditModal($quotationId): void
    {
        $this->dispatch('editQuotationModal', quotation_id: $quotationId);
    }

    public function deleteQuotation($id): void
    {
        $quotation = Quotation::find($id);

        if ($quotation && $quotation->canBeEdited()) {
            $quotation->delete();
            $this->dispatch('showToastr',
                type: 'success',
                message: 'Cotización eliminada exitosamente'
            );
            $this->dispatch('refreshQuotations');
        } else {
            $this->dispatch('showToastr',
                type: 'error',
                message: 'No se puede eliminar esta cotización'
            );
        }
    }

    public function render()
    {
        $data = Quotation::query()
            ->with(['items', 'creator'])
            ->when($this->search, function (Builder $query) {
                $query->where(function ($q) {
                    $q->where('quotation_number', 'like', '%'.$this->search.'%')
                        ->orWhere('client_company_name', 'like', '%'.$this->search.'%')
                        ->orWhere('client_ruc', 'like', '%'.$this->search.'%');
                });
            })
            ->when($this->statusFilter !== 'all', function (Builder $query) {
                $query->where('status', $this->statusFilter);
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->pagination);

        return view('livewire.quotation.data-table', ['data' => $data]);
    }
}
