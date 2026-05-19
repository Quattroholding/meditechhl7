<?php

namespace App\Livewire\Consultation;

use App\Models\Encounter;
use App\Models\MedicalSpeciality;
use App\Models\Practitioner;
use App\Models\Scopes\PractitionerScope;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Livewire\Component;

class Referral extends Component
{
    public $query = '';

    public $results = [];

    public $encounter_id;

    public $encounter;

    public $selectedLists = [];

    public $especialistas = [];

    public $selectedEspecialist = [];

    public $selectedReason = [];

    public $savedNota = [];

    public $savedEspecialist = [];

    public $savingNota = [];

    public $savingEspecialist = [];

    public $savedNotaTimer = [];

    public $savedEspecialistTimer = [];

    public $perPage = 50;

    public $totalResults = 0;

    public $hasMoreResults = false;

    // Nuevas propiedades para el directorio médico
    public $showMedicalDirectory = false;

    public $currentSpecialityId = null;

    public $currentSpecialityName = '';

    public $currentReferralId = null;

    public $practitioners = [];

    public $searchPractitioner = '';

    // Propiedades para especialista externo
    public $useExternalSpecialist = [];

    public $externalSpecialistName = [];

    public $externalSpecialistSpecialty = [];

    public $externalSpecialistLicense = [];

    public $externalSpecialistPhone = [];

    public $externalSpecialistClinic = [];

    public function mount()
    {
        $this->encounter = Encounter::find($this->encounter_id);

        $this->init();

    }

