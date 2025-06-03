<?php

namespace App\Livewire\Consultation;

use App\Models\Encounter;
use App\Models\MedicalSpeciality;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Livewire\Component;

class Referral extends Component
{

    public $query = '';
    public $results = [];
    public $encounter_id;
    public $encounter;
    public $selectedLists=[];
    public $especialistas=[];
    public $selectedEspecialist=[];
    public $selectedReason=[];
    public $saving = false;
    public $saved = false;
    public $savedNota=false;
    public $savedEspecialist=false;
    public function mount(){
        $this->encounter = Encounter::find($this->encounter_id);

        $this->selectedLists = $this->encounter->referrals()->get();

        foreach ($this->selectedLists as $selected){
            $responseEspecialist = Http::get( url('api/practitioners'), [ 'dropdown'=>true,  'speciality_id' => $selected->code, ]);
            $this->especialistas[$selected->code] = $responseEspecialist->json() ?? [];
            $this->selectedEspecialist[$selected->code]=$selected->referred_to_id;
            $this->selectedReason[$selected->code]=$selected->reason;
        }
    }
    public function updatedQuery()
    {
        if (strlen($this->query) < 2) {
            $this->results = [];
            return;
        }

        $response = Http::get( url('api/medical_speciality'), [
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
        $referral =\App\Models\Referral::whereEncounterId($this->encounter->id)->whereCode($option)->first();
        $specialty = MedicalSpeciality::whereId($option)->first();
        if(!$referral){
            $this->encounter->referrals()->create([
                'fhir_id' => 'servicerequest-' . Str::uuid(),
                'identifier' => 'REF-' . fake()->unique()->numerify('#######'),
                'status' => 'active',
                'intent' => 'order',
                'priority' => 'asap',
                'code' => $specialty->id,
                'description' => "Referencia a especialista en $specialty->name",
                'occurrence_date' =>now(),
                'supporting_info' => [
                    'speciality_id' => $specialty->id,
                    'speciality_name' => $specialty->name,
                ],
                //'referred_to_id' => $referredTo->id, Aun no lo tengo pedeir en el siguiente paso
                'patient_id' => $this->encounter->patient_id,
                'practitioner_id' => $this->encounter->practitioner_id,
            ]);
            $responseEspecialist = Http::get( url('api/practitioners'), [
                'dropdown'=>true,
                'speciality_id' => $specialty->id,
            ]);
            $this->especialistas[$specialty->id] =$responseEspecialist->json() ?? [];
            $this->selectedEspecialist[$specialty->id]=null;
            $this->selectedReason[$specialty->id]=null;
        }

        $this->query='';
        sleep(1);
        $this->saved=true;

        $this->selectedLists = $this->encounter->referrals()->get();
    }

    public function delete($id){
        $this->encounter->referrals()->whereId($id)->delete();
        $this->selectedLists = $this->encounter->referrals()->get();
    }

    public function updatedReason(){
        $this->savedNota=false;
    }

     public function setReason($reason,$referral_id)
     {
         $this->savedNota=false;
         $referral = $this->encounter->referrals()->whereId($referral_id)->first();
         $referral->reason = htmlspecialchars($reason);
         $referral->save();
         sleep(1);
         $this->savedNota=true;

     }

    public function setEspecialist($specialist,$referral_id)
    {
        $this->savedEspecialist=false;
        $referral = $this->encounter->referrals()->whereId($referral_id)->first();
        $referral->referred_to_id = $specialist;
        $referral->save();
        sleep(1);
        $this->savedEspecialist=true;
    }

    public function render()
    {
        return view('livewire.consultation.referral');
    }
}
