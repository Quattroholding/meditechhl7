<?php

namespace App\Livewire\Consultation;

use App\Models\ClinicalObservationType;
use App\Models\Encounter;
use Livewire\Component;

class PhysicalExam extends Component
{
    public $reason;
    public $encounter_id;
    public $encounter;
    public $items;
    public $values=[];

    public function mount(){
        $this->encounter = Encounter::find($this->encounter_id);

        $this->items = ClinicalObservationType::whereCategory('physical_exam')->get();

        foreach ($this->items as $i){
            $result = $this->encounter->physicalExams()->whereCode($i->code)->first();
            $this->values[$i->code]='';

            if($result) {
                foreach ($result->finding as $key=>$value){
                    $this->values[$i->code] .=$value;
                }
            }
        }
    }

    public function updatedValues($value, $code){

        $vs = $this->encounter->physicalExams()->whereEncounterId($this->encounter_id)->whereCode($code)->first();

        if(!$vs){
            $vsType = ClinicalObservationType::whereCode($code)->first();
            $this->encounter->physicalExams()->create([
                'fhir_id' => 'observation-' . fake()->uuid(),
                'code' => $code,
                'status' => 'final',
                'category' => 'exam',
                'description' => $vsType->name . ' realizado durante la consulta',
                'finding' => array('text'=>$value),
                'effective_date' => now(),
                'patient_id' => $this->encounter->patient_id,
                'practitioner_id' => $this->encounter->practitioner_id,
            ]);
        }else{
            $vs->finding = array('text'=>$value);
            $vs->save();
        }

    }
    public function render()
    {
        return view('livewire.consultation.physical-exam');
    }
}
