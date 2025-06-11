<?php

namespace App\Livewire\Practitioner;

use App\Models\MedicalSpeciality;
use App\Models\Practitioner;
use Illuminate\Support\Str;
use Livewire\Component;

class AddQualification extends Component
{
    public $showModal=false;
    public $practitioner_id;
    public $practitoner;
    public $medical_speciality_id;
    public $period_start;
    public $period_end;

    protected $rules = [
        'practitioner_id' => 'required|exists:practitioners,id',
        'medical_speciality_id' => 'required',
        'period_start' => 'required|date',
        'period_end' => 'required|date'
    ];

    protected $messages = [
        'medical_speciality_id.required' => 'Debe seleccionar un especialidad',
        'period_start.required' => 'Debe seleccionar una fecha de inicio',
        'period_end.required' => 'Debe seleccionar una fecha de finalizacion',
    ];

    public function render()
    {
        return view('livewire.practitioner.add-qualification');
    }

    public function mount(){
        $this->practitoner = Practitioner::find($this->practitioner_id);
    }

    public function openModal(){
        $this->showModal=true;
    }

    public function save(){

        $this->validate();

        $especialidad = MedicalSpeciality::find($this->medical_speciality_id);

        if(!$this->practitoner->qualifications()->whereMedicalSpecialityId($this->medical_speciality_id)->first()){
            $this->practitoner->qualifications()->create([
                'medical_speciality_id'=>$this->medical_speciality_id,
                'period_start' =>$this->period_start,
                'period_end'=>$this->period_end,
                'code'=>$this->medical_speciality_id,
                'system'=>'http://terminology.hl7.org/CodeSystem/v2-0360',
                'display'=>$especialidad->name,
                'created_at'=>now(),
                'updated_at'=>now(),
            ]);

            $this->showModal=false;
            $this->dispatch('loadQualifications');
            $this->dispatch('showToastr',
                type: 'success',
                message: '¡Guardado exitosamente!'
            );
            $this->reset(['medical_speciality_id', 'period_start','period_end']);
        }else{
            $this->dispatch('showToastr',
                type: 'error',
                message: '¡Esta especialidad ya se encuentra agregada!'
            );
        }
    }

    public function closeModal(){
        $this->showModal=false;
    }
}
