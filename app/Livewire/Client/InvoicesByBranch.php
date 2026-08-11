<?php

namespace App\Livewire\Client;

use App\Models\Payment;
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
        $this->invoices = Payment::select(
            'branches.id',
            'branches.name',
            'branches.address',
            'branches.type',
            DB::raw('SUM(payments.amount) as total_payments'))
            ->join('invoices', 'payments.invoice_id', '=', 'invoices.id')
            ->join('branches', 'invoices.branch_id', '=', 'branches.id')
            ->groupBy('branches.id', 'branches.name', 'branches.address', 'branches.type')
            ->orderBy('total_payments', 'DESC')
            ->limit(5)
            ->get();
    }

    public function render()
    {
        return view('livewire.client.invoices-by-branch');
    }
}
