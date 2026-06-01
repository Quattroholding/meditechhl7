<?php

namespace App\Livewire\Consultation;

use App\Models\CptCode;
use App\Models\Encounter;
use App\Models\RapidAccess;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use Livewire\Component;

class ServiceRequest extends Component
{
    public $query = '';

    public $results = [];

    public $encounter_id;

    public $encounter;

    public $type = 'procedure';

    public $selectedLists = [];

    public $rapidAccess = [];

    public $section_id;

    public $notes = [];

    public $performedInConsultation = [];

    public $procedureNotes = [];

    public $perPage = 50;

    public $totalResults = 0;

    public $hasMoreResults = false;

    public $isCodeSearch = false;

    public function mount()
    {
        $this->encounter = Encounter::find($this->encounter_id);

        if ($this->section_id == 6) {
            $this->type = 'laboratory';
        }
        if ($this->section_id == 7) {
            $this->type = 'images';
        }
        if ($this->section_id == 8) {
            $this->type = 'procedure';
        }
        $this->loadSelectedLists();
        $this->loadRapidAccess();
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

        $this->searchServiceRequests();
    }

    public function loadMore()
    {
        $this->perPage += 50;
        $this->searchServiceRequests();
    }

    private function searchServiceRequests()
    {
        $http = app()->environment('local') ? Http::withoutVerifying() : Http::asForm();
        $response = $http->get(url('api/cpts/'.$this->type), [
            'dropdown' => true,
            'q' => $this->query,
            'perPage' => $this->perPage,
        ]);

        if ($response->successful()) {
            $data = $response->json();
            $this->results = $data['data'] ?? [];
            $this->totalResults = $data['total'] ?? 0;
            $this->hasMoreResults = $data['hasMore'] ?? false;
            $this->isCodeSearch = $data['isCodeSearch'] ?? false;
        } else {
            $this->results = [];
            $this->totalResults = 0;
            $this->hasMoreResults = false;
        }
    }

    #[On('selectOption')]
    public function selectOption($option)
    {
        $key = 'service-'.$this->type.'-search';
        try {
            // Delay para que el usuario vea el spinner "Guardando..."
            sleep(1);

            $this->selectedOption = $option;

            // Manejar diferentes formatos de entrada
            if (is_array($option)) {
                $cptId = $option['id'] ?? $option;
                $this->query = $option['name'] ?? ''; // Asigna el nombre seleccionado al input
            } else {
                // Si es un número directo
                $cptId = $option;
                $this->query = '';
            }

            $this->results = []; // Limpia los resultados
            $cpt = CptCode::find($cptId);

            if (! $cpt) {
                $this->resetSaveState($key);
                $this->dispatch('showToastrConsultation',
                    type: 'error',
                    message: __('consultation.service_request.cpt_code_not_found')
                );

                return;
            }

            // Verificar si ya existe ANTES de crear
            $service_request = $this->encounter->serviceRequests()
                ->where('code', $cpt->code)
                ->where('service_type', $this->type)
                ->first();

            if (! $service_request) {
                $this->encounter->serviceRequests()->create([
                    'fhir_id' => 'servicerequest-'.Str::uuid(),
                    'patient_id' => $this->encounter->patient_id,
                    'practitioner_id' => $this->encounter->practitioner_id,
                    'status' => 'draft',
                    'intent' => 'order',
                    'priority' => 'asap',
                    'do_not_perform' => 0,
                    'code' => $cpt->code,
                    'service_type' => $cpt->type,
                    'code_system' => 'https://www.ama-assn.org/practice-management/cpt',
                    'quantity' => 1,
                    'occurrence_start' => now(),
                    'authored_on' => now(),
                    'last_updated' => now(),
                ]);

                $this->query = '';

                $this->dispatch('saved-'.$key);
                /*
                $this->dispatch('showToastrConsultation',
                    type: 'success',
                    message: 'Servicio agregado exitosamente'
                );*/
            } else {
                $this->dispatch('error-'.$key, __('consultation.service_request.cpt_already_added', ['code' => $cpt->code]));
                /*$this->dispatch('showToastrConsultation',
                    type: 'error',
                    message: '¡Servicio ('.$cpt->type.') ya está agregado a la consulta!'
                );*/
            }

            $this->query = '';

            $this->loadSelectedLists();
        } catch (\Exception $e) {
            $this->dispatch('error-'.$key, 'Error al guardar: '.$e->getMessage());
            /*
            $this->dispatch('showToastrConsultation',
                type: 'error',
                message: 'Error al guardar: '.$e->getMessage()
            );
            */
        }
    }

    public function getListeners()
    {
        // Escuchar evento único para este encuentro y sección
        return [
            'rapid-access-item-selected-'.$this->encounter_id.'-'.$this->section_id => 'handleRapidAccessSelection',
            'voice-dictation-service-requests' => 'handleVoiceDictationServiceRequests',
        ];
    }

