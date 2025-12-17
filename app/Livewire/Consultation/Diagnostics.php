<?php

namespace App\Livewire\Consultation;

use App\Models\Condition;
use App\Models\Encounter;
use App\Models\EncounterDiagnosis;
use App\Models\Icd10Code;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Livewire\Component;

class Diagnostics extends Component
{

    public $query = '';

    public $results = [];

    public $encounter_id;

    public $encounter;

    public $selectedLists = [];

    public $notes = [];

    public $clinical_status = [];

    public $severity = [];

    public function mount()
    {
        $this->encounter = Encounter::find($this->encounter_id);
        $this->loadSelectedLists();
    }

    private function loadSelectedLists()
    {
        $this->selectedLists = $this->encounter->diagnoses()->get();

        foreach ($this->selectedLists as $key) {
            $this->notes[$key->id] = $key->note;
            $this->clinical_status[$key->id] = $key->condition->clinical_status;
            $this->severity[$key->id] = $key->condition->severity;
        }

        $this->dispatch('findFinishedButtonStatus');
    }

    public function updatedQuery()
    {
        if (strlen($this->query) < 2) {
            $this->results = [];

            return;
        }

        $response = Http::get(url('api/diagnostics'), [
            'dropdown' => true,
            'q' => $this->query,
        ]);

        $this->results = $response->json() ?? [];
    }

    public function selectOption($option)
    {
        $key = 'diagnostic-search';

        try {
            sleep(1);

            // Verificar si ya existe ANTES de crear
            $encounter_diagnosis = $this->encounter->diagnoses()->whereHas('condition',function ($q) use($option){
                $q->where('conditions.code', $option['code']);
            }) ->first();

            if(!$encounter_diagnosis){
                $this->selectedOption = $option;
                $this->query = $option['name']; // Asigna el nombre seleccionado al input
                $this->results = []; // Limpia los resultados
                $condition = Condition::wherePatientId($this->encounter->patient_id)->whereCode($option)->first();
                $onset_info = '';
                $diagnostic = Icd10Code::whereCode($option['code'])->first();
                if ($diagnostic) {
                    $onset_info = $diagnostic->description_es;
                }
                if (! $condition) {
                    $condition = Condition::create([
                        'fhir_id' => 'condition-'.Str::uuid(),
                        'patient_id' => $this->encounter->patient_id,
                        'practitioner_id' => $this->encounter->practitioner_id,
                        'identifier' => 'DX-'.strtoupper(Str::random(7)),
                        'clinical_status' => 'active',
                        'verification_status' => 'confirmed',
                        'code' => $option['code'],
                        'severity' => 'severe',
                        'onset_info' => strtoupper($onset_info),
                        'onset_date' => now()->format('Y-m-d H:i'),
                        'recorded_date' => now()->format('Y-m-d H:i'),
                    ]);
                }

                $ed = $this->encounter->diagnoses()->create([
                    'encounter_id' => $this->encounter->id,
                    'condition_id' => $condition->id,
                    'rank' => 1,
                    'use' => 'principal',
                ]);

                $this->dispatch('saved-'.$key);

                $this->query = '';

                $this->loadSelectedLists();

            }else{
                $this->dispatch(
                    'error-'.$key,
                    '¡Diagnostico ('. $option['name'].') ya está agregado a la consulta!'
                );
            }

        } catch (\Exception $e) {
            $this->dispatch('error-'.$key,'Error al giardar :'.$e->getMessage());
        }
    }

    public function delete($diagnostic_id)
    {
        $ed = EncounterDiagnosis::find($diagnostic_id);
        $ed->delete();
        $this->loadSelectedLists();
    }

    public function updatedNotes($value, $key)
    {
        $this->updateNote($key);
    }

    public function updateNote($id)
    {
        $key = "note_{$id}";

        try {
            // Delay para que el usuario vea el spinner "Guardando..."
            sleep(1);

            $encounterDiagnostic = EncounterDiagnosis::find($id);
            $encounterDiagnostic->update(['note' => $this->notes[$id]]);

            $this->dispatch('saved-'.$key);

        } catch (\Exception $e) {

            session()->flash('error', 'Error al guardar: '.$e->getMessage());
        }
    }

    public function updateClinicalStatus($id)
    {
        $key = "clinical_status_{$id}";

        $this->startSaving($key);

        try {
            $encounterDiagnostic = EncounterDiagnosis::find($id);

            $encounterDiagnostic->update(['clinical_status' => $this->clinical_status[$id]]);

            // Delay para que el usuario vea el spinner "Guardando..."
            sleep(1);

            $this->finishSaving($key);
        } catch (\Exception $e) {
            $this->resetSaveState($key);
            session()->flash('error', 'Error al guardar: '.$e->getMessage());
        }
    }

    public function updatedSeverity($value, $key)
    {
        $this->updateSeverity($key);
    }

    public function updateSeverity($id)
    {
        $key = "severity_{$id}";
        try {

            sleep(1);

            $encounterDiagnostic = EncounterDiagnosis::find($id);
            $encounterDiagnostic->condition->update(['severity' => $this->severity[$id]]);

            // Delay para que el usuario vea el spinner "Guardando..."

            $this->dispatch('saved-'.$id);

        } catch (\Exception $e) {

            session()->flash('error', 'Error al guardar: '.$e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.consultation.diagnostics');
    }
}
