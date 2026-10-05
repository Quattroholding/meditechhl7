<?php

namespace App\Livewire\Finance\AccountsReceivable;

use App\Services\Finance\AccountsReceivableService;
use Carbon\Carbon;
use Livewire\Component;

class ReceivablesAgingReport extends Component
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
        $this->authorize('receivables.view');
        $this->asOfDate = now()->toDateString();
        $this->generateReport();
    }

    public function generateReport(): void
    {
        $clientId = auth()->user()->getCurrentClient()->id;
        $service = app(AccountsReceivableService::class);

        $report = $service->getAgingReport($clientId, Carbon::parse($this->asOfDate));

        $this->summary = [
            '0-30' => $report['0-30'],
            '31-60' => $report['31-60'],
            '61-90' => $report['61-90'],
            '90+' => $report['90+'],
            'total' => $report['total'],
        ];

        $this->ageingData = $report['items'];
    }

    public function updatedAsOfDate(): void
    {
        $this->generateReport();
    }

    public function render()
    {
        return view('livewire.finance.accounts-receivable.receivables-aging-report', [
            'ageingData' => $this->ageingData,
            'summary' => $this->summary,
        ]);
    }
}