    public function handleRapidAccessSelection($cptId)
    {
        // NO llamar a selectOption para evitar evento duplicado
        // Procesar directamente aquí
        $cpt = CptCode::find($cptId);

        if (! $cpt) {
            $this->dispatch('showToastrConsultation',
                type: 'error',
                message: __('consultation.service_request.cpt_code_not_found')
            );

            return;
        }

        // Verificar si ya existe
        $service_request = $this->encounter->serviceRequests()
            ->where('code', $cpt->code)
            ->where('service_type', $this->type)
            ->first();

        if (! $service_request) {
            $this->encounter->serviceRequests()->create([
                'fhir_id' => 'servicerequest-'.Str::uuid(),
                'patient_id' => $this->encounter->patient_id,
                'practitioner_id' => $this->encounter->practitioner_id,
                'status' => 'draft',
                'intent' => 'order',
                'priority' => 'asap',
                'do_not_perform' => 0,
                'code' => $cpt->code,
                'service_type' => $cpt->type,
                'code_system' => 'https://www.ama-assn.org/practice-management/cpt',
                'quantity' => 1,
                'occurrence_start' => now(),
                'authored_on' => now(),
                'last_updated' => now(),
            ]);

            $this->dispatch('showToastrConsultation',
                type: 'success',
                message: __('consultation.service_request.service_added_successfully')
            );

        } else {
            $this->dispatch('showToastrConsultation',
                type: 'error',
                message: __('consultation.service_request.service_already_added')
            );
        }

        $this->loadSelectedLists();
    }

    /**
     * Handle service requests from voice dictation
     */
    public function handleVoiceDictationServiceRequests($serviceRequests)
    {
        if (empty($serviceRequests)) {
            return;
        }

        \Log::info('ServiceRequest: Received voice dictation service requests', [
            'encounter_id' => $this->encounter_id,
            'section_id' => $this->section_id,
            'type' => $this->type,
            'count' => count($serviceRequests),
            'requests' => $serviceRequests,
        ]);

        $addedCount = 0;
        $skippedCount = 0;
        $notFoundDescriptions = [];

        foreach ($serviceRequests as $request) {
            // Skip if not the correct service type for this component
            if (! isset($request['service_type']) || $request['service_type'] !== $this->type) {
                continue;
            }

            $description = $request['description'] ?? '';
            $priority = $request['priority'] ?? 'routine';
            $note = $request['note'] ?? null;

            if (empty($description)) {
                continue;
            }

            // Search for matching CPT code in database
            $cpt = $this->findBestMatchingCpt($description);

            if (! $cpt) {
                \Log::warning('ServiceRequest: No CPT code found for description', [
                    'description' => $description,
                    'type' => $this->type,
                ]);
                $notFoundDescriptions[] = $description;

                continue;
            }

            // Check if service request already exists
            $existingRequest = $this->encounter->serviceRequests()
                ->where('code', $cpt->code)
                ->where('service_type', $this->type)
                ->first();

            if ($existingRequest) {
                $skippedCount++;

                continue;
            }

            // Create new service request
            $this->encounter->serviceRequests()->create([
                'fhir_id' => 'servicerequest-'.Str::uuid(),
                'patient_id' => $this->encounter->patient_id,
                'practitioner_id' => $this->encounter->practitioner_id,
                'status' => 'draft',
                'intent' => 'order',
                'priority' => $priority,
                'do_not_perform' => 0,
                'code' => $cpt->code,
                'service_type' => $cpt->type,
                'code_system' => 'https://www.ama-assn.org/practice-management/cpt',
                'code_display' => $cpt->description_es,
                'quantity' => 1,
                'note' => $note,
                'occurrence_start' => now(),
                'authored_on' => now(),
                'last_updated' => now(),
            ]);

            $addedCount++;

            \Log::info('ServiceRequest: Added from voice dictation', [
                'cpt_code' => $cpt->code,
                'description' => $cpt->description_es,
                'priority' => $priority,
            ]);
        }

        // Reload the list
        $this->loadSelectedLists();

        // Show notification
        if ($addedCount > 0 || $skippedCount > 0) {
            $message = '';
            if ($addedCount > 0) {
                $message .= __('consultation.service_request.services_added_from_dictation', ['count' => $addedCount]).' ';
            }
            if ($skippedCount > 0) {
                $message .= __('consultation.service_request.already_existed', ['count' => $skippedCount]).' ';
            }
            if (! empty($notFoundDescriptions)) {
                $message .= __('consultation.service_request.cpt_codes_not_found_for').' '.implode(', ', $notFoundDescriptions);
            }

            $this->dispatch('showToastrConsultation',
                type: $addedCount > 0 ? 'success' : 'info',
                message: trim($message)
            );
        }

        \Log::info('ServiceRequest: Voice dictation processing completed', [
            'added' => $addedCount,
            'skipped' => $skippedCount,
            'not_found' => count($notFoundDescriptions),
        ]);
    }

