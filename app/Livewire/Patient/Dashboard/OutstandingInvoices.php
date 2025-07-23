<?php

namespace App\Livewire\Patient\Dashboard;

use App\Models\Invoice;
use Livewire\Component;

class OutstandingInvoices extends Component
{
    public $patient;
    public $limit = 5;
    public $order;
    public $isLoading = true;
    public $outstandingInvoices = [];
    public $totalDebt = 0;

    protected $listeners = ['loadData'];

    public function mount($limit = 5, $order = null)
    {
        $this->patient = auth()->user()->patient;
        $this->limit = $limit;
        $this->order = $order;
        // Initialize empty data to avoid errors during loading
        $this->outstandingInvoices = collect();
        $this->totalDebt = 0;
    }

    public function loadData()
    {
        $this->loadOutstandingInvoices();
        $this->loadTotalDebt();
        $this->isLoading = false;
    }

    public function loadOutstandingInvoices()
    {
        if (!$this->patient) {
            $this->outstandingInvoices = collect();
            return;
        }

        $this->outstandingInvoices = Invoice::where('patient_id', $this->patient->id)
            ->whereIn('payment_status', ['unpaid', 'partial'])
            ->with(['encounter', 'lineItems'])
            ->orderBy('due_date')
            ->limit($this->limit)
            ->get();
    }

    public function loadTotalDebt()
    {
        if (!$this->patient) {
            $this->totalDebt = 0;
            return;
        }

        $this->totalDebt = Invoice::where('patient_id', $this->patient->id)
            ->whereIn('payment_status', ['pending', 'partial'])
            ->sum('amount_due') ?? 0;
    }

    public function render()
    {
        return view('livewire.patient.dashboard.outstanding-invoices');
    }
}
