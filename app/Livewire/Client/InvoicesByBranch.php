<?php

namespace App\Livewire\Client;

use App\Models\Invoice;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class InvoicesByBranch extends Component
{
    public $invoices;

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        $this->invoices = Invoice::select(
            'branches.id',
            'branches.name',
            'branches.address',
            'branches.type',
            DB::raw('SUM(invoices.total_amount) as total_invoices'))
            ->join('branches', 'invoices.branch_id', '=', 'branches.id')
            ->groupBy('branches.id', 'branches.name', 'branches.address', 'branches.type') // agregar todos los campos no agregados del SELECT
            ->orderBy('total_invoices', 'DESC')
            ->limit(5)
            ->get();
    }

    public function render()
    {
        return view('livewire.client.invoices-by-branch');
    }
}
