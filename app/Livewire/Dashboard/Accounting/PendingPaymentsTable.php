<?php

namespace App\Livewire\Dashboard\Accounting;

use App\Enums\PaymentStatus;
use App\Models\ClientInvoicePayment;
use Livewire\Component;

class PendingPaymentsTable extends Component
{
    public $payments = [];

    public function mount()
    {
        $this->loadPayments();
    }

    public function loadPayments()
    {
        $this->payments = ClientInvoicePayment::query()
            ->where('status', PaymentStatus::PENDING)
            ->with(['invoice.client', 'invoice.subscription.package'])
            ->orderBy('payment_date', 'asc')
            ->limit(10)
            ->get();
    }

    public function render()
    {
        return view('livewire.dashboard.accounting.pending-payments-table');
    }
}
