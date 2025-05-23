<?php

namespace App\Livewire\Consultation;

use App\Models\Encounter;
use App\Models\Patient;
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
    public $showModal = false;
    public $selectedPatient;

    public $note;
    public $patient_id;
    public $practitioner_id;

    public function mount($pagination=10,$sortField='encounters.id',$sortDirection='asc',$routename='',$title='')
    {
        $this->class = new Encounter();
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
        $data = Encounter::selectRaw('encounters.*')
            ->join('patients','patients.id','=','encounters.patient_id')
            ->join('practitioners','practitioners.id','=','encounters.practitioner_id')
            ->when($this->search, function (Builder $query) {
                $query->where(function ($q) {
                    $q->orWhere('encounters.status', 'like', '%' . $this->search . '%');
                    $q->orWhere('encounters.identifier', 'like', '%' . $this->search . '%');
                    $q->orWhere('encounters.start', 'like', '%' . $this->search . '%');
                    $q->orWhere('encounters.end', 'like', '%' . $this->search . '%');
                    $q->orWhere('patients.name', 'like', '%' . $this->search . '%');
                    $q->orWhere('practitioners.name', 'like', '%' . $this->search . '%');
                });
            })
            ->when(!empty($this->patient_id),function ($q){
                $q->where('patient_id',$this->patient_id);
            })
            ->when(!empty($this->practitioner_id),function ($q){
                $q->where('practitioner_id',$this->practitioner_id);
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->pagination);

        return view('livewire.consultation.data-table', [ 'data' => $data, ]);
    }
}
