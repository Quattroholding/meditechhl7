<?php

namespace App\Livewire\Appointment;

use App\Models\Appointment;
use Illuminate\Database\Eloquent\Builder;
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

    protected $queryString = [
        'search' => ['except' => ''],
    ];

    public function updatedSearch()
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

    public function getDataProperty()
    {
        return Appointment::query()->selectRaw('appointments.*')
            ->leftJoin('patients', 'patients.id', '=', 'appointments.patient_id')
            ->leftJoin('practitioners', 'practitioners.id', '=', 'appointments.practitioner_id')
            ->when($this->search, function (Builder $query) {
                $query->where(function ($q) {
                    $q->orWhere('service_type', 'like', '%'.$this->search.'%');
                    $q->orWhere('start', 'like', '%'.$this->search.'%');
                    $q->orWhere('end', 'like', '%'.$this->search.'%');
                    $q->orWhere('status', 'like', '%'.$this->search.'%');
                    $q->orWhereRaw("patients.name like '%".$this->search."%'");
                    $q->orWhereRaw("practitioners.name like '%".$this->search."%'");
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

            return view('livewire.appointment.data-table', ['data' => $data]);
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Invoice DataTable Error: '.$e->getMessage());

            // Return an empty collection to prevent infinite loading
            return view('livewire.appointment.data-table', [
                'invoices' => new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10),
            ]);
        }
    }

    public function editAppointment($appointmentId)
    {
        $this->modalTitle = 'Actualizar Cita';
        $this->dispatch('editAppointmentModal',$appointmentId);
    }
}