    /**
     * Get medical term synonyms for better matching
     */
    private function getMedicalSynonyms(): array
    {
        return [
            // Laboratory tests
            'hemograma' => ['conteo de glóbulos', 'cbc', 'blood count', 'recuento completo'],
            'glucosa' => ['glucose', 'azúcar en sangre', 'glicemia'],
            'proteína c reactiva' => ['c-reactiva', 'proteina c-reactiva', 'proteina c reactiva', 'alta sensibilidad'],
            'perfil lipídico' => ['colesterol', 'lipid panel', 'lípidos'],
            'hemoglobina' => ['hgb', 'hb', 'hemoglobin'],
            'creatinina' => ['creatinine'],
            'urea' => ['bun', 'nitrógeno ureico'],

            // Imaging studies
            'radiografía' => ['examen radiológico', 'rx', 'rayos x', 'radiographic'],
            'tórax' => ['torax', 'chest', 'pecho'],
            'ecografía' => ['ultrasonido', 'ultrasound', 'ecografia'],
            'tomografía' => ['tac', 'ct', 'tomografia', 'computed tomography'],
            'resonancia' => ['rm', 'mri', 'magnetic resonance'],

            // Procedures
            'electrocardiograma' => ['ecg', 'ekg', 'electrocardiogram'],
            'espirometría' => ['espirometria', 'spirometry', 'prueba de función pulmonar'],
        ];
    }

    /**
     * Get all search terms including synonyms
     */
    private function getSearchTerms(string $description): array
    {
        $terms = [strtolower($description)];
        $synonyms = $this->getMedicalSynonyms();
        $descLower = strtolower($description);

        foreach ($synonyms as $term => $alternatives) {
            if (str_contains($descLower, $term)) {
                $terms = array_merge($terms, $alternatives);
            }
        }

        return array_unique($terms);
    }

