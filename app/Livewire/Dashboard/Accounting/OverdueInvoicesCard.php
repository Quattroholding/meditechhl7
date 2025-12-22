<?php

namespace App\Livewire\Dashboard\Accounting;

use App\Enums\InvoiceStatus;
use App\Models\ClientInvoice;
use Livewire\Component;

class OverdueInvoicesCard extends Component
{
    public $count = 0;

    public $totalAmount = 0;

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        $overdueInvoices = ClientInvoice::query()
            ->where('status', InvoiceStatus::OVERDUE)
            ->get();

        $this->count = $overdueInvoices->count();
        $this->totalAmount = $overdueInvoices->sum('total');
    }

    public function render()
    {
        return view('livewire.dashboard.accounting.overdue-invoices-card');
    }
}
