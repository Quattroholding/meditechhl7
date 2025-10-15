<?php

namespace App\Livewire\Patient;

use App\Models\Patient;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;

class DataTable extends Component
{
    use WithPagination;

    protected $listeners = ['insurance-modal-closed' => 'closeInsuranceModal'];

    public $model; // Modelo dinámico

    public $class;

    public $columns = []; // Columnas a mostrar

    public $actions = [];

    public $search; // Búsqueda

    public $sortField = 'id'; // Ordenación por defecto

    public $sortDirection = 'desc'; // Dirección de orden

    public $pagination = 10;

    public $count = 0;

    public $route_name;

    public $title = '';

    public $showModal = false;

    public $selectedPatient;

    public $showInsuranceModal = false;

    public $selectedPatientForInsurance;

    public $note;

    public function openModal($patientId)
    {

        $this->selectedPatient = Patient::find($patientId);
        $this->note = $this->selectedPatient->note ?? '';
        $this->showModal = true;
    }

    public function saveNote()
    {
        dd('save note data table');
        $this->validate(['note' => 'required|string|max:1000']);
        if (auth()->user()->hasRole('doctor')) {
            $this->selectedPatient->notes()->create([
                'practitioner_id' => auth()->user()->practitioner->id,
                'description' => $this->note,
                // 'type'=>'enfermeria',
                'fhir_id' => 'clinicalimpresion-'.Str::uuid(),
                'status' => 'completed',
            ]);

            $this->showModal = false;
            session()->flash('message.success', 'Nota del Paciente '.$this->selectedPatient->name.' guardada correctamente.');
        } else {
            session()->flash('message.error', 'No se pudo guardar la nota porque no está autorizado para hacer este procedimiento.');
        }
    }

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

    public function render()
    {
        $data = Patient::query()
            ->when($this->search, function (Builder $query) {
                $query->where(function ($q) { // Asegura que las condiciones sean correctas
                    $q->orWhere('birth_date', 'like', '%'.$this->search.'%');
                    $q->orWhere('identifier', 'like', '%'.$this->search.'%');
                    $q->orWhere('email', 'like', '%'.$this->search.'%');
                    $q->orWhere('name', 'like', '%'.$this->search.'%');
                });
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->pagination, ['*'], 'page');

        return view('livewire.patient.data-table', ['data' => $data]);
    }

    public function openModalNote($patientId)
    {
        $practitioner_id = null;
        if (auth()->user()->hasRole('doctor')) {
            $practitioner_id = auth()->user()->practitioner->id;
        }

        $this->dispatch('openModal', $patientId, $practitioner_id);
    }

    public function openInsuranceModal($patientId)
    {
        $this->selectedPatientForInsurance = Patient::find($patientId);
        $this->showInsuranceModal = true;
    }

    public function closeInsuranceModal()
    {
        $this->showInsuranceModal = false;
        $this->selectedPatientForInsurance = null;
    }
}
