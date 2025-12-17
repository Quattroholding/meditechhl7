<?php

namespace App\Livewire\Consultation;

use App\Models\Encounter;
use App\Models\MedicationRequest;
use App\Models\Medicine;
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

            return;
        }

        // Query medicines directly instead of using API to maintain authentication context
        $this->results = Medicine::selectRaw("id,concat(home_name,' de ',mgs,' ',mgs_type,' en ',type) as name")
            ->whereRaw("(ndc_code LIKE '%".$this->query."%' or home_name LIKE '%".$this->query."%' or generic_name LIKE '%".$this->query."%')")
            ->take(10)
            ->get()
            ->toArray();
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
            $medicine_request = MedicationRequest::whereEncounterId($this->encounter->id)->whereMedicationId($option)->first();
            $medicine = Medicine::whereId($option)->first();
            if (! $medicine_request) {
                $this->encounter->medicationRequests()->create([
                    'fhir_id' => 'medicationrequest-'.Str::uuid(),
                    'identifier' => 'RX-'.strtoupper(Str::random(7)),
                    'status' => 'active',
                    'intent' => 'order',
                    'medication_id' => $medicine->id,
                    'valid_from' => now(),
                    'valid_to' => now()->addDays(30),
                    'patient_id' => $this->encounter->patient_id,
                    'practitioner_id' => $this->encounter->practitioner_id,
                    'dosage_instruction',
                ]);

                $this->query = '';

                $this->dispatch('saved-'.$key);

            } else {
                $this->dispatch('error-'.$key,  '¡Medicamento ('.$option['name'].') ya esta agregado a la  consulta.!');
            }

            $this->getMedicationRequestsProperty();

            // Disparar evento para limpiar el scroll bloqueado
            $this->dispatch('option-selected');

            // Disparar evento para actualizar el estado del botón de finalizar
            $this->dispatch('findFinishedButtonStatus');

        } catch (\Exception $e) {
            $this->dispatch('error-'.$key,  'Error al guardar: '.$e->getMessage());
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

    public function updatedQuantitys($value, $code){
        $this->saveQuatity($code);
    }

    public function saveQuatity($id)
    {
        try{
            $medicationRequest = $this->encounter->medicationRequests()->whereId($id)->first();
            $medicationRequest->update(['quantity' => $this->quantitys[$id]]);

            $this->generateDosageInstruction($id);


            $this->dispatch('saved-quantity-'.$id);

            // Disparar evento para actualizar el estado del botón de finalizar
            $this->dispatch('findFinishedButtonStatus');

        } catch (\Exception $e) {
            $this->dispatch('error-'.$id,'Error al guardar : '.$e->getMessage());
        }

    }

    public function updatedFrecuencies($value, $code){
        $this->saveFrecuency($code);
    }

    public function saveFrecuency($id)
    {
        try{
            $medicationRequest = $this->encounter->medicationRequests()->whereId($id)->first();
            $medicationRequest->update(['frequency' => $this->frecuencies[$id]]);

            $this->generateDosageInstruction($id);


            $this->dispatch('saved-frecuency-'.$id);

            // Disparar evento para actualizar el estado del botón de finalizar
            $this->dispatch('findFinishedButtonStatus');

        } catch (\Exception $e) {
            $this->dispatch('error-'.$id,'Error al guardar : '.$e->getMessage());
        }

    }

    public function updatedDurations($value, $code){
        $this->saveDuration($code);
    }

    public function saveDuration($id)
    {
        try{
            $medicationRequest = $this->encounter->medicationRequests()->whereId($id)->first();
            $medicationRequest->update(['duration' => $this->durations[$id]]);
            $this->generateDosageInstruction($id);

            $this->dispatch('saved-duration-'.$id);

            // Disparar evento para actualizar el estado del botón de finalizar
            $this->dispatch('findFinishedButtonStatus');

        } catch (\Exception $e) {
            $this->dispatch('error-'.$id,'Error al guardar : '.$e->getMessage());
        }

    }

    public function updatedRoutes($value, $code){
        $this->saveRoute($code);
    }

    public function saveRoute($id)
    {
        try{
            $medicationRequest = $this->encounter->medicationRequests()->whereId($id)->first();
            $medicationRequest->update(['route' => $this->routes[$id]]);

            $this->generateDosageInstruction($id);


            $this->dispatch('saved-route-'.$id);

            // Disparar evento para actualizar el estado del botón de finalizar
            $this->dispatch('findFinishedButtonStatus');

        } catch (\Exception $e) {
            $this->dispatch('error-'.$id,'Error al guardar : '.$e->getMessage());
        }

    }


    protected function generateDosageInstruction($id)
    {
        $frequency = '';
        $route = '';
        $duration = '';
        $quantity = '';
        $key='dosage_text_'.$id;

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
            if ($requestMedicine->medicine) {
                $medicine_type = $requestMedicine->medicine->type;
            }

            $dosage_instructions =[
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

        }catch (\Exception $e) {
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
                    'medication_id' => $medication['medication_id'],
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
            ->with('medicine')
            ->get()
            ->take(10);

        if ($this->rapidAccess->count() == 0) {
            $this->rapidAccess = \App\Models\RapidAccess::whereType('MASTER')
                ->whereEncounterSectionId($this->section_id)
                ->with('medicine')
                ->get()
                ->take(10);
        }
    }

    public function clearSearch()
    {
        $this->query = '';
        $this->results = [];
    }

    public function addToRapidAccess($medicineId)
    {
        try {
            $existing = \App\Models\RapidAccess::whereUserId(auth()->id())
                ->whereType('CLIENT')
                ->whereEncounterSectionId($this->section_id)
                ->where('medicine_id', $medicineId)
                ->first();

            if (! $existing) {
                \App\Models\RapidAccess::create([
                    'user_id' => auth()->id(),
                    'type' => 'CLIENT',
                    'encounter_section_id' => $this->section_id,
                    'medicine_id' => $medicineId,
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
