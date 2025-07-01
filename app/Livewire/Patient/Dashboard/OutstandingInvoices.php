<?php

namespace App\Livewire\Patient\Dashboard;

use App\Models\Invoice;
use Livewire\Component;

class OutstandingInvoices extends Component
{
    public $patient;
    public $limit = 5;

    public function mount($limit = 5)
    {
        $this->patient = auth()->user()->patient;
        $this->limit = $limit;
    }

    public function getOutstandingInvoicesProperty()
    {
        if (!$this->patient) {
            return collect();
        }

        return Invoice::where('patient_id', $this->patient->id)
            ->whereIn('payment_status', ['unpaid', 'partial'])
            ->with(['encounter', 'lineItems'])
            ->orderBy('due_date')
            ->limit($this->limit)
            ->get();
    }

    public function getTotalDebtProperty()
    {
        if (!$this->patient) {
            return 0;
        }

        return Invoice::where('patient_id', $this->patient->id)
            ->whereIn('payment_status', ['pending', 'partial'])
            ->sum('amount_due') ?? 0;
    }

    public function render()
    {
        return view('livewire.patient.dashboard.outstanding-invoices');
    }
}
