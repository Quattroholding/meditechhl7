<?php

namespace App\Livewire\Consultation;

use App\Models\Encounter;
use Livewire\Attributes\On;
use Livewire\Component;

class FinishedButton extends Component
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
        return view('livewire.consultation.finished-button');
    }

    #[On('findFinishedButtonStatus')]
    public function findFinishedButtonStatus()
    {
        if (auth()->user()->hasRole('asistente medico')) {
            $this->enabled = $this->validateGeneralNote();
        } else {
            $this->enabled = $this->validateReason() && $this->validatePresentIllnesses() && $this->validateCondition() && $this->validateMedicationRequests() && $this->validateReferrals();
        }
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
            $this->messages[1] = '- Motivo de Consulta';
            $this->messageSections[1] = 1; // Section ID for "Motivo de Consulta"
            $this->messageTargets[1] = '#marker-id-1 textarea'; // Specific textarea selector
        }

        return $return;
    }

    public function validatePresentIllnesses()
    {

        $return = true;
        if (! $this->encounter->presentIllnesses) {
            $return = false;
            $this->messages[2] = '- Ubicación';
            $this->messages[3] = '- Gravedad';
            $this->messages[4] = '- Duración';
            $this->messages[5] = '- Momento';
            $this->messages[6] = '- Descripción';
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
        } else {
            if (empty($this->encounter->presentIllnesses->locations)) {
                $this->messages[2] = '- Ubicación';
                $this->messageSections[2] = 3;
                $this->messageTargets[2] = '#marker-id-3\\.0';
                $return = false;
            } else {
                unset($this->messages[2]);
                unset($this->messageSections[2]);
                unset($this->messageTargets[2]);
            }
            if (empty($this->encounter->presentIllnesses->severity)) {
                $this->messages[3] = '- Gravedad';
                $this->messageSections[3] = 3;
                $this->messageTargets[3] = '#marker-id-3\\.1';
                $return = false;
            } else {
                unset($this->messages[3]);
                unset($this->messageSections[3]);
                unset($this->messageTargets[3]);
            }
            if (empty($this->encounter->presentIllnesses->duration)) {
                $this->messages[4] = '- Duración';
                $this->messageSections[4] = 3;
                $this->messageTargets[4] = '#marker-id-3\\.2';
                $return = false;
            } else {
                unset($this->messages[4]);
                unset($this->messageSections[4]);
                unset($this->messageTargets[4]);
            }
            if (empty($this->encounter->presentIllnesses->timing)) {
                $this->messages[5] = '- Momento';
                $this->messageSections[5] = 3;
                $this->messageTargets[5] = '#marker-id-3\\.3';
                $return = false;
            } else {
                unset($this->messages[5]);
                unset($this->messageSections[5]);
                unset($this->messageTargets[5]);
            }
            if (empty($this->encounter->presentIllnesses->description)) {
                $this->messages[6] = '- Descripción';
                $this->messageSections[6] = 3;
                $this->messageTargets[6] = '#marker-id-3\\.5 textarea';
                $return = false;
            } else {
                unset($this->messages[6]);
                unset($this->messageSections[6]);
                unset($this->messageTargets[6]);
            }
        }

        return $return;
    }

    public function validateCondition()
    {

        if ($this->encounter->diagnoses->count() > 0) {
            unset($this->messages[7]);
            unset($this->messageSections[7]);
            unset($this->messageTargets[7]);

            return true;
        } else {
            $this->messages[7] = '- Al menos un diagnostico';
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

            if (empty($medication->route)) {
                $missingFields[] = 'vía';
            }

            if (empty($medication->frequency)) {
                $missingFields[] = 'frecuencia';
            }

            if (empty($medication->quantity)) {
                $missingFields[] = 'cantidad';
            }

            if (empty($medication->duration)) {
                $missingFields[] = 'duración';
            }

            if (! empty($missingFields)) {
                $medicationName = $medication->medicine->full_name ?? 'Medicamento';
                $incompleMedications[] = $medicationName.' ('.implode(', ', $missingFields).')';
            }
        }

        if (! empty($incompleMedications)) {
            $this->messages[8] = '- Medicamentos incompletos: '.implode(', ', $incompleMedications);
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
                $missingFields[] = 'motivo de referencia';
            }

            /*if (empty($referral->referred_to_id)) {
                $missingFields[] = 'especialista asignado';
            }*/

            if (! empty($missingFields)) {
                $specialtyName = $referral->speciality->name ?? 'Especialidad desconocida';
                $incompleteReferrals[] = $specialtyName.' ('.implode(', ', $missingFields).')';
            }
        }

        if (! empty($incompleteReferrals)) {
            $this->messages[9] = '- Referencias incompletas: '.implode(', ', $incompleteReferrals);
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
            $this->messages[1] = '- Nota General';
            $this->messageSections[1] = 13; // Section ID for "Nota General"
            $this->messageTargets[1] = '#marker-id-13 textarea'; // General note textarea
        }

        return $return;
    }
}
