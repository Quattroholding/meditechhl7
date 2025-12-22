<?php

namespace App\Livewire\Dashboard\Accounting;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\ClientInvoicePayment;
use Livewire\Component;

class PaymentMethodsChart extends Component
{
    public $labels = [];

    public $data = [];

    public $colors = [];

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        foreach (PaymentMethod::cases() as $method) {
            $total = ClientInvoicePayment::query()
                ->where('status', PaymentStatus::COMPLETED)
                ->where('payment_method', $method)
                ->whereYear('payment_date', now()->year)
                ->sum('amount');

            if ($total > 0) {
                $this->labels[] = $method->label();
                $this->data[] = $total;
                $this->colors[] = $this->getColorForMethod($method);
            }
        }
    }

    private function getColorForMethod($method): string
    {
        return match ($method) {
            PaymentMethod::ACH => '#0d6efd',
            PaymentMethod::YAPPY => '#20c997',
            PaymentMethod::BANK_TRANSFER => '#6610f2',
            PaymentMethod::CREDIT_CARD => '#fd7e14',
            PaymentMethod::CASH => '#198754',
            default => '#6c757d',
        };
    }

    public function render()
    {
        return view('livewire.dashboard.accounting.payment-methods-chart');
    }
}
