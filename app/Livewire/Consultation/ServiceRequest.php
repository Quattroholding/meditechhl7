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

    public $type = 'procedure';

    public $selectedLists = [];

    public $rapidAccess = [];

    public $section_id;

    public $notes = [];

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

            return;
        }

        $response = Http::get(url('api/cpts/'.$this->type), [
            'dropdown' => true,
            'q' => $this->query,
        ]);

        $this->results = $response->json() ?? [];
    }

    #[\Livewire\Attributes\On('selectOption')]
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
                    message: 'Código CPT no encontrado'
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
                $this->dispatch('error-'.$key, '¡Cpt ('.$cpt->code.') ya está agregado a la consulta!');
                /*$this->dispatch('showToastrConsultation',
                    type: 'error',
                    message: '¡Servicio ('.$cpt->type.') ya está agregado a la consulta!'
                );*/
            }

            $this->query = '';

            $this->loadSelectedLists();
        } catch (\Exception $e) {
            $this->dispatch('error-'.$key,'Error al guardar: '.$e->getMessage());
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
                message: 'Código CPT no encontrado'
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
                message: 'Servicio agregado exitosamente'
            );

        } else {
            $this->dispatch('showToastrConsultation',
                type: 'error',
                message: '¡Servicio ya está agregado a la consulta!'
            );
        }

        $this->loadSelectedLists();
    }

    public function updatedNotes($value, $code)
    {
        $this->updateNote($code);
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
            $this->dispatch('error-'.$id,$e->getMessage());
        }
    }

    private function loadSelectedLists()
    {
        $this->selectedLists = $this->encounter->serviceRequests()
            ->where('service_type', $this->type)
            ->get();

        foreach ($this->selectedLists as $key) {
            $this->notes[$key->id] = $key->note;
        }
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
            $existing = \App\Models\RapidAccess::whereUserId(auth()->id())
                ->whereType('CLIENT')
                ->whereEncounterSectionId($this->section_id)
                ->where('cpt_id', $cptId)
                ->first();

            if (! $existing) {
                \App\Models\RapidAccess::create([
                    'user_id' => auth()->id(),
                    'type' => 'CLIENT',
                    'encounter_section_id' => $this->section_id,
                    'cpt_id' => $cptId,
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
        return view('livewire.consultation.service-request');
    }
}
