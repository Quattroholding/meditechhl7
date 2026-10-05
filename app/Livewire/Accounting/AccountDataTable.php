<?php

namespace App\Livewire\Accounting;

use App\Models\Accounting\AccountingAccount;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Livewire\WithPagination;

class AccountDataTable extends Component
{
    use WithPagination;

    public $search = '';

    public $sortField = 'code';

    public $sortDirection = 'asc';

    public $pagination = 15;

    public $statusFilter = 'all'; // all, active, inactive

    public $typeFilter = 'all'; // all, asset, liability, equity, income, expense, cost

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => 'all'],
        'typeFilter' => ['except' => 'all'],
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

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingTypeFilter()
    {
        $this->resetPage();
    }

    public function render(): View
    {
        $data = AccountingAccount::query()
            ->when($this->search, function (Builder $query) {
                $query->where(function ($q) {
                    $q->where('code', 'like', '%'.$this->search.'%')
                        ->orWhere('name', 'like', '%'.$this->search.'%');
                });
            })
            ->when($this->statusFilter !== 'all', function (Builder $query) {
                $query->where('status', $this->statusFilter === 'active' ? 'active' : 'inactive');
            })
            ->when($this->typeFilter !== 'all', function (Builder $query) {
                $query->where('account_type', $this->typeFilter);
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->pagination);

        return view('livewire.accounting.account-data-table', [
            'data' => $data,
        ]);
    }
}
