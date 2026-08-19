<?php

namespace App\Livewire\Consultation;

use App\Models\Appointment;
use App\Models\Encounter;
use App\Models\EncounterSection;
use App\Models\EncounterTemplate;
use App\Models\Patient;
use App\Models\Scopes\EncouterScope;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Create extends Component
{
    public $encounter_id;

    public $encounter;

    public $patient;

    public $appointment;

    public $encounter_sections;

    public $secciones;

    public $completedSections = [];

    public function mount()
    {
        $encounter_sections_user = EncounterTemplate::whereUserId(Auth::getUser()->id)->get();
        $user = User::find(Auth::getUser()->id);
        // Para preguntas de especialidad de Urología(42)
        $specialities = $user->practitioner?->specialties->pluck('id')->contains(42);

        if ($encounter_sections_user->count() > 0) {
            $this->encounter_sections = EncounterSection::whereIn('id', $encounter_sections_user->pluck('encounter_section_id'))->orderBy('order')->get();
        } elseif ($specialities && auth()->user()->hasRole('practitioner')) {
            $this->encounter_sections = EncounterSection::whereNull('deleted_at')->orderBy('order')->get();
        } else {
            $this->encounter_sections = EncounterSection::whereNull('category')->orderBy('order')->get();
        }

        if (app()->getLocale() === 'es') {
            $this->secciones = $this->encounter_sections->pluck('name_esp', 'id');

        } else {
            $this->secciones = $this->encounter_sections->pluck('name', 'id');
        }

        $this->encounter = Encounter::withoutGlobalScope(EncouterScope::class)->find($this->encounter_id);

        if ($this->encounter) {
            $this->patient = Patient::find($this->encounter->patient_id);
            $this->appointment = Appointment::find($this->encounter->appointment_id);

            // Verificar qué secciones ya están completadas
            $this->checkCompletedSections();
        }

    }

    /**
     * Verifica qué secciones ya tienen datos registrados
     */
    private function checkCompletedSections()
    {
        foreach ($this->encounter_sections as $section) {
            if ($this->isSectionCompleted($section)) {
                $this->completedSections[] = $section->id;
            }
        }
    }

    /**
     * Determina si una sección está completada basándose en su componente
     */
    private function isSectionCompleted($section): bool
    {
        $componentName = $section->livewire_component_name;

        // Para service-request, necesitamos verificar por tipo específico según la sección
        if ($componentName === 'consultation.service-request') {
            return $this->checkServiceRequestBySectionId($section->id);
        }

        // Mapeo de componentes a verificaciones de datos
        $completionChecks = [
            'consultation.reason' => fn () => ! empty($this->encounter->reason),
            'consultation.present-illness' => fn () => $this->validatePresentIllnessCompletion(),
            'consultation.diagnostics' => fn () => $this->encounter->diagnoses()->count() > 0,
            'consultation.vital-signs' => fn () => $this->encounter->vitalSigns()->count() > 0,
            'consultation.physical-exam' => fn () => $this->encounter->physicalExams()->count() > 0,
            'consultation.medication-requests' => fn () => $this->checkMedicationRequestsHasData(),
            'consultation.procedures' => fn () => $this->encounter->procedures()->count() > 0,
            'consultation.referral' => fn () => $this->checkReferralsHasData(),
            'consultation.general-note' => fn () => ! empty($this->encounter->general_note),
            'consultation.services' => fn () => $this->checkServicesHasData(),
        ];

        // Si existe una verificación para este componente, ejecutarla
        if (isset($completionChecks[$componentName])) {
            return $completionChecks[$componentName]();
        }

        return false;
    }

    /**
     * Verifica si hay service requests del tipo específico según el section ID
     */
    private function checkServiceRequestBySectionId($sectionId): bool
    {
        // Obtener la sección completa para verificar su api_path
        $section = EncounterSection::find($sectionId);

        if (! $section || ! isset($section->livewire_component_fields[0]['api_path'])) {
            // Si no hay api_path, verificar si hay algún service request
            return $this->encounter->serviceRequests()->count() > 0;
        }

        $apiPath = $section->livewire_component_fields[0]['api_path'];

        // Determinar el service_type basándose en el api_path
        $serviceType = null;
        if (str_contains($apiPath, 'laboratory')) {
            $serviceType = 'laboratory';
        } elseif (str_contains($apiPath, 'images') || str_contains($apiPath, 'imaging')) {
            $serviceType = 'images';
        } elseif (str_contains($apiPath, 'procedure')) {
            $serviceType = 'procedure';
        }

        if (! $serviceType) {
            // Si no se puede determinar el tipo, verificar si hay algún service request
            return $this->encounter->serviceRequests()->count() > 0;
        }

        // Verificar si hay service requests de este tipo específico
        return $this->encounter->serviceRequests()
            ->where('service_type', $serviceType)
            ->count() > 0;
    }

    /**
     * Verifica si hay medicamentos con datos completos
     * (solo marca como completado si hay medicamentos Y están completos)
     */
    private function checkMedicationRequestsHasData(): bool
    {
        $medicationRequests = $this->encounter->medicationRequests;

        // Si no hay medicamentos, NO está completado (es diferente a la validación para finalizar)
        if ($medicationRequests->count() === 0) {
            return false;
        }

        // Si hay medicamentos, verificar que todos tengan quantity y dosage_text
        foreach ($medicationRequests as $medication) {
            if (empty($medication->quantity) || empty($medication->dosage_text)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Verifica si hay referrals con datos completos
     * (solo marca como completado si hay referrals Y están completos)
     */
    private function checkReferralsHasData(): bool
    {
        $referrals = $this->encounter->referrals;

        // Si no hay referrals, NO está completado (es diferente a la validación para finalizar)
        if ($referrals->count() === 0) {
            return false;
        }

        // Si hay referrals, verificar que todos tengan reason
        foreach ($referrals as $referral) {
            if (empty($referral->reason)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Verifica si hay servicios facturables (ChargeItems)
     * (solo marca como completado si hay servicios agregados)
     */
    private function checkServicesHasData(): bool
    {
        // Verificar si hay ChargeItems asociados a este encounter
        return $this->encounter->chargeItems()->count() > 0;
    }

    /**
     * Valida si Present Illness está completo según el modo del usuario
     */
    private function validatePresentIllnessCompletion(): bool
    {
        if (! $this->encounter->presentIllnesses) {
            return false;
        }

        $mode = $this->getPresentIllnessMode();

        if ($mode === 'simplified') {
            // Solo requiere descripción en modo simplificado
            return ! empty($this->encounter->presentIllnesses->description);
        } else {
            // Modo completo requiere TODOS los campos
            $locations = $this->encounter->presentIllnesses->locations;
            $hasLocations = is_array($locations) && count($locations) > 0;

            return $hasLocations &&
                   ! empty($this->encounter->presentIllnesses->severity) &&
                   ! empty($this->encounter->presentIllnesses->duration) &&
                   ! empty($this->encounter->presentIllnesses->timing) &&
                   ! empty($this->encounter->presentIllnesses->description);
        }
    }

    /**
     * Obtiene el modo de Present Illness configurado para el usuario
     */
    private function getPresentIllnessMode(): string
    {
        $presentIllnessSection = EncounterSection::where('name', 'Present Illness')->first();

        if (! $presentIllnessSection) {
            return 'full';
        }

        $template = EncounterTemplate::where('user_id', auth()->id())
            ->where('encounter_section_id', $presentIllnessSection->id)
            ->first();

        if ($template && isset($template->encounter_section_fields['present_illness_mode'])) {
            return $template->encounter_section_fields['present_illness_mode'];
        }

        return 'full';
    }

    /**
     * Valida si los MedicationRequests están completos
     */
    private function validateMedicationRequestsCompletion(): bool
    {
        $medicationRequests = $this->encounter->medicationRequests;

        // Si no hay medicamentos, se considera completo
        if ($medicationRequests->count() === 0) {
            return true;
        }

        // Si hay medicamentos, verificar que todos tengan quantity y dosage_text
        foreach ($medicationRequests as $medication) {
            if (empty($medication->quantity) || empty($medication->dosage_text)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Valida si los Referrals están completos
     */
    private function validateReferralsCompletion(): bool
    {
        $referrals = $this->encounter->referrals;

        // Si no hay referrals, se considera completo
        if ($referrals->count() === 0) {
            return true;
        }

        // Si hay referrals, verificar que todos tengan reason
        foreach ($referrals as $referral) {
            if (empty($referral->reason)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Obtiene el estado actualizado de las secciones completadas
     */
    public function getCompletedSectionsStatus()
    {
        // Refrescar el encounter
        $this->encounter->refresh();

        // Verificar nuevamente qué secciones están completadas
        $this->completedSections = [];
        $this->checkCompletedSections();

        // Dispatch evento con las secciones completadas actualizadas
        $this->dispatch('sectionsStatusUpdated', completedSections: $this->completedSections);
    }

    public function render()
    {
        if (! auth()->user()->can('edit', $this->encounter)) {
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }

        if (env('ENCOUNTER_TEMPLATE_NEW')) {
            return view('livewire.consultation.create_new');
        }

        return view('livewire.consultation.create');
    }
}
