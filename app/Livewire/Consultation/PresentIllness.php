<?php

namespace App\Livewire\Consultation;

use App\Models\Encounter;
use App\Models\PresentIllnesType;
use Livewire\Component;

class PresentIllness extends Component
{
    public $present_illness;
    public $reason;
    public $encounter_id;
    public $encounter;
    public $location;
    public $severity;
    public $duration;
    public $timing;
    public $description;
    public $aggravating_factors;
    public $alleviating_factors;
    public $associated_symptoms;
    public $items=[];

    public function mount(){
        $this->encounter = Encounter::find($this->encounter_id);
        $this->present_illness = $this->encounter->presentIllnesses;
        $arr =['location'=>'Ubicación','severity'=>'Gravedad','duration'=>'Duración','timing'=>'Momento'];

        foreach ($arr as $key=>$value){
            $this->items[$key]['title'] =$value;
            $this->items[$key]['items'] = PresentIllnesType::whereType($key)->get();
            if($this->present_illness){
                $this->$key =  $this->encounter->presentIllnesses->$key;
            }
        }

        if($this->present_illness){
            $this->description = $this->present_illness->description;
            $this->aggravating_factors = $this->present_illness->aggravating_factors;
            $this->alleviating_factors = $this->present_illness->alleviating_factors;
            $this->associated_symptoms = $this->present_illness->associated_symptoms;
        }

    }

    public function render()
    {
        return view('livewire.consultation.present-illness');
    }

    public function save($type,$value){

        if($type=='location') $this->location = $value;
        if($type=='severity') $this->severity = $value;
        if($type=='duration') $this->duration = $value;
        if($type=='timing') $this->timing = $value;

        if(!$this->encounter->presentIllnesses){
            $this->present_illness = $this->encounter->presentIllnesses()->create([
                'fhir_id' => 'condition-' . fake()->uuid(),
                'description' => '',
                'location' => $this->location,
                'severity' => $this->severity,
                'duration' => $this->duration,
                'timing'   => $this->timing,
                'patient_id' => $this->encounter->patient_id,
                'practitioner_id' => $this->encounter->practitioner_id,
            ]);
        }else{
            $this->encounter->presentIllnesses->location = $this->location;
            $this->encounter->presentIllnesses->severity = $this->severity;
            $this->encounter->presentIllnesses->duration = $this->duration;
            $this->encounter->presentIllnesses->timing = $this->timing;
            $this->encounter->presentIllnesses->save();
        }
    }
    public function updatedDescription(){
        $this->present_illness->description = $this->description;
        $this->present_illness->save();
    }
    public function updatedAggravatingFactors(){
        $this->present_illness->aggravating_factors = $this->aggravating_factors;
        $this->present_illness->save();
    }
    public function updatedAlleviatingFactors(){
        $this->present_illness->alleviating_factors = $this->alleviating_factors;
        $this->present_illness->save();
    }
    public function updatedAssociatedSymptoms(){
        $this->present_illness->associated_symptoms = $this->associated_symptoms;
        $this->present_illness->save();
    }
}