    public function init()
    {
        $this->selectedLists = $this->encounter->referrals()->get();

        foreach ($this->selectedLists as $selected) {
            $http = app()->environment('local') ? Http::withoutVerifying() : Http::asForm();
            $responseEspecialist = $http->get(url('api/practitioners'), ['dropdown' => true,  'speciality_id' => $selected->code, 'referral' => true]);
            $this->especialistas[$selected->code] = $responseEspecialist->json() ?? [];
            $this->selectedEspecialist[$selected->code] = $selected->referred_to_id;
            $this->selectedReason[$selected->id] = $selected->reason;

            // Cargar datos de especialista externo si existen
            $this->useExternalSpecialist[$selected->id] = ! empty($selected->external_specialist_name);
            $this->externalSpecialistName[$selected->id] = $selected->external_specialist_name ?? '';
            $this->externalSpecialistSpecialty[$selected->id] = $selected->external_specialist_specialty ?? '';
            $this->externalSpecialistLicense[$selected->id] = $selected->external_specialist_license ?? '';
            $this->externalSpecialistPhone[$selected->id] = $selected->external_specialist_phone ?? '';
            $this->externalSpecialistClinic[$selected->id] = $selected->external_specialist_clinic ?? '';
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

        $this->searchSpecialities();
    }

    public function loadMore()
    {
        $this->perPage += 50;
        $this->searchSpecialities();
    }

    private function searchSpecialities()
    {
        $http = app()->environment('local') ? Http::withoutVerifying() : Http::asForm();
        $response = $http->get(url('api/medical_speciality'), [
            'dropdown' => true,
            'q' => $this->query,
            'perPage' => $this->perPage,
        ]);

        if ($response->successful()) {
            $data = $response->json();
            $this->results = $data['data'] ?? [];
            $this->totalResults = $data['total'] ?? 0;
            $this->hasMoreResults = $data['hasMore'] ?? false;
        } else {
            $this->results = [];
            $this->totalResults = 0;
            $this->hasMoreResults = false;
        }
    }

    public function selectOption($option)
    {
        $key = 'speciality-search';

        try {
            sleep(1);

            $this->saved = false;
            $this->selectedOption = $option;
            $this->query = $option['name']; // Asigna el nombre seleccionado al input
            $this->results = []; // Limpia los resultados
            $referral = \App\Models\Referral::whereEncounterId($this->encounter->id)->whereCode($option)->first();
            $specialty = MedicalSpeciality::whereId($option)->first();
            if (! $referral) {
                $ref_created = $this->encounter->referrals()->create([
                    'fhir_id' => 'servicerequest-'.Str::uuid(),
                    'identifier' => 'REF-'.strtoupper(Str::random(7)),
                    'status' => 'active',
                    'intent' => 'order',
                    'priority' => 'asap',
                    'code' => $specialty->id,
                    'description' => "Referencia a especialista en $specialty->name",
                    'occurrence_date' => now(),
                    'supporting_info' => [
                        'speciality_id' => $specialty->id,
                        'speciality_name' => $specialty->name,
                    ],
                    // 'referred_to_id' => $referredTo->id, Aun no lo tengo pedeir en el siguiente paso
                    'patient_id' => $this->encounter->patient_id,
                    'practitioner_id' => $this->encounter->practitioner_id,
                ]);
                $http = app()->environment('local') ? Http::withoutVerifying() : Http::asForm();
                $responseEspecialist = $http->get(url('api/practitioners'), [
                    'dropdown' => true,
                    'speciality_id' => $specialty->id,
                ]);
                $this->especialistas[$specialty->id] = $responseEspecialist->json() ?? [];
                $this->selectedEspecialist[$specialty->id] = null;
                $this->selectedReason[$specialty->id] = null;
            }

            $this->dispatch('saved-'.$key);

            $this->query = '';

            $this->init();

            // Disparar evento para actualizar el estado del botón de finalizar
            $this->dispatch('findFinishedButtonStatus');

        } catch (\Exception $e) {
            $this->dispatch(
                'error-'.$key,
                $e->getMessage(),
            );
        }

    }

    public function resetSaved()
    {
        $this->saved = false;
        $this->savedTimer = null;
    }

    public function delete($id)
    {
        $this->encounter->referrals()->whereId($id)->delete();
        $this->selectedLists = $this->encounter->referrals()->get();

        // Disparar evento para actualizar el estado del botón de finalizar
        $this->dispatch('findFinishedButtonStatus');
    }

    public function updatedselectedReason($value, $code)
    {
        $this->setReason($code);
    }

    public function setReason($id)
    {
        $key = 'referral_'.$id;
        try {
            sleep(1);

            $referral = $this->encounter->referrals()->whereId($id)->first();
            $referral->reason = htmlspecialchars($this->selectedReason[$id]);
            $referral->save();

            sleep(1);

            $this->dispatch('saved-'.$key);

            // Disparar evento para actualizar el estado del botón de finalizar
            $this->dispatch('findFinishedButtonStatus');

        } catch (\Exception $e) {
            $this->dispatch(
                'error-'.$key,
                $e->getMessage(),
            );
        }
    }

    public function resetSavedNota($referral_id)
    {
        $this->savedNota[$referral_id] = false;
        $this->savedNotaTimer[$referral_id] = null;
    }

    public function setEspecialist($specialist, $referral_id)
    {
        $this->savingEspecialist[$referral_id] = true;
        $this->savedEspecialist[$referral_id] = false;

        $referral = $this->encounter->referrals()->whereId($referral_id)->first();
        $referral->referred_to_id = $specialist;
        $referral->save();

        sleep(1);

        $this->savingEspecialist[$referral_id] = false;
        $this->savedEspecialist[$referral_id] = true;
        $this->savedEspecialistTimer[$referral_id] = now()->timestamp;

        // Disparar evento para actualizar el estado del botón de finalizar
        $this->dispatch('findFinishedButtonStatus');
    }

    public function resetSavedEspecialist($referral_id)
    {
        $this->savedEspecialist[$referral_id] = false;
        $this->savedEspecialistTimer[$referral_id] = null;
    }

    // Nuevos métodos para el directorio médico
    public function openMedicalDirectory($specialityId, $referralId)
    {
        $this->currentSpecialityId = $specialityId;
        $this->currentReferralId = $referralId;
        $speciality = MedicalSpeciality::find($specialityId);
        $this->currentSpecialityName = $speciality ? $speciality->name : '';
        $this->searchPractitioner = '';
        $this->loadPractitioners();
        $this->showMedicalDirectory = true;
    }

    public function closeMedicalDirectory()
    {
        $this->showMedicalDirectory = false;
        $this->currentSpecialityId = null;
        $this->currentReferralId = null;
        $this->currentSpecialityName = '';
        $this->practitioners = [];
        $this->searchPractitioner = '';
    }

    public function loadPractitioners()
    {
        if (! $this->currentSpecialityId) {
            $this->practitioners = [];

            return;
        }

        $query = Practitioner::withoutGlobalScope(PractitionerScope::class)
            ->with(['qualifications', 'user.workingHours'])
            ->active()
            ->whereHas('qualifications', function ($q) {
                $q->where('medical_speciality_id', $this->currentSpecialityId);
            });

        if ($this->searchPractitioner) {
            $searchTerm = '%'.$this->searchPractitioner.'%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', $searchTerm)
                    ->orWhere('identifier', 'like', $searchTerm);
            });
        }

        $this->practitioners = $query->orderBy('name')->get();
    }

    public function updatedSearchPractitioner()
    {
        $this->loadPractitioners();
    }

    public function selectPractitioner($practitionerId)
    {
        if ($this->currentReferralId) {
            $referralId = $this->currentReferralId; // Guardar el ID antes de cerrar el modal

            $this->savingEspecialist[$referralId] = true;
            $this->savedEspecialist[$referralId] = false;

            $referral = $this->encounter->referrals()->whereId($referralId)->first();
            if ($referral) {
                $referral->referred_to_id = $practitionerId;
                $referral->save();
                $referral->refresh(); // Refrescar el modelo
                $this->selectedEspecialist[$referral->code] = $practitionerId;
            }

            // Recargar el encounter para obtener las relaciones actualizadas
            $this->encounter->refresh();

            // Recargar la lista de referidos con los datos actualizados
            $this->selectedLists = $this->encounter->referrals()->get();

            sleep(1);

            $this->savingEspecialist[$referralId] = false;
            $this->savedEspecialist[$referralId] = true;
            $this->savedEspecialistTimer[$referralId] = now()->timestamp;

            $this->closeMedicalDirectory();

            // Disparar evento para actualizar el estado del botón de finalizar
            $this->dispatch('findFinishedButtonStatus');
        }
    }

    /**
     * Alternar entre especialista interno y externo
     */
    public function toggleExternalSpecialist($referralId)
    {
        $this->useExternalSpecialist[$referralId] = ! ($this->useExternalSpecialist[$referralId] ?? false);

        // Si cambia a externo, limpiar el especialista interno
        if ($this->useExternalSpecialist[$referralId]) {
            $referral = $this->encounter->referrals()->whereId($referralId)->first();
            if ($referral) {
                $referral->referred_to_id = null;
                $referral->save();
                $this->selectedEspecialist[$referral->code] = null;
            }
        } else {
            // Si cambia a interno, limpiar los campos externos
            $this->saveExternalSpecialist($referralId, true);
        }
    }

    /**
     * Guardar información de especialista externo
     */
    public function saveExternalSpecialist($referralId, $clear = false)
    {
        try {
            $referral = $this->encounter->referrals()->whereId($referralId)->first();
            if ($referral) {
                if ($clear) {
                    // Limpiar datos externos
                    $referral->external_specialist_name = null;
                    $referral->external_specialist_specialty = null;
                    $referral->external_specialist_license = null;
                    $referral->external_specialist_phone = null;
                    $referral->external_specialist_clinic = null;
                } else {
                    // Guardar datos externos
                    $referral->external_specialist_name = $this->externalSpecialistName[$referralId] ?? null;
                    $referral->external_specialist_specialty = $this->externalSpecialistSpecialty[$referralId] ?? null;
                    $referral->external_specialist_license = $this->externalSpecialistLicense[$referralId] ?? null;
                    $referral->external_specialist_phone = $this->externalSpecialistPhone[$referralId] ?? null;
                    $referral->external_specialist_clinic = $this->externalSpecialistClinic[$referralId] ?? null;

                    // Limpiar referred_to_id si se usa especialista externo
                    $referral->referred_to_id = null;
                }

                $referral->save();

                $this->savingEspecialist[$referralId] = false;
                $this->savedEspecialist[$referralId] = true;
                $this->savedEspecialistTimer[$referralId] = now()->timestamp;

                // Disparar evento para actualizar el estado del botón de finalizar
                $this->dispatch('findFinishedButtonStatus');
            }
        } catch (\Exception $e) {
            \Log::error('Error al guardar especialista externo', [
                'error' => $e->getMessage(),
                'referral_id' => $referralId,
            ]);
        }
    }

    /**
     * Listener para auto-guardar campos de especialista externo
     */
    public function updatedExternalSpecialistName($value, $referralId)
    {
        $this->saveExternalSpecialist($referralId);
    }

    public function updatedExternalSpecialistSpecialty($value, $referralId)
    {
        $this->saveExternalSpecialist($referralId);
    }

    public function updatedExternalSpecialistLicense($value, $referralId)
    {
        $this->saveExternalSpecialist($referralId);
    }

    public function updatedExternalSpecialistPhone($value, $referralId)
    {
        $this->saveExternalSpecialist($referralId);
    }

    public function updatedExternalSpecialistClinic($value, $referralId)
    {
        $this->saveExternalSpecialist($referralId);
    }

    public function render()
    {
        return view('livewire.consultation.referral');
    }
}
