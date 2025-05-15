<?php

namespace App\Livewire\Consultation;

use App\Models\CptCode;
use App\Models\Encounter;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Livewire\Component;

class Procedures extends Component
{

    public $query = '';
    public $results = [];
    public $encounter_id;
    public $encounter;
    public $type='procedure';
    public $selectedLists=[];

    public function mount(){
        $this->encounter = Encounter::find($this->encounter_id);

        $this->selectedLists = $this->encounter->procedures()->whereIdentifier($this->type)->get();
    }

    public function updatedQuery()
    {
        if (strlen($this->query) < 2) {
            $this->results = [];
            return;
        }

        $response = Http::get( url('api/cpts/'.$this->type), [
            'dropdown'=>true,
            'q' => $this->query,
        ]);

        $this->results = $response->json() ?? [];
    }

    public function selectOption($option)
    {
        $this->selectedOption = $option;
        $this->query = $option['name']; // Asigna el nombre seleccionado al input
        $this->results = []; // Limpia los resultados
        $cpt = CptCode::whereId($option)->first();
        $procedure =  $this->encounter->procedures()->where('procedures.code',$cpt->code)->first();
        if(!$procedure){

            $this->encounter->procedures()->create([
                'fhir_id' => 'procedure-' . fake()->uuid(),
                'code' => $cpt->code,
                'identifier'=>$cpt->type,
                'status' => 'in-progress',
                'patient_id' => $this->encounter->patient_id,
                'practitioner_id' => $this->encounter->practitioner_id,
            ]);
        }

        $this->selectedLists = $this->encounter->procedures()->whereIdentifier($this->type)->get();
    }

    public function delete($id){
        $this->encounter->procedures()->whereId($id)->delete();
        $this->selectedLists = $this->encounter->procedures()->whereIdentifier($this->type)->get();
    }

    public function render()
    {
        return view('livewire.consultation.procedures');
    }
}
