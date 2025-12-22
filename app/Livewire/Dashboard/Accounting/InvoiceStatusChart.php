<?php

namespace App\Livewire\Dashboard\Accounting;

use App\Enums\InvoiceStatus;
use App\Models\ClientInvoice;
use Livewire\Component;

class InvoiceStatusChart extends Component
{
    public $labels = [];

    public $counts = [];

    public $amounts = [];

    public $colors = [];

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        foreach (InvoiceStatus::cases() as $status) {
            $invoices = ClientInvoice::query()
                ->where('status', $status)
                ->get();

            $count = $invoices->count();

            if ($count > 0) {
                $this->labels[] = $status->label();
                $this->counts[] = $count;
                $this->amounts[] = $invoices->sum('total');
                $this->colors[] = $this->getColorForStatus($status);
            }
        }
    }

    private function getColorForStatus($status): string
    {
        return match ($status) {
            InvoiceStatus::PAID => '#198754',
            InvoiceStatus::PENDING => '#ffc107',
            InvoiceStatus::PARTIALLY_PAID => '#0dcaf0',
            InvoiceStatus::OVERDUE => '#dc3545',
            InvoiceStatus::CANCELLED => '#6c757d',
            InvoiceStatus::DRAFT => '#adb5bd',
            InvoiceStatus::REFUNDED => '#fd7e14',
        };
    }

    public function render()
    {
        return view('livewire.dashboard.accounting.invoice-status-chart');
    }
}
