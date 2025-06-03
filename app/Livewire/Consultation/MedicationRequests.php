<?php

namespace App\Livewire\Consultation;

use App\Models\Encounter;
use App\Models\MedicalSpeciality;
use App\Models\MedicationRequest;
use App\Models\Medicine;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Livewire\Component;

class MedicationRequests extends Component
{

    public $query = '';
    public $results = [];
    public $encounter_id;
    public $encounter;
    public $selectedLists=[];
    public $dosage_texts=[];
    public $quantitys=[];
    public $refills=[];

    public $frecuencies=[];
    public $durations=[];
    public $routes=[];
    public $saved = false;

    public function mount(){
        $this->encounter = Encounter::find($this->encounter_id);

        $this->selectedLists = $this->encounter->medicationRequests()->get();

        foreach ($this->selectedLists as $sl){
            $this->frecuencies[$sl->id] = $sl->frecuency;
            $this->routes[$sl->id] = $sl->route;
            $this->durations[$sl->id] = $sl->duration;
            $this->quantitys[$sl->id] = $sl->quantity;
            $this->dosage_texts[$sl->id] = $sl->dosage_text;
        }

    }
    public function updatedQuery()
    {
        if (strlen($this->query) < 2) {
            $this->results = [];
            return;
        }

        $response = Http::get( url('api/medicines'), [
            'dropdown'=>true,
            'q' => $this->query,
        ]);

        $this->results = $response->json() ?? [];
    }

    public function selectOption($option)
    {
        $this->saved=false;
        $this->selectedOption = $option;
        $this->query = $option['name']; // Asigna el nombre seleccionado al input
        $this->results = []; // Limpia los resultados
        $medicine_request =MedicationRequest::whereEncounterId($this->encounter->id)->whereMedicationId($option)->first();
        $medicine = Medicine::whereId($option)->first();
        if(!$medicine_request){
            $this->encounter->medicationRequests()->create([
                'fhir_id' => 'medicationrequest-' . Str::uuid(),
                'identifier' => 'RX-' . fake()->unique()->numerify('#######'),
                'status' => 'active',
                'intent' => 'order',
                'medication_id' => $medicine->id,
                'valid_from' => now(),
                'valid_to' => now()->addDays(30),
                'patient_id' => $this->encounter->patient_id,
                'practitioner_id' => $this->encounter->practitioner_id,
                'dosage_instruction'
            ]);

            $this->query='';
            sleep(1);
            $this->saved=true;
        }


        $this->selectedLists = $this->encounter->medicationRequests()->get();
    }

    public function delete($id){
        $this->encounter->medicationRequests()->whereId($id)->delete();
        $this->selectedLists = $this->encounter->referrals()->get();
        $this->selectedLists = $this->encounter->medicationRequests()->get();
    }

    public function updateField($id,$value,$field)
    {
        if($field=='quantity') $this->quantitys[$id] = $value;
        if($field=='frequency') $this->frecuencies[$id] = $value;
        if($field=='duration') $this->durations[$id] = $value;
        if($field=='route') $this->routes[$id] = $value;

        $dosage_instructions = $this->generateDosageInstruction($id);

        $medicationRequest = $this->encounter->medicationRequests()->whereId($id)->first();
        $medicationRequest->$field = htmlspecialchars($value);
        $medicationRequest->dosage_instruction =$dosage_instructions;
        $medicationRequest->dosage_text =$dosage_instructions['text'];
        $medicationRequest->save();

        $this->dosage_texts[$id] =$dosage_instructions['text'];
    }

    protected function generateDosageInstruction($id)
    {
        $frequency ='';
        $route ='';
        $duration ='';
        $quantity ='';

        if(isset($this->frecuencies[$id])) $frequency = $this->frecuencies[$id];
        if(isset($this->routes[$id])) $route =$this->routes[$id];
        if(isset($this->durations[$id])) $duration = $this->durations[$id];
        if(isset($this->quantitys[$id])) $quantity = $this->quantitys[$id];

        $requestMedicine = $this->encounter->medicationRequests()->whereId($id)->first();
        $medicine_type='';
        if($requestMedicine) $medicine_type = $requestMedicine->medicine->type;

        return [
            'text' => $quantity . ' ' .$medicine_type .
                ' cada ' . $frequency. ' horas' .
                ' via ' . $route .
                ' por ' . $duration. ' dias',
            'route' => $route,
            'frequency' => $frequency,
            'duration' => $duration
        ];
    }

    public function render()
    {
        return view('livewire.consultation.medication-requests');
    }
}
