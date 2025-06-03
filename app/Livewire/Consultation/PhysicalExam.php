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
    public $saving = false;
    public $saved = [];

    public function mount(){
        $this->encounter = Encounter::find($this->encounter_id);

        $this->items = ClinicalObservationType::whereCategory('physical_exam')->get();

        foreach ($this->items as $i){
            $result = $this->encounter->physicalExams()->whereCode($i->code)->first();
            $this->values[$i->code]='';
            $this->saved[$i->code]=false;
            if($result) {
                foreach ($result->finding as $key=>$value){

                    $this->values[$i->code] .=$value;
                }
            }
        }
    }

    public function updatedValues($value, $code){
        $this->saved[$code] = false;
    }

    public function save($code)
    {
        $this->saved[$code] = false;
        // Simular guardado en base de datos
        // Aquí puedes guardar en tu modelo específico
        try {
            $vs = $this->encounter->physicalExams()->whereEncounterId($this->encounter_id)->whereCode($code)->first();

            if(!$vs){
                $vsType = ClinicalObservationType::whereCode($code)->first();
                $this->encounter->physicalExams()->create([
                    'fhir_id' => 'observation-' . fake()->uuid(),
                    'code' => $code,
                    'status' => 'final',
                    'category' => 'exam',
                    'description' => $vsType->name . ' realizado durante la consulta',
                    'finding' => array('text'=> $this->saved[$code]),
                    'effective_date' => now(),
                    'patient_id' => $this->encounter->patient_id,
                    'practitioner_id' => $this->encounter->practitioner_id,
                ]);
            }else{
                $vs->finding = array('text'=> $this->saved[$code]);
                $vs->save();
            }
            // Simular tiempo de guardado
            sleep(1);
            $this->saved[$code] = true;

        } catch (\Exception $e) {
            session()->flash('error', 'Error al guardar: ' . $e->getMessage());
        }
    }
    public function render()
    {
        return view('livewire.consultation.physical-exam');
    }
}
