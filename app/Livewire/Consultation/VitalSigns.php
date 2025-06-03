<?php

namespace App\Livewire\Consultation;

use App\Models\ClinicalObservationType;
use App\Models\Encounter;
use Livewire\Component;

class VitalSigns extends Component
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

        $this->items = ClinicalObservationType::whereCategory('vital_sign')->get();

        foreach ($this->items as $i){
            $this->saved[$i->code]=false;
            $result = $this->encounter->vitalSigns()->whereCode($i->code)->first();
            $this->values[$i->code]='';
            if($result) $this->values[$i->code] = $result->value;
        }
    }

    public function render()
    {
        return view('livewire.consultation.vital-signs');
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
            $vs = $this->encounter->vitalSigns()->whereEncounterId($this->encounter_id)->whereCode($code)->first();

            if(!$vs){
                $vsType = ClinicalObservationType::whereCode($code)->first();
                $this->encounter->vitalSigns()->create([
                    'fhir_id' => 'observation-' . fake()->uuid(),
                    'code' => $code,
                    'status' => 'final',
                    'category' => 'vital-signs',
                    'value' =>$this->values[$code],
                    'unit' => $vsType->default_unit,
                    'effective_date' => now(),
                    'issued_date' => now(),
                    'patient_id' => $this->encounter->patient_id,
                    'practitioner_id' => $this->encounter->practitioner_id,
                ]);
            }else{
                $vs->value =$this->values[$code];
                $vs->save();
            }
            // Simular tiempo de guardado
            sleep(1);
            $this->saved[$code] = true;

        } catch (\Exception $e) {
            session()->flash('error', 'Error al guardar: ' . $e->getMessage());
        }
    }
}
