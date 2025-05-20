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

    public function mount($pagination=10,$sortField='id',$sortDirection='asc',$routename='',$title='')
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
        $data = Appointment::query()
            ->when($this->search, function (Builder $query) {
                $query->where(function ($q) { // Asegura que las condiciones sean correctas

                      $q->whereHas('patient',function ($q2){
                          $q2->orWhereRaw("patients.name like '%" . $this->search . "%'");
                      });
                    $q->whereHas('practitioner',function ($q2){
                        $q2->orWhereRaw("practitioners.name like '%" . $this->search . "%'");
                    });
                    $q->orWhere('service_type', 'like', '%' . $this->search . '%');
                    $q->orWhere('start_date', 'like', '%' . $this->search . '%');
                    $q->orWhere('status', 'like', '%' . $this->search . '%');
                });
            })
            ->when(Schema::hasColumn($this->class->getTable(),$this->sortField),function ($q){
                $q->orderBy($this->sortField, $this->sortDirection);
            })
            ->when(!empty($this->patient_id),function ($q){
                $q->where('patient_id',$this->patient_id);
            })
            ->paginate($this->pagination);

        return view('livewire.appointment.data-table', [ 'data' => $data, ]);
    }
}
