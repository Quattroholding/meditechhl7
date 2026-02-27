<?php

namespace App\Livewire\Consultation;

use App\Models\Encounter;
use App\Models\Medication;
use App\Models\MedicationRequest;
use Illuminate\Support\Str;
use Livewire\Component;

class MedicationRequests extends Component
{
    public $query = '';

    public $results = [];

    public $encounter_id;

    public $encounter;

    public $selectedLists = [];

    public $rapidAccess = [];

    public $dosage_texts = [];

    public $quantitys = [];

    public $refills = [];

    public $frecuencies = [];

    public $durations = [];

    public $routes = [];

    public $section_id = 11;

    public $perPage = 50;

    public $totalResults = 0;

    public $hasMoreResults = false;

    public $isCodeSearch = false;

    protected $listeners = ['copyMedicationsToCurrentRecipe'];

    public function mount()
    {
        $this->encounter = Encounter::find($this->encounter_id);

        $this->getMedicationRequestsProperty();
        $this->loadRapidAccess();
    }

    public function getMedicationRequestsProperty()
    {
        $this->selectedLists = $this->encounter->medicationRequests()->orderBy('id', 'ASC')->get();

        foreach ($this->selectedLists as $sl) {
            $this->frecuencies[$sl->id] = $sl->frequency;
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
            $this->totalResults = 0;
            $this->hasMoreResults = false;
            $this->perPage = 50;

            return;
        }

        // Resetear paginación al cambiar query
        $this->perPage = 50;

        $this->searchMedications();
    }

    public function loadMore()
    {
        $this->perPage += 50;
        $this->searchMedications();
    }

