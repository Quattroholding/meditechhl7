<?php

namespace App\Livewire\Patient;

use App\Models\MedicalHistory;
use App\Models\Patient;
use Illuminate\Support\Str;
use Livewire\Component;

class AddMedicalHistory extends Component
{
    public $showModal=false;
    public $patient_id;
    public $patient;
    public $category;
    public $title;
    public $description;
    public $occurrence_date;

    public function mount(){
        $this->patient = Patient::find($this->patient_id);
    }

    public function openModal(){
        $this->showModal=true;
    }

    public function save(){

        $this->patient->medicalHistories()->create([
            'fhir_id' => 'medicalhistory-' . Str::uuid(),
            'category'=>$this->category,
            'title'=>$this->title,
            'description'=>$this->description,
            'recorded_date'=>now(),
            'occurrence_date'=>$this->occurrence_date,
            'verification_status'=>'confirmed',
        ]);

        $this->showModal=false;

        $this->dispatch('showToastr',
            type: 'success',
            message: '¡Guardado exitosamente!'
        );

        $this->reset(['title', 'category','description','occurrence_date']);
    }

    public function render()
    {
        return view('livewire.patient.add-medical-history');
    }
}
