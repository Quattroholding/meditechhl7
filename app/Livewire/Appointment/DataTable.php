<?php

namespace App\Livewire\Appointment;

use App\Models\Appointment;
use App\Models\Client;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Component;
use Livewire\WithPagination;

class DataTable extends Component
{
    use WithPagination;

    public $search = '';

    public $sortField = 'id';

    public $sortDirection = 'desc';

    public $pagination = 10;

    public $show_create = true;

    public $showModal = false;

    public $modalTitle = 'Confirmar Cita';

    public $methodFilter = '';

    public $clientFilter = '';

    public $dateFilter = '';

    protected $queryString = [
        'search' => ['except' => ''],
    ];

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedMethodFilter()
    {
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

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }

        $this->sortField = $field;
        $this->resetPage();
    }

    public function updatingSearch()
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

    /**
     * Obtener variaciones de búsqueda por fecha
     * Convierte formatos de fecha latinos (dd-mm-yyyy) a formato de BD (yyyy-mm-dd)
     */
    private function getSearchVariations(): array
    {
        $variations = [$this->search];

        // Intentar parsear como fecha en formato latino (dd-mm-yyyy)
        if (preg_match('/^\d{2}-\d{2}-\d{4}$/', $this->search)) {
            try {
                $date = Carbon::createFromFormat('d-m-Y', $this->search);
                $variations[] = $date->format('Y-m-d');
            } catch (\Exception $e) {
                // Si no se puede parsear, continuar sin la variación
            }
        }

        // Intentar parsear como fecha en formato yyyy-mm-dd
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $this->search)) {
            try {
                $date = Carbon::createFromFormat('Y-m-d', $this->search);
                // También agregar en formato latino
                $variations[] = $date->format('d-m-Y');
            } catch (\Exception $e) {
                // Si no se puede parsear, continuar sin la variación
            }
        }

        return array_unique($variations);
    }

    public function getDataProperty()
    {
        return Appointment::query()->selectRaw('appointments.*')
            ->leftJoin('patients', 'patients.id', '=', 'appointments.patient_id')
            ->leftJoin('practitioners', 'practitioners.id', '=', 'appointments.practitioner_id')
            ->when($this->search, function (Builder $query) {
                $variations = $this->getSearchVariations();
                $query->where(function ($q) use ($variations) {
                    // Búsqueda en campos de texto
                    $q->orWhere('service_type', 'like', '%'.$this->search.'%');
                    $q->orWhere('status', 'like', '%'.$this->search.'%');
                    $q->orWhereRaw("patients.name like '%".$this->search."%'");
                    $q->orWhereRaw("practitioners.name like '%".$this->search."%'");

                    // Búsqueda por fecha - probar todas las variaciones
                    foreach ($variations as $variation) {
                        $q->orWhere('start', 'like', '%'.$variation.'%');
                        $q->orWhere('end', 'like', '%'.$variation.'%');
                    }
                });
            })
            ->when(! empty($this->patient_id), function ($q) {
                $q->where('patient_id', $this->patient_id);
            })
            ->when(! empty($this->practitioner_id), function ($q) {
                $q->where('practitioner_id', $this->practitioner_id);
            })
            ->when(! empty($this->patient_id), function ($q) {
                $q->wherePatientId($this->patient_id);
            })
            ->when(request()->has('status'), function ($q) {
                $q->whereStatus(request()->get('status'));
            })
            ->when(request()->has('id'), function ($q) {
                $q->whereId(request()->get('id'));
            })
            ->when(! empty($this->methodFilter), function ($q) {
                $q->where('status', $this->methodFilter);
            })
            ->when(! empty($this->clientFilter), function ($q) {
                $q->where('client_id', $this->clientFilter);
            })
            ->when(! empty($this->dateFilter), function ($q) {
                $dateRange = $this->getDateRange();
                if ($dateRange) {
                    $q->whereBetween('start', $dateRange);
                }
            })
            ->when(! empty($this->limit), function ($q) {
                $q->take($this->limit);
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->pagination);

    }

    public function render()
    {
        try {
            $data = $this->data;
            $clients = auth()->user()->hasRole('admin') ? Client::all() : [];

            return view('livewire.appointment.data-table', [
                'data' => $data,
                'clients' => $clients,
            ]);
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Invoice DataTable Error: '.$e->getMessage());

            // Return an empty collection to prevent infinite loading
            return view('livewire.appointment.data-table', [
                'invoices' => new LengthAwarePaginator([], 0, 10),
            ]);
        }
    }

    public function editAppointment($appointmentId)
    {
        $this->modalTitle = 'Actualizar Cita';
        $this->dispatch('editAppointmentModal', $appointmentId);
    }
}
