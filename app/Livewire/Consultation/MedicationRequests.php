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

    public function mount(){
        $this->encounter = Encounter::find($this->encounter_id);

        $this->selectedLists = $this->encounter->medicationRequests()->get();

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
            ]);


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
        $referral = $this->encounter->medicationRequests()->whereId($id)->first();
        $referral->$field = htmlspecialchars($value);
        $referral->save();
    }

    public function setEspecialist($specialist,$referral_id)
    {
        $referral = $this->encounter->medicationRequests()->whereId($referral_id)->first();
        $referral->referred_to_id = $specialist;
        $referral->save();
    }

    public function render()
    {
        return view('livewire.consultation.medication-requests');
    }
}
