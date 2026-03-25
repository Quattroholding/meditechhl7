<?php

namespace App\Livewire\Consultation;

use App\Models\Condition;
use App\Models\Encounter;
use App\Models\EncounterDiagnosis;
use App\Models\Icd10Code;
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

    public $perPage = 50;

    public $totalResults = 0;

    public $hasMoreResults = false;

    public $groupedResults = [];

    public $isCodeSearch = false;

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
            $this->totalResults = 0;
            $this->hasMoreResults = false;
            $this->perPage = 50;

            return;
        }

        // Resetear paginación al cambiar query
        $this->perPage = 50;

        $this->searchDiagnostics();
    }

    public function loadMore()
    {
        $this->perPage += 50;
        $this->searchDiagnostics();
    }

    private function searchDiagnostics()
    {
        $query = $this->query;

        // Detectar si es búsqueda por código (empieza con letra y contiene números)
        $this->isCodeSearch = preg_match('/^[A-Z][0-9]/i', $query);

        // Contar total de resultados
        if ($this->isCodeSearch) {
            // Búsqueda prioritaria por código
            $this->totalResults = Icd10Code::whereRaw('code LIKE ?', ["%{$query}%"])->count();
        } else {
            // Búsqueda por descripción
            $this->totalResults = Icd10Code::whereRaw('(code LIKE ? or description LIKE ? or description_es LIKE ?)', [
                "%{$query}%",
                "%{$query}%",
                "%{$query}%",
            ])->count();
        }

        // Búsqueda inteligente con ordenamiento por relevancia
        $queryBuilder = Icd10Code::selectRaw("id, code, concat(code,'|',description_es) as name, description_es, description");

        if ($this->isCodeSearch) {
            // Búsqueda por código: priorizar coincidencias en código
            $queryBuilder->whereRaw('code LIKE ?', ["%{$query}%"])
                ->orderByRaw('
                    CASE
                        WHEN code = ? THEN 1
                        WHEN code LIKE ? THEN 2
                        ELSE 3
                    END,
                    code ASC
                ', [$query, "{$query}%"]);
        } else {
            // Búsqueda por descripción: priorizar coincidencias en descripción
            $queryBuilder->whereRaw('(code LIKE ? or description LIKE ? or description_es LIKE ?)', [
                "%{$query}%",
                "%{$query}%",
                "%{$query}%",
            ])
                ->orderByRaw('
                    CASE
                        WHEN code = ? THEN 1
                        WHEN description_es = ? THEN 2
                        WHEN description_es LIKE ? THEN 3
                        WHEN code LIKE ? THEN 4
                        ELSE 5
                    END,
                    code ASC
                ', [$query, $query, "{$query}%", "{$query}%"]);
        }

        $results = $queryBuilder->take($this->perPage)->get()->toArray();

        $this->results = $results;

        // Agrupar resultados por categoría (primer carácter del código)
        $this->groupedResults = $this->groupByCategory($results);

        $this->hasMoreResults = $this->totalResults > $this->perPage;
    }

    private function groupByCategory($results)
    {
        $grouped = [];

        foreach ($results as $result) {
            $code = $result['code'];
            $category = $this->getCategoryName($code);

            if (! isset($grouped[$category])) {
                $grouped[$category] = [
                    'name' => $category,
                    'items' => [],
                ];
            }

            $grouped[$category]['items'][] = $result;
        }

        return $grouped;
    }

    private function getCategoryName($code)
    {
        // Obtener categoría basada en el primer carácter del código ICD-10
        $firstChar = strtoupper(substr($code, 0, 1));

        $categories = [
            'A' => 'Enfermedades Infecciosas (A00-B99)',
            'B' => 'Enfermedades Infecciosas (A00-B99)',
            'C' => 'Neoplasias (C00-D49)',
            'D' => 'Neoplasias / Sangre (C00-D89)',
            'E' => 'Enfermedades Endocrinas (E00-E89)',
            'F' => 'Trastornos Mentales (F01-F99)',
            'G' => 'Sistema Nervioso (G00-G99)',
            'H' => 'Ojos y Oídos (H00-H95)',
            'I' => 'Sistema Circulatorio (I00-I99)',
            'J' => 'Sistema Respiratorio (J00-J99)',
            'K' => 'Sistema Digestivo (K00-K95)',
            'L' => 'Piel (L00-L99)',
            'M' => 'Sistema Musculoesquelético (M00-M99)',
            'N' => 'Sistema Genitourinario (N00-N99)',
            'O' => 'Embarazo y Parto (O00-O9A)',
            'P' => 'Afecciones Perinatales (P00-P96)',
            'Q' => 'Malformaciones Congénitas (Q00-Q99)',
            'R' => 'Síntomas y Signos (R00-R99)',
            'S' => 'Traumatismos (S00-T88)',
            'T' => 'Traumatismos / Envenenamientos (S00-T88)',
            'V' => 'Causas Externas (V00-Y99)',
            'W' => 'Causas Externas (V00-Y99)',
            'X' => 'Causas Externas (V00-Y99)',
            'Y' => 'Causas Externas (V00-Y99)',
            'Z' => 'Factores de Salud (Z00-Z99)',
        ];

        return $categories[$firstChar] ?? 'Otros';
    }

    public function selectOption($option)
    {
        $key = 'diagnostic-search';

        try {
            sleep(1);

            // Verificar si ya existe ANTES de crear
            $encounter_diagnosis = $this->encounter->diagnoses()->whereHas('condition', function ($q) use ($option) {
                $q->where('conditions.code', $option['code']);
            })->first();

            if (! $encounter_diagnosis) {
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

            } else {
                $this->dispatch(
                    'error-'.$key,
                    '¡Diagnostico ('.$option['name'].') ya está agregado a la consulta!'
                );
            }

        } catch (\Exception $e) {
            $this->dispatch('error-'.$key, 'Error al giardar :'.$e->getMessage());
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
