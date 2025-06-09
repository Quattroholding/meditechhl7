<?php

namespace App\Livewire\Consultation;

use App\Models\CptCode;
use App\Models\Encounter;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Livewire\Component;

class ServiceRequest extends Component
{

    public $query = '';
    public $results = [];
    public $encounter_id;
    public $encounter;
    public $type='procedure';
    public $selectedLists=[];
    public $rapidAccess=[];
    public $section_id;
    public $saved = false;

    public function mount(){
        $this->encounter = Encounter::find($this->encounter_id);

        if($this->section_id==6) $this->type='laboratory';
        if($this->section_id==7) $this->type='images';
        if($this->section_id==8) $this->type='procedure';
        $this->loadSelectedLists();
        $this->loadRapidAccess();
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
        $this->saved=false;
        $this->selectedOption = $option;
        $this->query = $option['name']; // Asigna el nombre seleccionado al input
        $this->results = []; // Limpia los resultados
        $cpt = CptCode::whereId($option)->first();
        $service_request =  $this->encounter->serviceRequests()->whereCode($cpt->code)->first();

        if(!$service_request){

            $this->encounter->serviceRequests()->create([
                'fhir_id' => 'servicerequest-' . Str::uuid(),
                'patient_id' => $this->encounter->patient_id,
                'practitioner_id' => $this->encounter->practitioner_id,
                'status' => 'draft',
                'intent' => 'order',
                'priority' => 'asap',
                'do_not_perform' => 0,
                'code' =>$cpt->code,
                'service_type'=>$cpt->type,
                'code_system' => 'https://www.ama-assn.org/practice-management/cpt',
                'quantity' => 1,
                'occurrence_start' => now(),
                'authored_on' =>now(),
                'last_updated' =>now(),
            ]);
        }

        $this->query='';
        sleep(1);
        $this->saved=true;

        $this->dispatch('option-selected');
        $this->loadSelectedLists();
    }

    private function loadSelectedLists()
    {
        $this->selectedLists = $this->encounter->serviceRequests()
            ->where('service_type', $this->type)
            ->get();
    }

    private function loadRapidAccess()
    {
        $this->rapidAccess = \App\Models\RapidAccess::whereUserId(auth()->id())
            ->whereType('CLIENT')
            ->whereEncounterSectionId($this->section_id)
            ->get();

        if ($this->rapidAccess->count() == 0) {
            $this->rapidAccess = \App\Models\RapidAccess::whereType('MASTER')
                ->whereEncounterSectionId($this->section_id)
                ->get();
        }
    }

    public function delete($id){
        $this->encounter->serviceRequests()->whereId($id)->delete();
        $this->loadRapidAccess();
    }

    public function clearSearch()
    {
        $this->query = '';
        $this->results = [];
        $this->dispatch('option-selected');
    }

    public function addToRapidAccess($cptId)
    {

        try {
            $existing = \App\Models\RapidAccess::whereUserId(auth()->id())
                ->whereType('CLIENT')
                ->whereEncounterSectionId($this->section_id)
                ->where('cpt_id', $cptId)
                ->first();

            if (!$existing) {
                \App\Models\RapidAccess::create([
                    'user_id' => auth()->id(),
                    'type' => 'CLIENT',
                    'encounter_section_id' => $this->section_id,
                    'cpt_id' => $cptId
                ]);

                $this->loadRapidAccess();

                $this->dispatch('showToastr',
                    type: 'success',
                    message: 'Agregado a accesos rápidos'
                );

            } else {
                $this->dispatch('showToastr',
                    type: 'error',
                    message: 'Ya está en accesos rápidos'
                );
            }
        } catch (\Exception $e) {
            $this->dispatch('showToastr',
                type: 'error',
                message: 'Error al agregar a accesos rápidos'
            );
        }
    }
    public function render()
    {
        return view('livewire.consultation.service-request');
    }
}