    /**
     * Find the best matching CPT code for a given description
     */
    private function findBestMatchingCpt(string $description): ?CptCode
    {
        \Log::info('ServiceRequest: Searching for CPT code', [
            'description' => $description,
            'type' => $this->type,
        ]);

        // Get search terms including synonyms
        $searchTerms = $this->getSearchTerms($description);

        \Log::info('ServiceRequest: Search terms with synonyms', [
            'search_terms' => $searchTerms,
        ]);

        // Try exact match first (case insensitive)
        foreach ($searchTerms as $term) {
            $cpt = CptCode::whereType($this->type)
                ->whereRaw('LOWER(description_es) = ?', [$term])
                ->first();

            if ($cpt) {
                \Log::info('ServiceRequest: Found exact match', [
                    'cpt_code' => $cpt->code,
                    'description' => $cpt->description_es,
                    'matched_term' => $term,
                ]);

                return $cpt;
            }
        }

        // Try partial match with LIKE for each term
        foreach ($searchTerms as $term) {
            $cpt = CptCode::whereType($this->type)
                ->where(function ($query) use ($term) {
                    $query->whereRaw('LOWER(description_es) LIKE ?', ['%'.$term.'%'])
                        ->orWhereRaw('LOWER(description) LIKE ?', ['%'.$term.'%']);
                })
                ->orderByRaw('
                    CASE
                        WHEN LOWER(description_es) LIKE ? THEN 1
                        WHEN LOWER(description) LIKE ? THEN 2
                        ELSE 3
                    END
                ', [$term.'%', $term.'%'])
                ->first();

            if ($cpt) {
                \Log::info('ServiceRequest: Found partial match', [
                    'cpt_code' => $cpt->code,
                    'description' => $cpt->description_es,
                    'search_term' => $description,
                    'matched_term' => $term,
                ]);

                return $cpt;
            }
        }

        \Log::warning('ServiceRequest: No CPT code found', [
            'description' => $description,
            'type' => $this->type,
            'search_terms_tried' => $searchTerms,
        ]);

        return null;
    }

    public function updatedNotes($value, $code)
    {
        $this->updateNote($code);
    }

    public function updatedProcedureNotes($value, $id)
    {
        $this->updateProcedureNotes($id);
    }

    public function updateNote($id)
    {
        $key = "note_{$id}";

        try {

            // Delay para que el usuario vea el spinner "Guardando..."
            sleep(1);

            $serviceRequest = \App\Models\ServiceRequest::find($id);
            $serviceRequest->update(['note' => $this->notes[$id]]);

            $this->dispatch('saved-'.$id);

        } catch (\Exception $e) {
            $this->dispatch('error-'.$id, $e->getMessage());
        }
    }

    public function togglePerformedInConsultation($id)
    {
        try {
            $serviceRequest = \App\Models\ServiceRequest::find($id);

            if (! $serviceRequest) {
                throw new \Exception(__('consultation.service_request.service_request_not_found'));
            }

            // Leer el valor actual de la base de datos, no de la memoria
            $currentValue = (bool) $serviceRequest->performed_in_consultation;
            $newValue = ! $currentValue;

            \Log::info('Toggle performed in consultation', [
                'id' => $id,
                'current_value' => $currentValue,
                'new_value' => $newValue,
            ]);

            $serviceRequest->update(['performed_in_consultation' => $newValue]);
            $this->performedInConsultation[$id] = $newValue;

            // Si se desmarca, limpiar las notas del procedimiento
            if (! $newValue) {
                $serviceRequest->update(['procedure_notes' => null]);
                $this->procedureNotes[$id] = null;
            }

            $this->dispatch('showToastrConsultation',
                type: 'success',
                message: $newValue ? __('consultation.service_request.procedure_marked_as_performed') : __('consultation.service_request.procedure_unmarked')
            );

        } catch (\Exception $e) {
            \Log::error('Error toggle performed in consultation', [
                'id' => $id,
                'error' => $e->getMessage(),
            ]);

            $this->dispatch('showToastrConsultation',
                type: 'error',
                message: __('consultation.service_request.error_updating').' '.$e->getMessage()
            );
        }
    }

    public function updateProcedureNotes($id)
    {
        try {
            sleep(1);

            $serviceRequest = \App\Models\ServiceRequest::find($id);

            if (! $serviceRequest) {
                throw new \Exception(__('consultation.service_request.service_request_not_found'));
            }

            $notes = $this->procedureNotes[$id] ?? null;

            \Log::info('Updating procedure notes', [
                'id' => $id,
                'notes' => $notes,
            ]);

            $serviceRequest->update(['procedure_notes' => $notes]);

            $this->dispatch('saved-procedure-notes-'.$id);

        } catch (\Exception $e) {
            \Log::error('Error updating procedure notes', [
                'id' => $id,
                'error' => $e->getMessage(),
            ]);

            $this->dispatch('error-procedure-notes-'.$id, $e->getMessage());
        }
    }

    private function loadSelectedLists()
    {
        $this->selectedLists = $this->encounter->serviceRequests()
            ->with(['cpt', 'observations'])
            ->where('service_type', $this->type)
            ->get();

        foreach ($this->selectedLists as $key) {
            $this->notes[$key->id] = $key->note;
            $this->performedInConsultation[$key->id] = (bool) $key->performed_in_consultation;
            $this->procedureNotes[$key->id] = $key->procedure_notes;
        }
    }

    public function checkForNewResults()
    {
        // Método público para polling - refresca los service requests
        $this->loadSelectedLists();
    }

    public function hasPendingLabResults()
    {
        // Verifica si hay algún service request de CBC sin resultados
        return $this->selectedLists->contains(function ($sr) {
            return in_array($sr->code, ['85025', '85027'])
                && $sr->status !== 'completed'
                && $sr->observations()->count() === 0;
        });
    }

    #[On('lab-results-received')]
    public function handleLabResultsReceived($data)
    {
        // Cuando llegan nuevos resultados, recargar la lista
        $this->loadSelectedLists();

        // Opcional: mostrar notificación al usuario
        $this->dispatch('show-toast', [
            'message' => __('consultation.service_request.new_lab_results_available'),
            'type' => 'success',
        ]);
    }

    private function loadRapidAccess()
    {
        $this->rapidAccess = RapidAccess::whereUserId(auth()->id())
            ->whereType('CLIENT')
            ->whereEncounterSectionId($this->section_id)
            ->get();

        if ($this->rapidAccess->count() == 0) {
            $this->rapidAccess = RapidAccess::whereType('MASTER')
                ->whereEncounterSectionId($this->section_id)
                ->get();
        }
    }

    public function delete($id)
    {
        $this->encounter->serviceRequests()->whereId($id)->delete();
        $this->loadSelectedLists();

        // Notificar al componente de accesos rápidos para que actualice el indicador
        $this->dispatch('rapid-access-list-updated-'.$this->encounter_id.'-'.$this->section_id);

        /*$this->dispatch('showToastrConsultation',
            type: 'success',
            message: 'Eliminado con exito.'
        );*/
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
            $existing = RapidAccess::whereUserId(auth()->id())
                ->whereType('CLIENT')
                ->whereEncounterSectionId($this->section_id)
                ->where('cpt_id', $cptId)
                ->first();

            if (! $existing) {
                RapidAccess::create([
                    'user_id' => auth()->id(),
                    'type' => 'CLIENT',
                    'encounter_section_id' => $this->section_id,
                    'cpt_id' => $cptId,
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
        return view('livewire.consultation.service-request');
    }
}
