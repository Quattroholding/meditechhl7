<?php

namespace App\Livewire\Accounting\Reports;

use App\Services\Accounting\FinancialReportService;
use Carbon\Carbon;
use Livewire\Attributes\Validate;
use Livewire\Component;

class BalanceSheet extends Component
{
    #[Validate]
    public string $as_of_date = '';

    protected FinancialReportService $reportService;

    public function mount(): void
    {
        $this->as_of_date = now()->toDateString();
        $this->reportService = app(FinancialReportService::class);
    }

    public function rules(): array
    {
        return [
            'as_of_date' => 'required|date',
        ];
    }

    protected $messages = [
        'as_of_date.required' => 'La fecha es obligatoria.',
        'as_of_date.date' => 'La fecha debe ser una fecha válida.',
    ];

    public function getReportProperty(): array
    {
        try {
            $this->validate();

            $clientId = auth()->user()->getCurrentClient()->id;
            $asOfDate = Carbon::parse($this->as_of_date);

            return $this->reportService->generateBalanceSheet($clientId, $asOfDate);
        } catch (\Exception $e) {
            return [
                'report_type' => 'balance_sheet',
                'error' => $e->getMessage(),
                'assets' => [],
                'liabilities' => [],
                'equity' => [],
            ];
        }
    }

    public function render()
    {
        return view('livewire.accounting.reports.balance-sheet', [
            'report' => $this->report,
        ]);
    }
}
