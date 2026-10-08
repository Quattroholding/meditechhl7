<?php

namespace App\Livewire\Accounting\Reports;

use App\Services\Accounting\FinancialReportService;
use Carbon\Carbon;
use Livewire\Attributes\Validate;
use Livewire\Component;

class IncomeStatement extends Component
{
    #[Validate]
    public string $start_date = '';

    #[Validate]
    public string $end_date = '';

    protected FinancialReportService $reportService;

    public function mount(): void
    {
        $this->start_date = now()->startOfMonth()->toDateString();
        $this->end_date = now()->toDateString();
        $this->reportService = app(FinancialReportService::class);
    }

    public function rules(): array
    {
        return [
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ];
    }

    protected $messages = [
        'start_date.required' => 'La fecha de inicio es obligatoria.',
        'end_date.required' => 'La fecha de fin es obligatoria.',
        'end_date.after_or_equal' => 'La fecha de fin debe ser igual o posterior a la fecha de inicio.',
    ];

    public function getReportProperty(): array
    {
        try {
            $this->validate();

            $clientId = auth()->user()->getCurrentClient()->id;
            $startDate = Carbon::parse($this->start_date);
            $endDate = Carbon::parse($this->end_date);

            return $this->reportService->generateIncomeStatement($clientId, $startDate, $endDate);
        } catch (\Exception $e) {
            return [
                'report_type' => 'income_statement',
                'error' => $e->getMessage(),
                'income' => [],
                'costs' => [],
                'expenses' => [],
            ];
        }
    }

    public function render()
    {
        return view('livewire.accounting.reports.income-statement', [
            'report' => $this->report,
        ]);
    }
}
