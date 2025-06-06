<?php

namespace App\Livewire\Appointment;

use App\Models\Appointment;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Schema;
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
    public $sortField = 'id'; // Ordenación por defecto
    public $sortDirection = 'asc'; // Dirección de orden
    public $pagination;

    public $count = 0;
    public $route_name;
    public $title='';
    public $patient_id;
    public $practitioner_id;
    public $limit;
    public $show_create=true;
    public $showModal=false;
    public $modalTitle='Confirmar Cita';

    public function mount($pagination=10,$sortField='appointments.id',$sortDirection='desc',$routename='',$title='')
    {
        $this->class = new Appointment();
        $this->pagination = $pagination;
        $this->route_name = $routename;
        $this->sortField =$sortField;
        $this->sortDirection =$sortDirection;
        $this->title=$title;
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

    public function render()
    {
        $query = Appointment::selectRaw('appointments.*')
            ->leftJoin('patients','patients.id','=','appointments.patient_id')
            ->leftJoin('practitioners','practitioners.id','=','appointments.practitioner_id')
            ->when($this->search, function (Builder $query) {
                $query->where(function ($q) {
                    $q->orWhere('service_type', 'like', '%' . $this->search . '%');
                    $q->orWhere('start', 'like', '%' . $this->search . '%');
                    $q->orWhere('end', 'like', '%' . $this->search . '%');
                    $q->orWhere('status', 'like', '%' . $this->search . '%');
                    $q->orWhereRaw("patients.name like '%" . $this->search . "%'");
                    $q->orWhereRaw("practitioners.name like '%" . $this->search . "%'");
                });
            })
            ->when(!empty($this->patient_id),function ($q){
                $q->where('patient_id',$this->patient_id);
            })
            ->when(!empty($this->practitioner_id),function ($q){
                $q->where('practitioner_id',$this->practitioner_id);
            })
            ->when(!empty($this->patient_id),function ($q){
                $q->wherePatientId($this->patient_id);
            })
            ->when(!empty($this->limit),function ($q){
                $q->take($this->limit);
            })
            ->orderBy($this->sortField, $this->sortDirection);

        if($this->pagination>0) {
            $data = $query->paginate($this->pagination);
        }else{
            $data = $query->get();
        }

        return view('livewire.appointment.data-table', [ 'data' => $data, ]);
    }

    public function editAppointment($appointmentId)
    {
        $this->modalTitle = 'Actualizar Cita';
        $this->dispatch('editAppointmentModal',$appointmentId);
    }
}
