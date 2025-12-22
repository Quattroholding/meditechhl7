<?php

namespace App\Livewire\Dashboard\Accounting;

use App\Enums\PaymentStatus;
use App\Models\ClientInvoicePayment;
use Livewire\Component;

class PendingPaymentsCard extends Component
{
    public $count = 0;

    public $totalAmount = 0;

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        $pendingPayments = ClientInvoicePayment::query()
            ->where('status', PaymentStatus::PENDING)
            ->get();

        $this->count = $pendingPayments->count();
        $this->totalAmount = $pendingPayments->sum('amount');
    }

    public function render()
    {
        return view('livewire.dashboard.accounting.pending-payments-card');
    }
}
