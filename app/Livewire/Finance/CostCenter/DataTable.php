<?php

namespace App\Livewire\Finance\CostCenter;

use App\Models\Finance\CostCenter;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Livewire\WithPagination;

class DataTable extends Component
{
    use WithPagination;

    public $search = '';

    public $sortField = 'code';

    public $sortDirection = 'asc';

    public $pagination = 10;

    public $statusFilter = 'all'; // all, active, inactive

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => 'all'],
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

    public function render(): View
    {
        $data = CostCenter::query()
            ->when($this->search, function (Builder $query) {
                $query->where(function ($q) {
                    $q->where('code', 'like', '%'.$this->search.'%')
                        ->orWhere('name', 'like', '%'.$this->search.'%');
                });
            })
            ->when($this->statusFilter !== 'all', function (Builder $query) {
                $query->where('status', $this->statusFilter === 'active' ? 'active' : 'inactive');
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->pagination);

        return view('livewire.finance.cost-center.data-table', [
            'data' => $data,
        ]);
    }
}
