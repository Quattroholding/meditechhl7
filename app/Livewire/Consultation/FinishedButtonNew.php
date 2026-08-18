<?php

namespace App\Livewire\Consultation;

use App\Enums\SupplyRequestStatus;
use App\Models\Encounter;
use App\Models\EncounterSection;
use App\Models\EncounterTemplate;
use App\Models\InventoryReport;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class FinishedButtonNew extends Component
{
    public $encounter_id;

    public $encounter;

    public $enabled = false;

    public $messages = [];

    // Map message index to section ID for scroll functionality
    public $messageSections = [];

    // Map message index to specific element selector within the section
    public $messageTargets = [];

    public function mount()
    {

        $this->encounter = Encounter::find($this->encounter_id);

        $this->findFinishedButtonStatus();
    }

    public function render()
    {
        return view('livewire.consultation.finished-button-new');
    }

    #[On('findFinishedButtonStatus')]
    public function findFinishedButtonStatus()
    {
        if (auth()->user()->hasRole('asistente medico')) {
            $this->enabled = $this->validateGeneralNote();
        } else {
            $this->enabled = $this->validateReason() && $this->validatePresentIllnesses() && $this->validateCondition() && $this->validateMedicationRequests() && $this->validateSupplyRequests() && $this->validateReferrals();
        }

        // Emitir evento con los mensajes para mostrar en el footer central
        $this->dispatch('updateFooterMessages', [
            'messages' => $this->messages,
            'enabled' => $this->enabled
        ]);
    }

    public function validateReason()
    {

        $return = true;

        if (! empty($this->encounter->reason)) {
            unset($this->messages[1]);
            unset($this->messageSections[1]);
            unset($this->messageTargets[1]);
        } else {
            $return = false;
            $this->messages[1] = '- '.__('consultation.finished_button.reason_for_visit');
            $this->messageSections[1] = 1; // Section ID for "Motivo de Consulta"
            $this->messageTargets[1] = '#marker-id-1 textarea'; // Specific textarea selector
        }

        return $return;
    }

    public function validatePresentIllnesses()
    {
        // Verificar el modo de Present Illness del usuario
        $mode = $this->getPresentIllnessMode();

        $return = true;

        if (! $this->encounter->presentIllnesses) {
            $return = false;

            if ($mode === 'simplified') {
                // Solo validar descripción en modo simplificado
                $this->messages[6] = '- '.__('consultation.finished_button.description');
                $this->messageSections[6] = 3;
                $this->messageTargets[6] = '#marker-id-3\\.5 textarea';
            } else {
                // Validar todos los campos en modo completo
                $this->messages[2] = '- '.__('consultation.finished_button.location');
                $this->messages[3] = '- '.__('consultation.finished_button.severity');
                $this->messages[4] = '- '.__('consultation.finished_button.duration');
                $this->messages[5] = '- '.__('consultation.finished_button.timing');
                $this->messages[6] = '- '.__('consultation.finished_button.description');
                $this->messageSections[2] = 3;
                $this->messageSections[3] = 3;
                $this->messageSections[4] = 3;
                $this->messageSections[5] = 3;
                $this->messageSections[6] = 3;
                $this->messageTargets[2] = '#marker-id-3\\.0'; // Location selector
                $this->messageTargets[3] = '#marker-id-3\\.1'; // Severity selector
                $this->messageTargets[4] = '#marker-id-3\\.2'; // Duration selector
                $this->messageTargets[5] = '#marker-id-3\\.3'; // Timing selector
                $this->messageTargets[6] = '#marker-id-3\\.5 textarea'; // Description textarea
            }
        } else {
            if ($mode === 'simplified') {
                // Solo validar descripción en modo simplificado
                // Limpiar mensajes de otros campos
                unset($this->messages[2]);
                unset($this->messageSections[2]);
                unset($this->messageTargets[2]);
                unset($this->messages[3]);
                unset($this->messageSections[3]);
                unset($this->messageTargets[3]);
                unset($this->messages[4]);
                unset($this->messageSections[4]);
                unset($this->messageTargets[4]);
                unset($this->messages[5]);
                unset($this->messageSections[5]);
                unset($this->messageTargets[5]);

                if (empty($this->encounter->presentIllnesses->description)) {
                    $this->messages[6] = '- '.__('consultation.finished_button.description');
                    $this->messageSections[6] = 3;
                    $this->messageTargets[6] = '#marker-id-3\\.5 textarea';
                    $return = false;
                } else {
                    unset($this->messages[6]);
                    unset($this->messageSections[6]);
                    unset($this->messageTargets[6]);
                }
            } else {
                // Validar todos los campos en modo completo
                if (empty($this->encounter->presentIllnesses->locations)) {
                    $this->messages[2] = '- '.__('consultation.finished_button.location');
                    $this->messageSections[2] = 3;
                    $this->messageTargets[2] = '#marker-id-3\\.0';
                    $return = false;
                } else {
                    unset($this->messages[2]);
                    unset($this->messageSections[2]);
                    unset($this->messageTargets[2]);
                }
                if (empty($this->encounter->presentIllnesses->severity)) {
                    $this->messages[3] = '- '.__('consultation.finished_button.severity');
                    $this->messageSections[3] = 3;
                    $this->messageTargets[3] = '#marker-id-3\\.1';
                    $return = false;
                } else {
                    unset($this->messages[3]);
                    unset($this->messageSections[3]);
                    unset($this->messageTargets[3]);
                }
                if (empty($this->encounter->presentIllnesses->duration)) {
                    $this->messages[4] = '- '.__('consultation.finished_button.duration');
                    $this->messageSections[4] = 3;
                    $this->messageTargets[4] = '#marker-id-3\\.2';
                    $return = false;
                } else {
                    unset($this->messages[4]);
                    unset($this->messageSections[4]);
                    unset($this->messageTargets[4]);
                }
                if (empty($this->encounter->presentIllnesses->timing)) {
                    $this->messages[5] = '- '.__('consultation.finished_button.timing');
                    $this->messageSections[5] = 3;
                    $this->messageTargets[5] = '#marker-id-3\\.3';
                    $return = false;
                } else {
                    unset($this->messages[5]);
                    unset($this->messageSections[5]);
                    unset($this->messageTargets[5]);
                }
                if (empty($this->encounter->presentIllnesses->description)) {
                    $this->messages[6] = '- '.__('consultation.finished_button.description');
                    $this->messageSections[6] = 3;
                    $this->messageTargets[6] = '#marker-id-3\\.5 textarea';
                    $return = false;
                } else {
                    unset($this->messages[6]);
                    unset($this->messageSections[6]);
                    unset($this->messageTargets[6]);
                }
            }
        }

        return $return;
    }

    protected function getPresentIllnessMode(): string
    {
        $presentIllnessSection = EncounterSection::where('name', 'Present Illness')->first();

        if (! $presentIllnessSection) {
            return 'full';
        }

        $template = EncounterTemplate::where('user_id', Auth::id())
            ->where('encounter_section_id', $presentIllnessSection->id)
            ->first();

        if ($template && isset($template->encounter_section_fields['present_illness_mode'])) {
            return $template->encounter_section_fields['present_illness_mode'];
        }

        return 'full';
    }

    public function validateCondition()
    {

        if ($this->encounter->diagnoses->count() > 0) {
            unset($this->messages[7]);
            unset($this->messageSections[7]);
            unset($this->messageTargets[7]);

            return true;
        } else {
            $this->messages[7] = '- '.__('consultation.finished_button.at_least_one_diagnosis');
            $this->messageSections[7] = 5; // Section ID for "Diagnosticos"
            $this->messageTargets[7] = '.selector-field input[placeholder*="diagnostico"]'; // Diagnostic search input

            return false;
        }
    }

    public function validateMedicationRequests()
    {
        $medicationRequests = $this->encounter->medicationRequests();

        // Si no hay medicamentos agregados, la validación pasa
        if ($medicationRequests->count() === 0) {
            unset($this->messages[8]);

            return true;
        }

        $incompleMedications = [];

        foreach ($medicationRequests->get() as $medication) {
            $missingFields = [];

            // Solo validar campos requeridos: quantity e indications (dosage_text)
            if (empty($medication->quantity)) {
                $missingFields[] = __('consultation.finished_button.quantity');
            }

            if (empty($medication->dosage_text)) {
                $missingFields[] = __('consultation.finished_button.indications');
            }

            if (! empty($missingFields)) {
                $medicationName = $medication->medicine->full_name ?? __('consultation.finished_button.medication');
                $incompleMedications[] = $medicationName.' ('.implode(', ', $missingFields).')';
            }
        }

        if (! empty($incompleMedications)) {
            $this->messages[8] = '- '.__('consultation.finished_button.incomplete_medications').' '.implode(', ', $incompleMedications);
            $this->messageSections[8] = 11; // Section ID for "Medicamentos"
            $this->messageTargets[8] = '.medicine-table'; // Medication table

            return false;
        } else {
            unset($this->messages[8]);
            unset($this->messageSections[8]);
            unset($this->messageTargets[8]);

            return true;
        }
    }

    public function validateSupplyRequests()
    {
        $supplyRequests = $this->encounter->supplyRequests()
            ->where('status', SupplyRequestStatus::DRAFT)
            ->with(['inventoryItem', 'practitioner']);

        // Si no hay suministros agregados, la validación pasa
        if ($supplyRequests->count() === 0) {
            unset($this->messages[10]);
            unset($this->messageSections[10]);
            unset($this->messageTargets[10]);

            return true;
        }

        $issues = [];

        foreach ($supplyRequests->get() as $supply) {
            // Validar cantidad
            if ($supply->quantity <= 0) {
                $issues[] = "{$supply->inventoryItem->name} (".__('consultation.finished_button.invalid_quantity').')';

                continue;
            }

            // Validar stock disponible
            $practitioner = $supply->practitioner;
            $branchId = $supply->branch_id;

            $inventoryReport = InventoryReport::getForPractitioner(
                inventoryItemId: $supply->inventory_item_id,
                practitioner: $practitioner,
                branchId: $branchId
            );

            if (! $inventoryReport || $inventoryReport->quantity_available < $supply->quantity) {
                $available = $inventoryReport ? $inventoryReport->quantity_available : 0;
                $issues[] = "{$supply->inventoryItem->name} (".__('consultation.finished_button.insufficient_stock', ['available' => $available, 'requested' => $supply->quantity]).')';
            }
        }

        if (! empty($issues)) {
            $this->messages[10] = '- '.__('consultation.finished_button.supplies_with_issues').' '.implode(', ', $issues);
            $this->messageSections[10] = 17; // Section ID for "Suministros"
            $this->messageTargets[10] = '.medicine-table'; // Supply table

            return false;
        } else {
            unset($this->messages[10]);
            unset($this->messageSections[10]);
            unset($this->messageTargets[10]);

            return true;
        }
    }

    public function validateReferrals()
    {
        $referrals = $this->encounter->referrals();

        // Si no hay referrals agregados, la validación pasa
        if ($referrals->count() === 0) {
            unset($this->messages[9]);

            return true;
        }

        $incompleteReferrals = [];

        foreach ($referrals->get() as $referral) {
            $missingFields = [];

            if (empty($referral->reason)) {
                $missingFields[] = __('consultation.finished_button.referral_reason');
            }

            if (! empty($missingFields)) {
                $specialtyName = $referral->speciality->name ?? __('consultation.finished_button.unknown_specialty');
                $incompleteReferrals[] = $specialtyName.' ('.implode(', ', $missingFields).')';
            }
        }

        if (! empty($incompleteReferrals)) {
            $this->messages[9] = '- '.__('consultation.finished_button.incomplete_referrals').' '.implode(', ', $incompleteReferrals);
            $this->messageSections[9] = 10; // Section ID for "Referencia Especialista"
            $this->messageTargets[9] = '.medicine-table textarea'; // Referral note textarea (first one in table)

            return false;
        } else {
            unset($this->messages[9]);
            unset($this->messageSections[9]);
            unset($this->messageTargets[9]);

            return true;
        }
    }

    public function validateGeneralNote()
    {

        $return = true;

        if (! empty($this->encounter->general_note)) {
            unset($this->messages[1]);
            unset($this->messageSections[1]);
            unset($this->messageTargets[1]);
        } else {
            $return = false;
            $this->messages[1] = '- '.__('consultation.finished_button.general_note');
            $this->messageSections[1] = 13; // Section ID for "Nota General"
            $this->messageTargets[1] = '#marker-id-13 textarea'; // General note textarea
        }

        return $return;
    }
}
