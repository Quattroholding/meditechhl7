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
    public $saving = false;
    public $savedDescription = false;
    public $savedAlleviatingFactors = false;
    public $savedAggravatingFactors = false;
    public $savedAssociatedSymptoms = false;

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

    public function create(){
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
    }

    public function save($type,$value){

        if($type=='location') $this->location = $value;
        if($type=='severity') $this->severity = $value;
        if($type=='duration') $this->duration = $value;
        if($type=='timing') $this->timing = $value;

        if(!$this->encounter->presentIllnesses){
          $this->create();
        }else{
            $this->encounter->presentIllnesses->location = $this->location;
            $this->encounter->presentIllnesses->severity = $this->severity;
            $this->encounter->presentIllnesses->duration = $this->duration;
            $this->encounter->presentIllnesses->timing = $this->timing;
            $this->encounter->presentIllnesses->save();
        }
    }
    public function updatedDescription(){
        $this->savedDescription = false;
    }
    public function updatedAggravatingFactors(){
        $this->savedAggravatingFactors = false;
    }
    public function updatedAlleviatingFactors(){
        $this->savedAlleviatingFactors = false;
    }
    public function updatedAssociatedSymptoms(){
        $this->savedAssociatedSymptoms = false;
    }

    public function saveDescription(){

        $this->savedDescription = false;
        // Simular guardado en base de datos
        // Aquí puedes guardar en tu modelo específico
        try {
            if(!$this->encounter->presentIllnesses){
                $this->create();
            }else {
                $this->present_illness->description = $this->description;
                $this->present_illness->save();
                // Simular tiempo de guardado
                sleep(1);
                $this->savedDescription = true;
            }

        } catch (\Exception $e) {
            session()->flash('error', 'Error al guardar: ' . $e->getMessage());
        }
    }
    public function saveAggravatingFactors(){

        $this->savedAggravatingFactors = false;
        // Simular guardado en base de datos
        // Aquí puedes guardar en tu modelo específico
        try {
            if(!$this->encounter->presentIllnesses){
                $this->create();
            }else{
                $this->present_illness->aggravating_factors = $this->aggravating_factors;
                $this->present_illness->save();
                // Simular tiempo de guardado
                sleep(1);
                $this->savedAggravatingFactors = true;
            }

        } catch (\Exception $e) {
            session()->flash('error', 'Error al guardar: ' . $e->getMessage());
        }
    }
    public function saveAlleviatingFactors(){

        $this->savedAlleviatingFactors = false;
        // Simular guardado en base de datos
        // Aquí puedes guardar en tu modelo específico
        try {
            if(!$this->encounter->presentIllnesses){
                $this->create();
            }else {
                $this->present_illness->alleviating_factors = $this->alleviating_factors;
                $this->present_illness->save();
                // Simular tiempo de guardado
                sleep(1);
                $this->savedAlleviatingFactors = true;
            }

        } catch (\Exception $e) {
            session()->flash('error', 'Error al guardar: ' . $e->getMessage());
        }
    }
    public function saveAssociatedSymptoms(){

        $this->savedAssociatedSymptoms = false;
        // Simular guardado en base de datos
        // Aquí puedes guardar en tu modelo específico
        try {
            if(!$this->encounter->presentIllnesses){
                $this->create();
            }else {
                $this->present_illness->associated_symptoms = $this->associated_symptoms;
                $this->present_illness->save();
                // Simular tiempo de guardado
                sleep(1);
                $this->savedAssociatedSymptoms = true;
            }

        } catch (\Exception $e) {
            session()->flash('error', 'Error al guardar: ' . $e->getMessage());
        }
    }
}
