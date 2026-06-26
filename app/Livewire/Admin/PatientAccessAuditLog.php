<?php

namespace App\Livewire\Admin;

use App\Models\PatientAccessLog;
use Livewire\Component;
use Livewire\WithPagination;

class PatientAccessAuditLog extends Component
{
    use WithPagination;

    public string $patientSearch = '';

    public string $userSearch = '';

    public string $actionType = '';

    public string $dateFrom = '';

    public string $dateTo = '';

    public int $perPage = 25;

    protected $queryString = [
        'patientSearch' => ['except' => ''],
        'userSearch' => ['except' => ''],
        'actionType' => ['except' => ''],
        'dateFrom' => ['except' => ''],
        'dateTo' => ['except' => ''],
    ];

    public function updatingPatientSearch(): void
    {
        $this->resetPage();
    }

    public function updatingUserSearch(): void
    {
        $this->resetPage();
    }

    public function updatingActionType(): void
    {
        $this->resetPage();
    }

    public function updatingDateFrom(): void
    {
        $this->resetPage();
    }

    public function updatingDateTo(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->patientSearch = '';
        $this->userSearch = '';
        $this->actionType = '';
        $this->dateFrom = '';
        $this->dateTo = '';
        $this->resetPage();
    }

    public function exportCsv()
    {
        $logs = $this->getLogs()->get();

        $csvData = "Timestamp,User Name,User Email,Patient Name,Action,Resource,IP Address,Metadata\n";

        foreach ($logs as $log) {
            $csvData .= sprintf(
                '"%s","%s","%s","%s","%s","%s","%s","%s"'."\n",
                $log->access_timestamp?->toIso8601String() ?? '',
                $log->user?->full_name ?? 'Unknown',
                $log->user?->email ?? 'N/A',
                $log->patient?->name ?? 'Unknown',
                $log->action_type,
                $log->resource_type,
                $log->ip_address ?? 'N/A',
                json_encode($log->metadata ?? [])
            );
        }

        return response()->streamDownload(
            function () use ($csvData) {
                echo $csvData;
            },
            'patient-access-audit-'.now()->format('Y-m-d-His').'.csv',
            ['Content-Type' => 'text/csv; charset=utf-8']
        );
    }

    private function getLogs()
    {
        $query = PatientAccessLog::with(['user', 'patient']);

        if ($this->patientSearch) {
            $query->whereHas('patient', fn ($q) => $q->where('name', 'like', '%'.$this->patientSearch.'%')
                ->orWhere('identifier', 'like', '%'.$this->patientSearch.'%')
            );
        }

        if ($this->userSearch) {
            $query->whereHas('user', fn ($q) => $q->where('full_name', 'like', '%'.$this->userSearch.'%')
                ->orWhere('email', 'like', '%'.$this->userSearch.'%')
            );
        }

        if ($this->actionType) {
            $query->where('action_type', $this->actionType);
        }

        if ($this->dateFrom) {
            $query->where('access_timestamp', '>=', $this->dateFrom.' 00:00:00');
        }

        if ($this->dateTo) {
            $query->where('access_timestamp', '<=', $this->dateTo.' 23:59:59');
        }

        return $query->orderByDesc('access_timestamp');
    }

    public function getStats()
    {
        $query = PatientAccessLog::query();

        if ($this->patientSearch) {
            $query->whereHas('patient', fn ($q) => $q->where('name', 'like', '%'.$this->patientSearch.'%')
                ->orWhere('identifier', 'like', '%'.$this->patientSearch.'%')
            );
        }

        if ($this->userSearch) {
            $query->whereHas('user', fn ($q) => $q->where('full_name', 'like', '%'.$this->userSearch.'%')
                ->orWhere('email', 'like', '%'.$this->userSearch.'%')
            );
        }

        if ($this->actionType) {
            $query->where('action_type', $this->actionType);
        }

        if ($this->dateFrom) {
            $query->where('access_timestamp', '>=', $this->dateFrom.' 00:00:00');
        }

        if ($this->dateTo) {
            $query->where('access_timestamp', '<=', $this->dateTo.' 23:59:59');
        }

        return [
            'total_accesses' => $query->count(),
            'unique_patients' => $query->select('patient_id')->distinct()->count(),
            'unique_users' => $query->select('user_id')->distinct()->count(),
        ];
    }

    public function render()
    {
        return view('livewire.admin.patient-access-audit-log', [
            'logs' => $this->getLogs()->paginate($this->perPage),
            'stats' => $this->getStats(),
            'actionTypes' => ['view', 'download', 'export', 'print'],
        ]);
    }
}
