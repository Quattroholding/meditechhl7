<?php

namespace App\Livewire\Consultation;

use App\Models\Client;
use App\Models\Encounter;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Livewire\Component;
use Livewire\WithPagination;

class DataTable extends Component
{
    use WithPagination;

    public $model; // Modelo dinámico

    public $class;

    public $columns = []; // Columnas a mostrar

    public $actions = [];

    public $search; // Búsqueda

    public $statusFilter = ''; // Filtro de status (deprecated, usar methodFilter)

    public $methodFilter = ''; // Filtro de status

    public $sortField = 'id'; // Ordenación por defecto

    public $sortDirection = 'desc'; // Dirección de orden

    public $pagination;

    public $count = 0;

    public $route_name;

    public $title = '';

    public $showModal = false;

    public $selectedPatient;

    public $note;

    public $patient_id;

    public $practitioner_id;

    public $clientFilter = '';

    public $dateFilter = '';

    public function mount($pagination = 10, $sortField = 'encounters.id', $sortDirection = 'desc', $routename = '', $title = '')
    {
        $this->class = new Encounter;
        $this->pagination = $pagination;
        $this->route_name = $routename;
        $this->sortField = $sortField;
        $this->sortDirection = $sortDirection;
        $this->title = $title;
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function updatedMethodFilter()
    {
        // Mantener sincronizado statusFilter para compatibilidad
        $this->statusFilter = $this->methodFilter;
        $this->resetPage();
    }

    public function updatedClientFilter()
    {
        $this->resetPage();
    }

    public function updatedDateFilter()
    {
        $this->resetPage();
    }

    /**
     * Obtener rango de fechas según el filtro seleccionado
     */
    private function getDateRange(): ?array
    {
        return match ($this->dateFilter) {
            'today' => [Carbon::today(), Carbon::today()->endOfDay()],
            'tomorrow' => [Carbon::tomorrow(), Carbon::tomorrow()->endOfDay()],
            'this_week' => [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()],
            'this_month' => [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()],
            default => null,
        };
    }

    public function render(Request $request)
    {
        $data = Encounter::selectRaw('encounters.*')
            ->join('patients', 'patients.id', '=', 'encounters.patient_id')
            ->join('practitioners', 'practitioners.id', '=', 'encounters.practitioner_id')
            ->join('appointments', 'encounters.appointment_id', '=', 'appointments.id')
            ->when($this->search, function (Builder $query) {
                $query->where(function ($q) {
                    $q->orWhere('encounters.status', 'like', '%'.$this->search.'%');
                    $q->orWhere('encounters.identifier', 'like', '%'.$this->search.'%');
                    $q->orWhere('encounters.start', 'like', '%'.$this->search.'%');
                    $q->orWhere('encounters.end', 'like', '%'.$this->search.'%');
                    $q->orWhere('patients.name', 'like', '%'.$this->search.'%');
                    $q->orWhere('practitioners.name', 'like', '%'.$this->search.'%');
                });
            })
            ->when(! empty($this->methodFilter) || ! empty($this->statusFilter), function ($q) {
                $filterValue = ! empty($this->methodFilter) ? $this->methodFilter : $this->statusFilter;
                $q->where('encounters.status', $filterValue);
            })
            ->when(! empty($request->status), function ($q) use ($request) {
                $q->where('encounters.status', $request->status);
            })
            ->when(! empty($request->patient_id), function ($q) use ($request) {
                $q->where('encounters.patient_id', $request->patient_id);
            })
            ->when(! empty($this->practitioner_id), function ($q) use ($request) {
                $q->where('encounters.practitioner_id', $request->practitioner_id);
            })
            ->when(! empty($this->clientFilter), function ($q) {
                $q->where('appointments.client_id', $this->clientFilter);
            })
            ->when(! empty($this->dateFilter), function ($q) {
                $dateRange = $this->getDateRange();
                if ($dateRange) {
                    $q->whereBetween('encounters.start', $dateRange);
                }
            })
            ->whereNull('appointments.deleted_at')
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->pagination);

        $clients = auth()->user()->hasRole('admin') ? Client::all() : [];

        return view('livewire.consultation.data-table', [
            'data' => $data,
            'clients' => $clients,
        ]);
    }
}
