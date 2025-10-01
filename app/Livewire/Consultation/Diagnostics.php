<?php

namespace App\Livewire\Consultation;

use App\Models\Condition;
use App\Models\Encounter;
use App\Models\EncounterDiagnosis;
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

    public $saving = false;

    public $saved = false;

    public $savedNote = [];

    public $notes = [];

    public $clinical_status = [];

    public $severity = [];

    public $savedSeverity = [];

    public function mount()
    {
        $this->encounter = Encounter::find($this->encounter_id);
        $this->loadSelectedLists();
    }

    private function loadSelectedLists()
    {
        $this->selectedLists = $this->encounter->diagnoses()->get();

        foreach ($this->selectedLists as $key) {
            $this->savedNote[$key->id] = false;
            $this->savedSeverity[$key->id] = false;
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
        $this->saved = false;
        $this->selectedOption = $option;
        $this->query = $option['name']; // Asigna el nombre seleccionado al input
        $this->results = []; // Limpia los resultados
        $condition = Condition::wherePatientId($this->encounter->patient_id)->whereCode($option)->first();
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

        $this->query = '';
        sleep(1);
        $this->saved = true;
        $this->loadSelectedLists();
    }

    public function delete($diagnostic_id)
    {
        $ed = EncounterDiagnosis::find($diagnostic_id);
        $ed->delete();
        $this->loadSelectedLists();
    }

    public function updateNote($id)
    {
        $this->savedNote[$id] = false;
        $encounterDiagnostic = EncounterDiagnosis::find($id);

        $encounterDiagnostic->update(['note' => $this->notes[$id]]);
        sleep(2);
        $this->savedNote[$id] = true;

    }

    public function updateClinicalStatus($id)
    {
        $this->savedNote[$id] = false;
        $encounterDiagnostic = EncounterDiagnosis::find($id);

        $encounterDiagnostic->update(['clinical_status' => $this->clinical_status[$id]]);
        sleep(2);
        $this->savedNote[$id] = true;

    }

    public function updateSeverity($id)
    {
        $this->savedSeverity[$id] = false;
        $encounterDiagnostic = EncounterDiagnosis::find($id);
        $encounterDiagnostic->condition->update(['severity' => $this->severity[$id]]);
        sleep(2);
        $this->savedSeverity[$id] = true;

    }

    public function render()
    {
        return view('livewire.consultation.diagnostics');
    }
}
