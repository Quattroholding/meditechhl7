<?php

namespace App\Livewire\Finance\AccountsPayable;

use App\Models\SupplierInvoice;
use Carbon\Carbon;
use Livewire\Component;

class PayablesAgingReport extends Component
{
    public string $asOfDate = '';

    public array $ageingData = [];

    public array $summary = [
        '0-30' => 0,
        '31-60' => 0,
        '61-90' => 0,
        '90+' => 0,
        'total' => 0,
    ];

    public function mount(): void
    {
        $this->authorize('payables.view');
        $this->asOfDate = now()->toDateString();
        $this->generateReport();
    }

    public function generateReport(): void
    {
        $clientId = auth()->user()->getCurrentClient()->id;
        $asOfDate = Carbon::parse($this->asOfDate);

        $invoices = SupplierInvoice::where('client_id', $clientId)
            ->where('status', '!=', 'paid')
            ->where('status', '!=', 'cancelled')
            ->with('supplier')
            ->get();

        $this->ageingData = [];
        $this->summary = [
            '0-30' => 0,
            '31-60' => 0,
            '61-90' => 0,
            '90+' => 0,
            'total' => 0,
        ];

        foreach ($invoices as $invoice) {
            $daysOverdue = $asOfDate->diffInDays($invoice->due_date);
            $range = $this->getRange($daysOverdue);

            $this->ageingData[] = [
                'supplier' => $invoice->supplier->legal_name,
                'invoice_number' => $invoice->invoice_number,
                'invoice_date' => $invoice->invoice_date->toDateString(),
                'due_date' => $invoice->due_date->toDateString(),
                'balance' => $invoice->balance,
                'days_overdue' => max(0, $daysOverdue),
                'range' => $range,
                'status' => $invoice->status,
            ];

            $this->summary[$range] += $invoice->balance;
            $this->summary['total'] += $invoice->balance;
        }
    }

    private function getRange(int $daysOverdue): string
    {
        if ($daysOverdue <= 30) {
            return '0-30';
        } elseif ($daysOverdue <= 60) {
            return '31-60';
        } elseif ($daysOverdue <= 90) {
            return '61-90';
        }

        return '90+';
    }

    public function updatedAsOfDate(): void
    {
        $this->generateReport();
    }

    public function render()
    {
        return view('livewire.finance.accounts-payable.payables-aging-report', [
            'ageingData' => $this->ageingData,
            'summary' => $this->summary,
        ]);
    }
}
