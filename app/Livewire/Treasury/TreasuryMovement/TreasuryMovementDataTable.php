<?php

namespace App\Livewire\Treasury\TreasuryMovement;

use App\Models\Treasury\TreasuryMovement;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Livewire\WithPagination;

class TreasuryMovementDataTable extends Component
{
    use WithPagination;

    public $search = '';

    public $sortField = 'movement_date';

    public $sortDirection = 'desc';

    public $pagination = 10;

    public $typeFilter = 'all'; // all, income, expense, transfer

    public $accountFilter = 'all'; // all, bank, cash

    protected $queryString = [
        'search' => ['except' => ''],
        'typeFilter' => ['except' => 'all'],
        'accountFilter' => ['except' => 'all'],
    ];

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

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingTypeFilter()
    {
        $this->resetPage();
    }

    public function updatingAccountFilter()
    {
        $this->resetPage();
    }

    public function render(): View
    {
        $data = TreasuryMovement::query()
            ->where('client_id', auth()->user()->getCurrentClient()->id)
            ->with(['bank', 'cashRegister'])
            ->when($this->search, function (Builder $query) {
                $query->where(function ($q) {
                    $q->where('movement_number', 'like', '%'.$this->search.'%')
                        ->orWhere('description', 'like', '%'.$this->search.'%')
                        ->orWhere('reference_number', 'like', '%'.$this->search.'%');
                });
            })
            ->when($this->typeFilter !== 'all', function (Builder $query) {
                $query->where('movement_type', $this->typeFilter);
            })
            ->when($this->accountFilter !== 'all', function (Builder $query) {
                if ($this->accountFilter === 'bank') {
                    $query->whereNotNull('bank_id');
                } elseif ($this->accountFilter === 'cash') {
                    $query->whereNotNull('cash_register_id');
                }
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->pagination);

        return view('livewire.treasury.movement.data-table', [
            'data' => $data,
        ]);
    }
}
