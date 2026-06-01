<?php

namespace App\Livewire\Consultation;

use App\Models\Encounter;
use App\Models\Medication;
use App\Models\MedicationRequest;
use App\Models\RapidAccess;
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

    protected $listeners = [
        'copyMedicationsToCurrentRecipe',
        'voice-dictation-medications' => 'updateFromVoice',
    ];

    public function mount()
    {
        $this->encounter = Encounter::find($this->encounter_id);

        $this->getMedicationRequestsProperty();
        $this->loadRapidAccess();
    }

    /**
     * Update medication requests from voice dictation
     */
    public function updateFromVoice($medications)
    {
        \Log::info('MedicationRequests: updateFromVoice called', [
            'encounter_id' => $this->encounter_id,
            'is_array' => is_array($medications),
            'medications_count' => is_array($medications) ? count($medications) : 0,
        ]);

        if (! is_array($medications)) {
            \Log::warning('Voice dictation: medications parameter is not an array', [
                'encounter_id' => $this->encounter_id,
                'medications_type' => gettype($medications),
                'medications_value' => $medications,
            ]);

            return;
        }

        $addedCount = 0;
        $notFoundCount = 0;
        $notFoundMedications = [];

        \Log::info('MedicationRequests: Processing medications', [
            'medications' => $medications,
        ]);

        foreach ($medications as $medData) {
            if (! isset($medData['medication_name']) || empty($medData['medication_name'])) {
                continue;
            }

            // Search for medication in database
            $medication = $this->findMedicationByName($medData['medication_name']);

            if ($medication) {
                // Check if medication already exists in this encounter
                $existingRequest = $this->encounter->medicationRequests()
                    ->where('medication_id2', $medication->id)
                    ->first();

                if (! $existingRequest) {
                    // Parse dosage to extract numeric quantity
                    $quantity = $this->parseQuantity($medData['dosage'] ?? null);

                    // Create new medication request
                    $medicationRequest = $this->encounter->medicationRequests()->create([
                        'fhir_id' => 'medicationrequest-'.Str::uuid(),
                        'identifier' => 'RX-'.strtoupper(Str::random(7)),
                        'status' => 'active',
                        'intent' => 'order',
                        'medication_id2' => $medication->id,
                        'quantity' => $quantity,
                        'frequency' => $this->parseFrequency($medData['frequency'] ?? null),
                        'duration' => $this->parseDuration($medData['duration'] ?? null),
                        'route' => $this->parseRoute($medData['route'] ?? 'oral'),
                        'dosage_text' => $medData['instructions'] ?? $medData['dosage'] ?? null,
                        'valid_from' => now(),
                        'valid_to' => now()->addDays(30),
                        'patient_id' => $this->encounter->patient_id,
                        'practitioner_id' => $this->encounter->practitioner_id,
                    ]);

                    // Update local arrays
                    $this->frecuencies[$medicationRequest->id] = $medicationRequest->frequency;
                    $this->routes[$medicationRequest->id] = $medicationRequest->route;
                    $this->durations[$medicationRequest->id] = $medicationRequest->duration;
                    $this->quantitys[$medicationRequest->id] = $medicationRequest->quantity;

                    // Generate dosage instruction
                    $this->generateDosageInstruction($medicationRequest->id);

                    $addedCount++;

                    \Log::info('Voice dictation: Medication added', [
                        'medication_name' => $medData['medication_name'],
                        'medication_id' => $medication->id,
                        'encounter_id' => $this->encounter_id,
                    ]);
                } else {
                    \Log::info('Voice dictation: Medication already exists in encounter', [
                        'medication_name' => $medData['medication_name'],
                        'encounter_id' => $this->encounter_id,
                    ]);
                }
            } else {
                $notFoundCount++;
                $notFoundMedications[] = $medData['medication_name'];

                \Log::warning('Voice dictation: Medication not found in database', [
                    'medication_name' => $medData['medication_name'],
                    'encounter_id' => $this->encounter_id,
                ]);
            }
        }

        // Refresh medication list
        $this->getMedicationRequestsProperty();

        // Show notification to user
        if ($addedCount > 0 && $notFoundCount > 0) {
            $this->dispatch('showToastrConsultation', [
                'type' => 'warning',
                'message' => __('consultation.medication_requests.medications_added_not_found', [
                    'added_count' => $addedCount,
                    'not_found_count' => $notFoundCount,
                    'medications' => implode(', ', $notFoundMedications),
                ]),
            ]);
        } elseif ($addedCount > 0) {
            $this->dispatch('showToastrConsultation', [
                'type' => 'success',
                'message' => __('consultation.medication_requests.medications_added_from_dictation', ['count' => $addedCount]),
            ]);
        } elseif ($notFoundCount > 0) {
            $this->dispatch('showToastrConsultation', [
                'type' => 'error',
                'message' => __('consultation.medication_requests.medications_not_found_in_database', ['medications' => implode(', ', $notFoundMedications)]),
            ]);
        }

        // Update finish button status
        $this->dispatch('findFinishedButtonStatus');
    }

    /**
     * Find medication in database by name (fuzzy matching)
     */
    private function findMedicationByName(string $name): ?Medication
    {
        $searchTerm = trim($name);

        // Try exact match first
        $medication = Medication::where('status', 'active')
            ->where(function ($q) use ($searchTerm) {
                $q->where('display', $searchTerm)
                    ->orWhere('generic_name', $searchTerm)
                    ->orWhere('home_name', $searchTerm);
            })
            ->first();

        if ($medication) {
            return $medication;
        }

        // Try fuzzy matching (LIKE search)
        $medication = Medication::where('status', 'active')
            ->where(function ($q) use ($searchTerm) {
                $q->where('display', 'like', '%'.$searchTerm.'%')
                    ->orWhere('generic_name', 'like', '%'.$searchTerm.'%')
                    ->orWhere('home_name', 'like', '%'.$searchTerm.'%');
            })
            ->orderByRaw('
                CASE
                    WHEN display = ? THEN 1
                    WHEN generic_name = ? THEN 2
                    WHEN home_name = ? THEN 3
                    WHEN display LIKE ? THEN 4
                    WHEN generic_name LIKE ? THEN 5
                    WHEN home_name LIKE ? THEN 6
                    ELSE 7
                END
            ', [$searchTerm, $searchTerm, $searchTerm, "{$searchTerm}%", "{$searchTerm}%", "{$searchTerm}%"])
            ->first();

        return $medication;
    }

    /**
     * Parse quantity from voice dictation to extract numeric value
     * Extracts the number of physical units (tablets, capsules, ml), NOT milligrams
     */
    private function parseQuantity(?string $quantity): ?int
    {
        if (empty($quantity)) {
            return null;
        }

        $normalized = strtolower(trim($quantity));

        // Check for unit keywords to extract correct quantity
        $unitKeywords = ['tableta', 'cápsula', 'capsula', 'comprimido', 'gragea', 'sobre', 'ampolla', 'dosis', 'aplicación', 'aplicacion'];

        foreach ($unitKeywords as $unit) {
            // Look for patterns like "1 tableta", "2 cápsulas", etc.
            if (preg_match('/(\d+(?:\.\d+)?)\s*'.$unit.'/', $normalized, $matches)) {
                return (int) $matches[1];
            }
        }

        // Check for ml/cc patterns
        if (preg_match('/(\d+(?:\.\d+)?)\s*(ml|cc)/', $normalized, $matches)) {
            return (int) $matches[1];
        }

        // If dosage contains "mg" or "miligramos", assume 1 unit
        // (e.g., "500 mg" → 1 tablet of 500mg)
        if (str_contains($normalized, 'mg') || str_contains($normalized, 'miligramo')) {
            return 1;
        }

        // Extract first numeric value as fallback
        if (preg_match('/(\d+(?:\.\d+)?)/', $quantity, $matches)) {
            $value = (int) $matches[1];

            // If value is very high (>100), it's probably mg, so return 1
            if ($value > 100) {
                return 1;
            }

            return $value;
        }

        return 1; // Default to 1 unit if nothing else works
    }

    /**
     * Parse frequency from voice dictation (in hours) to hours
     */
    private function parseFrequency(?string $frequency): ?string
    {
        if (empty($frequency)) {
            return null;
        }

        // Extract numeric value
        if (preg_match('/(\d+)/', $frequency, $matches)) {
            return $matches[1];
        }

        return $frequency;
    }

    /**
     * Parse duration from voice dictation to days
     */
    private function parseDuration(?string $duration): ?string
    {
        if (empty($duration)) {
            return null;
        }

        // Extract numeric value
        if (preg_match('/(\d+)/', $duration, $matches)) {
            return $matches[1];
        }

        return $duration;
    }

    /**
     * Parse route from voice dictation to standardized route
     */
    private function parseRoute(?string $route): string
    {
        if (empty($route)) {
            return 'oral';
        }

        $routeMap = [
            'oral' => 'oral',
            'bucal' => 'oral',
            'boca' => 'oral',
            'intravenoso' => 'intravenoso',
            'intravenosa' => 'intravenoso',
            'iv' => 'intravenoso',
            'intramuscular' => 'intramuscular',
            'im' => 'intramuscular',
            'subcutáneo' => 'subcutaneo',
            'subcutanea' => 'subcutaneo',
            'sc' => 'subcutaneo',
            'tópico' => 'topico',
            'topica' => 'topico',
            'piel' => 'topico',
            'rectal' => 'rectal',
            'vaginal' => 'vaginal',
            'oftálmico' => 'oftalmico',
            'oftalmica' => 'oftalmico',
            'ojo' => 'oftalmico',
            'ótico' => 'otico',
            'otica' => 'otico',
            'oído' => 'otico',
            'nasal' => 'nasal',
            'nariz' => 'nasal',
            'inhalación' => 'inhalacion',
            'inhalacion' => 'inhalacion',
        ];

        $normalized = strtolower(trim($route));

        return $routeMap[$normalized] ?? 'oral';
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
                $this->dispatch('error-'.$key, __('consultation.medication_requests.medication_already_added', ['name' => $option['name']]));
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
            session()->flash('message.success', __('consultation.medication_requests.medications_copied_successfully', ['count' => $copiedCount]));
        } else {
            session()->flash('message.info', __('consultation.medication_requests.selected_medications_already_in_current_prescription'));
        }

        // Disparar evento para actualizar el estado del botón de finalizar
        $this->dispatch('findFinishedButtonStatus');
    }

    private function loadRapidAccess()
    {
        $this->rapidAccess = RapidAccess::whereUserId(auth()->id())
            ->whereType('CLIENT')
            ->whereEncounterSectionId($this->section_id)
            ->with('medication')
            ->get()
            ->take(10);

        if ($this->rapidAccess->count() == 0) {
            $this->rapidAccess = RapidAccess::whereType('MASTER')
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
            $existing = RapidAccess::whereUserId(auth()->id())
                ->whereType('CLIENT')
                ->whereEncounterSectionId($this->section_id)
                ->where('medication_id', $medicationId)
                ->first();

            if (! $existing) {
                RapidAccess::create([
                    'user_id' => auth()->id(),
                    'type' => 'CLIENT',
                    'encounter_section_id' => $this->section_id,
                    'medication_id' => $medicationId,
                ]);

                $this->loadRapidAccess();

                $this->dispatch('showToastrConsultation',
                    type: 'success',
                    message: __('consultation.service_request.added_to_rapid_access')
                );
            } else {
                $this->dispatch('showToastrConsultation',
                    type: 'error',
                    message: __('consultation.service_request.already_in_rapid_access')
                );
            }
        } catch (\Exception $e) {
            $this->dispatch('showToastrConsultation',
                type: 'error',
                message: __('consultation.service_request.error_adding_to_rapid_access')
            );
        }
    }

    public function render()
    {
        return view('livewire.consultation.medication-requests');
    }
}
