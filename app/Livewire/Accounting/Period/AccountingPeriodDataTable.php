<?php

namespace App\Livewire\Accounting\Period;

use App\Models\Accounting\AccountingPeriod;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class AccountingPeriodDataTable extends Component
{
    use WithPagination;

    public string $search = '';

    public string $statusFilter = '';

    public string $yearFilter = '';

    public function mount(): void
    {
        $this->yearFilter = (string) now()->year;
    }

    #[On('period-created')]
    #[On('period-closed')]
    #[On('period-locked')]
    public function refresh(): void
    {
        $this->resetPage();
    }

    public function getPeriodsProperty()
    {
        $clientId = auth()->user()->getCurrentClient()->id;

        $query = AccountingPeriod::where('client_id', $clientId);

        if ($this->search) {
            $query->where('name', 'like', "%{$this->search}%");
        }

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        if ($this->yearFilter) {
            $query->where('fiscal_year', $this->yearFilter);
        }

        return $query->orderBy('fiscal_year', 'desc')
            ->orderBy('period_number', 'desc')
            ->paginate(15);
    }

    public function render()
    {
        return view('livewire.accounting.period.accounting-period-data-table', [
            'periods' => $this->periods,
        ]);
    }
}
