<?php

namespace App\Livewire\HemoScreen;

use App\Models\HemoScreenStandaloneResult;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class StandaloneDashboard extends Component
{
    use WithPagination;

    // Filters
    public $dateFrom = '';

    public $dateTo = '';

    public $deviceSerial = '';

    public $showAbnormalOnly = false;

    public $selectedResults = [];

    // Chart parameters
    public $chartParameter = '718-7'; // Default: Hemoglobin

    public $chartDays = 30;

    // Available chart parameters
    public $chartParameters = [
        '718-7' => 'Hemoglobin',
        '6690-2' => 'WBC',
        '789-8' => 'RBC',
        '20570-8' => 'Hematocrit',
        '777-3' => 'Platelets',
    ];

    protected $queryString = [
        'dateFrom' => ['except' => ''],
        'dateTo' => ['except' => ''],
        'deviceSerial' => ['except' => ''],
        'showAbnormalOnly' => ['except' => false],
    ];

    public function mount()
    {
        // Set default date range to last 30 days
        if (empty($this->dateFrom)) {
            $this->dateFrom = now()->subDays(30)->format('Y-m-d');
        }

        if (empty($this->dateTo)) {
            $this->dateTo = now()->format('Y-m-d');
        }
    }

    public function updatingDateFrom()
    {
        $this->resetPage();
    }

    public function updatingDateTo()
    {
        $this->resetPage();
    }

    public function updatingDeviceSerial()
    {
        $this->resetPage();
    }

    public function updatingShowAbnormalOnly()
    {
        $this->resetPage();
    }

    public function updatedChartParameter()
    {
        $this->dispatch('chartUpdated', [
            'chartData' => $this->chartData,
            'chartParameter' => $this->chartParameter,
        ]);
    }

    public function updatedChartDays()
    {
        $this->dispatch('chartUpdated', [
            'chartData' => $this->chartData,
            'chartParameter' => $this->chartParameter,
        ]);
    }

    public function resetFilters()
    {
        $this->dateFrom = now()->subDays(30)->format('Y-m-d');
        $this->dateTo = now()->format('Y-m-d');
        $this->deviceSerial = '';
        $this->showAbnormalOnly = false;
        $this->resetPage();
    }

    public function getResultsProperty()
    {
        $query = HemoScreenStandaloneResult::query()
            ->forUser(Auth::id())
            ->with('practitioner')
            ->latestFirst();

        // Apply date range filter
        if ($this->dateFrom && $this->dateTo) {
            $query->dateRange($this->dateFrom, $this->dateTo.' 23:59:59');
        }

        // Apply device serial filter
        if ($this->deviceSerial) {
            $query->byDevice($this->deviceSerial);
        }

        // Apply abnormal only filter
        if ($this->showAbnormalOnly) {
            $query->where(function ($q) {
                // This is a simplified filter - we'll check abnormal values in the result display
                // For better performance, you could add a 'has_abnormal' column to the table
            });
        }

        return $query->paginate(25);
    }

    public function getChartDataProperty()
    {
        $results = HemoScreenStandaloneResult::query()
            ->forUser(Auth::id())
            ->where('test_performed_at', '>=', now()->subDays($this->chartDays))
            ->orderBy('test_performed_at', 'asc')
            ->get();

        $chartData = [
            'labels' => [],
            'values' => [],
            'referenceMin' => null,
            'referenceMax' => null,
        ];

        if ($results->isEmpty()) {
            return $chartData;
        }

        // Get reference ranges for the selected parameter
        $referenceRanges = $results->first()->getReferenceRanges();
        $reference = $referenceRanges[$this->chartParameter] ?? null;

        if ($reference) {
            $chartData['referenceMin'] = $reference['min'];
            $chartData['referenceMax'] = $reference['max'];
        }

        foreach ($results as $result) {
            $observation = $result->getObservationByCode($this->chartParameter);

            if ($observation) {
                $chartData['labels'][] = $result->test_performed_at->format('d/m H:i');
                $chartData['values'][] = (float) $observation['value'];
            }
        }

        return $chartData;
    }

    public function getAvailableDevicesProperty()
    {
        return HemoScreenStandaloneResult::query()
            ->forUser(Auth::id())
            ->select('device_serial')
            ->distinct()
            ->pluck('device_serial');
    }

    public function exportToPdf()
    {
        $resultIds = $this->selectedResults;

        if (empty($resultIds)) {
            $this->dispatch('alert', [
                'type' => 'warning',
                'message' => 'Seleccione al menos un resultado para exportar',
            ]);

            return;
        }

        // Redirect to PDF export route
        return redirect()->route('hemoscreen.export-pdf', ['ids' => implode(',', $resultIds)]);
    }

    public function exportToCsv()
    {
        // Redirect to CSV export route
        return redirect()->route('hemoscreen.export-csv', [
            'dateFrom' => $this->dateFrom,
            'dateTo' => $this->dateTo,
            'deviceSerial' => $this->deviceSerial,
        ]);
    }

    public function render()
    {
        return view('livewire.hemo-screen.standalone-dashboard', [
            'results' => $this->results,
            'chartData' => $this->chartData,
            'availableDevices' => $this->availableDevices,
            'totalResults' => HemoScreenStandaloneResult::forUser(Auth::id())->count(),
        ])->layout('layouts.app');
    }
}