    private function searchMedications()
    {
        $searchQuery = $this->query;

        // Detectar si es búsqueda por código (empieza con letra o número seguido de otro)
        $this->isCodeSearch = preg_match('/^[A-Z0-9][A-Z0-9]/i', $searchQuery);

        // Query medications table with ingredients
        $query = Medication::query()
            ->with('ingredients')
            ->where(function ($q) use ($searchQuery) {
                $q->where('code', 'like', '%'.$searchQuery.'%')
                    ->orWhere('home_name', 'like', '%'.$searchQuery.'%')
                    ->orWhere('generic_name', 'like', '%'.$searchQuery.'%')
                    ->orWhere('display', 'like', '%'.$searchQuery.'%');
            })
            ->where('status', 'active');

        // Contar total de resultados
        $this->totalResults = $query->count();

        // Búsqueda inteligente con ordenamiento por relevancia
        if ($this->isCodeSearch && strlen($searchQuery) > 0) {
            // Búsqueda por código: priorizar coincidencias en código
            $query->orderByRaw('
                CASE
                    WHEN code = ? THEN 1
                    WHEN code LIKE ? THEN 2
                    WHEN home_name LIKE ? THEN 3
                    ELSE 4
                END,
                code ASC
            ', [$searchQuery, "{$searchQuery}%", "{$searchQuery}%"]);
        } elseif (strlen($searchQuery) > 0) {
            // Búsqueda por nombre: priorizar coincidencias en nombres
            $query->orderByRaw('
                CASE
                    WHEN display = ? THEN 1
                    WHEN generic_name = ? THEN 2
                    WHEN home_name = ? THEN 3
                    WHEN display LIKE ? THEN 4
                    WHEN generic_name LIKE ? THEN 5
                    WHEN home_name LIKE ? THEN 6
                    ELSE 7
                END,
                display ASC
            ', [$searchQuery, $searchQuery, $searchQuery, "{$searchQuery}%", "{$searchQuery}%", "{$searchQuery}%"]);
        } else {
            // Sin búsqueda, ordenar por display
            $query->orderBy('display');
        }

        $this->results = $query->take($this->perPage)
            ->get()
            ->map(function ($med) {
                $ingredient = $med->ingredients->first();
                $strength = $ingredient ? $ingredient->strength_value.' '.$ingredient->strength_unit : '';

                return [
                    'id' => $med->id,
                    'name' => $med->display,
                    'code' => $med->code,
                    'generic_name' => $med->generic_name,
                ];
            })
            ->toArray();

        $this->hasMoreResults = $this->totalResults > $this->perPage;
    }

    public function selectOption($option)
    {
        $key = 'medication-search';

        try {
            // Delay para que el usuario vea el spinner "Guardando..."
            sleep(1);

            $this->selectedOption = $option;
            $this->query = $option['name']; // Asigna el nombre seleccionado al input
            $this->results = []; // Limpia los resultados
            $medicine_request = MedicationRequest::whereEncounterId($this->encounter->id)->whereMedicationId2($option['id'])->first();
            $medication = Medication::whereId($option['id'])->first();
            if (! $medicine_request) {
                $this->encounter->medicationRequests()->create([
                    'fhir_id' => 'medicationrequest-'.Str::uuid(),
                    'identifier' => 'RX-'.strtoupper(Str::random(7)),
                    'status' => 'active',
                    'intent' => 'order',
                    'medication_id2' => $medication->id,
                    'valid_from' => now(),
                    'valid_to' => now()->addDays(30),
                    'patient_id' => $this->encounter->patient_id,
                    'practitioner_id' => $this->encounter->practitioner_id,
                    'dosage_instruction',
                ]);

                $this->query = '';

                $this->dispatch('saved-'.$key);

            } else {
                $this->dispatch('error-'.$key, '¡Medicamento ('.$option['name'].') ya esta agregado a la  consulta.!');
            }

            $this->getMedicationRequestsProperty();

            // Disparar evento para limpiar el scroll bloqueado
            $this->dispatch('option-selected');

            // Disparar evento para actualizar el estado del botón de finalizar
            $this->dispatch('findFinishedButtonStatus');

        } catch (\Exception $e) {
            $this->dispatch('error-'.$key, 'Error al guardar: '.$e->getMessage());
        }
    }

    public function delete($id)
    {
        $this->encounter->medicationRequests()->whereId($id)->delete();
        $this->selectedLists = $this->encounter->referrals()->get();
        $this->getMedicationRequestsProperty();

        // Disparar evento para actualizar el estado del botón de finalizar
        $this->dispatch('findFinishedButtonStatus');
    }

    public function updatedQuantitys($value, $code)
    {
        $this->saveQuatity($code);
    }

    public function saveQuatity($id)
    {
        try {
            $medicationRequest = $this->encounter->medicationRequests()->whereId($id)->first();
            $medicationRequest->update(['quantity' => $this->quantitys[$id]]);

            $this->generateDosageInstruction($id);

            $this->dispatch('saved-quantity-'.$id);

            // Disparar evento para actualizar el estado del botón de finalizar
            $this->dispatch('findFinishedButtonStatus');

        } catch (\Exception $e) {
            $this->dispatch('error-'.$id, 'Error al guardar : '.$e->getMessage());
        }

    }

    public function updatedFrecuencies($value, $code)
    {
        $this->saveFrecuency($code);
    }

    public function saveFrecuency($id)
    {
        try {
            $medicationRequest = $this->encounter->medicationRequests()->whereId($id)->first();
            $medicationRequest->update(['frequency' => $this->frecuencies[$id]]);

            $this->generateDosageInstruction($id);

            $this->dispatch('saved-frecuency-'.$id);

            // Disparar evento para actualizar el estado del botón de finalizar
            $this->dispatch('findFinishedButtonStatus');

        } catch (\Exception $e) {
            $this->dispatch('error-'.$id, 'Error al guardar : '.$e->getMessage());
        }

    }

    public function updatedDurations($value, $code)
    {
        $this->saveDuration($code);
    }

    public function saveDuration($id)
    {
        try {
            $medicationRequest = $this->encounter->medicationRequests()->whereId($id)->first();
            $medicationRequest->update(['duration' => $this->durations[$id]]);
            $this->generateDosageInstruction($id);

            $this->dispatch('saved-duration-'.$id);

            // Disparar evento para actualizar el estado del botón de finalizar
            $this->dispatch('findFinishedButtonStatus');

        } catch (\Exception $e) {
            $this->dispatch('error-'.$id, 'Error al guardar : '.$e->getMessage());
        }

    }

    public function updatedRoutes($value, $code)
    {
        $this->saveRoute($code);
    }

    public function saveRoute($id)
    {
        try {
            $medicationRequest = $this->encounter->medicationRequests()->whereId($id)->first();
            $medicationRequest->update(['route' => $this->routes[$id]]);

            $this->generateDosageInstruction($id);

            $this->dispatch('saved-route-'.$id);

            // Disparar evento para actualizar el estado del botón de finalizar
            $this->dispatch('findFinishedButtonStatus');

        } catch (\Exception $e) {
            $this->dispatch('error-'.$id, 'Error al guardar : '.$e->getMessage());
        }

    }

    protected function generateDosageInstruction($id)
    {
        $frequency = '';
        $route = '';
        $duration = '';
        $quantity = '';
        $key = 'dosage_text_'.$id;

        try {
            if (isset($this->frecuencies[$id])) {
                $frequency = $this->frecuencies[$id];
            }
            if (isset($this->routes[$id])) {
                $route = $this->routes[$id];
            }
            if (isset($this->durations[$id])) {
                $duration = $this->durations[$id];
            }
            if (isset($this->quantitys[$id])) {
                $quantity = $this->quantitys[$id];
            }

            $requestMedicine = $this->encounter->medicationRequests()->whereId($id)->first();
            $medicine_type = '';

            // Try medication2 first (new medications table), then fall back to medicine (old medicines table)
            if ($requestMedicine->medication2) {
                $medicine_type = $requestMedicine->medication2->form;
            } elseif ($requestMedicine->medicine) {
                $medicine_type = $requestMedicine->medicine->type;
            }

            $dosage_instructions = [
                'text' => $quantity.' '.$medicine_type.
                    ' cada '.$frequency.' horas'.
                    ' via '.$route.
                    ' por '.$duration.' dias',
                'route' => $route,
                'frequency' => $frequency,
                'duration' => $duration,
            ];

            $requestMedicine->dosage_instruction = $dosage_instructions;
            $requestMedicine->dosage_text = $dosage_instructions['text'];
            $this->dosage_texts[$id] = $dosage_instructions['text'];
            $requestMedicine->save();

            $this->dispatch('saved-dosage_text_'.$key);

        } catch (\Exception $e) {
            $this->dispatch('error-'.$key, 'Error al guardar :'.$e->getMessage());
        }

    }

    public function medical_request_history()
    {
        $this->dispatch('showMedicationHistory', $this->encounter->patient_id);
    }

    public function copyMedicationsToCurrentRecipe($selectedMedications)
    {
        $copiedCount = 0;

        foreach ($selectedMedications as $medication) {
            // Verificar si el medicamento ya existe en la receta actual
            $existingMedication = $this->encounter->medicationRequests()
                ->when(! empty($medication['medication_id2']), function ($query) use ($medication) {
                    return $query->where('medication_id2', $medication['medication_id2']);
                })
                ->when(! empty($medication['medication_id']), function ($query) use ($medication) {
                    return $query->where('medication_id', $medication['medication_id']);
                })
                ->when(! empty($medication['medication']), function ($query) use ($medication) {
                    return $query->where('medication', $medication['medication']);
                })
                ->first();

            if (! $existingMedication) {
                // Crear nueva receta basada en la histórica
                $newMedicationRequest = $this->encounter->medicationRequests()->create([
                    'fhir_id' => 'medicationrequest-'.Str::uuid(),
                    'identifier' => 'RX-'.strtoupper(Str::random(7)),
                    'status' => 'active',
                    'intent' => 'order',
                    'medication_id' => $medication['medication_id'] ?? null,
                    'medication_id2' => $medication['medication_id2'] ?? null,
                    'medication' => $medication['medication'],
                    'quantity' => $medication['quantity'],
                    'frequency' => $medication['frequency'],
                    'duration' => $medication['duration'],
                    'route' => $medication['route'],
                    'refills' => $medication['refills'],
                    'dosage_text' => $medication['dosage_text'],
                    'dosage_instruction' => $medication['dosage_instruction'],
                    'valid_from' => now(),
                    'valid_to' => now()->addDays(30),
                    'patient_id' => $this->encounter->patient_id,
                    'practitioner_id' => $this->encounter->practitioner_id,
                ]);

                // Actualizar los arrays locales para mostrar la información
                $this->frecuencies[$newMedicationRequest->id] = $medication['frequency'];
                $this->routes[$newMedicationRequest->id] = $medication['route'];
                $this->durations[$newMedicationRequest->id] = $medication['duration'];
                $this->quantitys[$newMedicationRequest->id] = $medication['quantity'];
                $this->dosage_texts[$newMedicationRequest->id] = $medication['dosage_text'];

                $copiedCount++;
            }
        }

        // Actualizar la lista de medicamentos seleccionados
        $this->selectedLists = $this->encounter->medicationRequests()->get();

        if ($copiedCount > 0) {
            session()->flash('message.success', "{$copiedCount} medicamento(s) copiado(s) exitosamente.");
        } else {
            session()->flash('message.info', 'Los medicamentos seleccionados ya están en la receta actual.');
        }

        // Disparar evento para actualizar el estado del botón de finalizar
        $this->dispatch('findFinishedButtonStatus');
    }

    private function loadRapidAccess()
    {
        $this->rapidAccess = \App\Models\RapidAccess::whereUserId(auth()->id())
            ->whereType('CLIENT')
            ->whereEncounterSectionId($this->section_id)
            ->with('medication')
            ->get()
            ->take(10);

        if ($this->rapidAccess->count() == 0) {
            $this->rapidAccess = \App\Models\RapidAccess::whereType('MASTER')
                ->whereEncounterSectionId($this->section_id)
                ->with('medication')
                ->get()
                ->take(10);
        }
    }

    public function clearSearch()
    {
        $this->query = '';
        $this->results = [];
    }

    public function addToRapidAccess($medicationId)
    {
        try {
            $existing = \App\Models\RapidAccess::whereUserId(auth()->id())
                ->whereType('CLIENT')
                ->whereEncounterSectionId($this->section_id)
                ->where('medication_id', $medicationId)
                ->first();

            if (! $existing) {
                \App\Models\RapidAccess::create([
                    'user_id' => auth()->id(),
                    'type' => 'CLIENT',
                    'encounter_section_id' => $this->section_id,
                    'medication_id' => $medicationId,
                ]);

                $this->loadRapidAccess();

                $this->dispatch('showToastrConsultation',
                    type: 'success',
                    message: 'Agregado a accesos rápidos'
                );
            } else {
                $this->dispatch('showToastrConsultation',
                    type: 'error',
                    message: 'Ya está en accesos rápidos'
                );
            }
        } catch (\Exception $e) {
            $this->dispatch('showToastrConsultation',
                type: 'error',
                message: 'Error al agregar a accesos rápidos'
            );
        }
    }

    public function render()
    {
        return view('livewire.consultation.medication-requests');
    }
}
